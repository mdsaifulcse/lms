<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemReceiveResource;
use App\Http\Resources\VendorPaymentResource;
use App\Http\Resources\VendorPaymentResourceCollection;
use App\Http\Traits\ApiResponseTrait;
use App\Models\ItemReceive;
use App\Models\VendorPayment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use DB,Hash,Validator,Auth;

class VendorPaymentController extends Controller
{
    use ApiResponseTrait;

    public function __construct(VendorPayment $vendorPayment)
    {
        $this->model=$vendorPayment;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        try{
            $vendorPayments=$this->model->with('vendor','itemReceive');
            if ($request->has('item_receive_id')&& $request->item_receive_id!='null'){
                $vendorPayments=$vendorPayments->where(['item_receive_id'=>$request->item_receive_id]);
            }
            $vendorPayments=$vendorPayments->latest()->get();

            return $this->respondWithSuccess('Vendor payment list',VendorPaymentResourceCollection::make($vendorPayments),Response::HTTP_OK);
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function vendorPaymentsByReceivedId($receivedId)
    {
        $itemReceive=ItemReceive::with('itemReceivePayments')->find($receivedId);
        try{
            if ($itemReceive){
                return $this->respondWithSuccess('Vendor payment info',new  ItemReceiveResource($itemReceive),Response::HTTP_OK);
            }else{
                return $this->respondWithError('Vendor payment info data found',[],Response::HTTP_NOT_FOUND);
            }
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function payableReceivedOrderByReceivedId($receivedId)
    {
        try{
            $itemReceived=ItemReceive::where(['id'=>$receivedId])->whereNotIn('payment_status',[ItemReceive::PAID])->first();
            if ($itemReceived){
                return $this->respondWithSuccess('Payable Order Receive Info',new  ItemReceiveResource($itemReceived),Response::HTTP_OK);
            }else{
                return $this->respondWithError('No data found',[],Response::HTTP_NOT_FOUND);
            }
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $paymentNumber=$this->generateVendorPaymentNo();
        $request['vendor_payment_no']=$paymentNumber;
        $rules=$this->storeValidationRules($request);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondWithValidation('Validation Fail',$validator->errors()->first(),Response::HTTP_BAD_REQUEST);
        }

        DB::beginTransaction();
        try{

            $itemReceiveData=ItemReceive::find($request->item_receive_id);

            // calculate TOTAL_LAST_DUE_AMOUNT and payment STATUS
            $totalPaidAmount=$itemReceiveData->paid_amount+$request->paid_amount;
            $totalLastDueAmount=$itemReceiveData->payable_amount-$totalPaidAmount;


            $paymentStatus=$itemReceiveData->payment_status;
            if ($totalLastDueAmount>0){

                $paymentStatus=ItemReceive::DUE;
            }else{

                $paymentStatus=ItemReceive::PAID;
            }


            $itemReceiveData->update([
                'paid_amount'=>$totalPaidAmount,
                'due_amount'=>$totalLastDueAmount,
                'payment_status'=>$paymentStatus
            ]);

            // Generate Received Date ----
            if ($request->payment_date){
                $paymentDate=date('Y-m-d',strtotime($request->payment_date));
            }else{
                $paymentDate=date('Y-m-d');
            }
            // Vendor Payment ----------------------------------------
            $vendorPayment=$this->model->create([
                'vendor_payment_no'=>$request->vendor_payment_no,
                'item_receive_id'=>$request->item_receive_id,
                'vendor_id'=>$itemReceiveData->vendor_id,
                'paid_amount'=>$request->paid_amount,
                'total_last_due_amount'=>$totalLastDueAmount,
                'payment_date'=>$paymentDate,
                'comments'=>$request->comments,
            ]);

//            $vendorPayment=$this->model->where(['item_receive_id'=>$request->item_receive_id])->first();
//
//            if (empty($vendorPayment)){ // Update vendor Payment ----
//                $vendorPayment=$this->model->create([
//                    'vendor_payment_no'=>$request->vendor_payment_no,
//                    'item_receive_id'=>$request->item_receive_id,
//                    'vendor_id'=>$itemReceiveData->vendor_id,
//                    'paid_amount'=>$request->paid_amount,
//                    'total_last_due_amount'=>$totalLastDueAmount,
//                ]);
//            }
//            else{ // Create Vendor Payment -----
//                $vendorPayment->update([
//                    'paid_amount'=>$request->paid_amount,
//                    'total_last_due_amount'=>$totalLastDueAmount,]);
//            }


            // To get update data --------------------------------
            $vendorPayment=$this->model->find($vendorPayment->id);
            DB::commit();
            return $this->respondWithSuccess('Vendor payment has been created successful',new  VendorPaymentResource($vendorPayment),Response::HTTP_OK);

        }catch(Exception $e){
            DB::rollback();
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeValidationRules($request){
        return [
            'vendor_payment_no' => 'unique:vendor_payments,vendor_payment_no,NULL,id,deleted_at,NULL',
            'item_receive_id'  => "required|exists:item_receives,id",
            'paid_amount'=>"required|numeric|digits_between:1,9",
            'comments'=>"nullable|max:145",
        ];
    }

    public function generateVendorPaymentNo(){

        $lastPaymentNo=VendorPayment::max('vendor_payment_no');
        $lastPaymentNo=$lastPaymentNo?$lastPaymentNo+1:1;

        $paymentNoLength= VendorPayment::PAYMENTNOLENGTH;

        return str_pad($lastPaymentNo,$paymentNoLength,"0",false);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $vendorPayment=$this->model->with('vendor')->where(['id'=>$id])->first();
            if ($vendorPayment){
                return $this->respondWithSuccess('Vendor payment Info',new  VendorPaymentResource($vendorPayment),Response::HTTP_OK);
            }else{
                return $this->respondWithError('No data found',[],Response::HTTP_NOT_FOUND);
            }
        }catch(Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $vendorPayment=$this->model->with('itemReceive')->where(['id'=>$id])->first();
            if (!$vendorPayment){
                return $this->respondWithError('No data found',[],Response::HTTP_NOT_FOUND);
            }

            // ------- minus(-) paid_amount from Paid Amount, add(+) to due_amount and change payment_status on Item Receive table

            $itemReceive=$vendorPayment->itemReceive;

            // Paid After minus(-) paid amount -------
            $totalPaidAmount=$itemReceive->paid_amount-$vendorPayment->paid_amount;
            // Due amount After add(+) paid amount ----
            $totalDueAmount=$itemReceive->due_amount+$vendorPayment->paid_amount;

            // calculate payment status
            $paymentStatus=$itemReceive->payment_status;
            if ($totalDueAmount==0){
                $paymentStatus=ItemReceive::PAID;
            }else{
                $paymentStatus=ItemReceive::DUE;
            }

            $itemReceive->update([
                'paid_amount'=>$totalPaidAmount,
                'due_amount'=>$totalDueAmount,
                'payment_status'=>$paymentStatus
            ]);

            $vendorPayment->delete();

            return $this->respondWithSuccess('Vendor Payment has been Deleted',[],Response::HTTP_OK);

        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
