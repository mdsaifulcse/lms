<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
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
    public function index()
    {
        try{
            $vendorPayments=$this->model->with('user')->get();
            return $this->respondWithSuccess('Vendor payment list',VendorPaymentResourceCollection::make($vendorPayments),Response::HTTP_OK);
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $paymentNumber=$this->generateItemReceiveNo();
        $request['']=$paymentNumber;
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
            if ($totalPaidAmount>$totalLastDueAmount){
                $paymentStatus=ItemReceive::DUE;
            }else{
                $paymentStatus=ItemReceive::PAID;
            }


            $itemReceiveData->update([
                'paid_amount'=>$totalPaidAmount,
                'due_amount'=>$totalLastDueAmount,
                'payment_status'=>$paymentStatus
            ]);


            $vendorPayment=$this->model->where(['item_receive_id'=>$request->item_receive_id])->first();

            if (empty($vendorPayment)){ // Update vendor Payment ----
                $vendorPayment=$this->model->create([
                    'item_receive_id'=>$request->item_receive_id,
                    'vendor_id'=>$request->vendor_id,
                    'paid_amount'=>$request->paid_amount,
                    'total_last_due_amount'=>$totalLastDueAmount,
                ]);
            }else{ // Create Vendor Payment -----
                $vendorPayment->update([
                    'paid_amount'=>$request->paid_amount,
                    'total_last_due_amount'=>$totalLastDueAmount,]);
            }


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
            'vendor_id'  => "nullable|exists:vendors,id",
            'paid_amount'=>"required|numeric|digits_between:1,9",
        ];
    }

    public function generateItemReceiveNo(){

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
            $vendorPayment=$this->model->with('user')->where(['id'=>$id])->first();
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
            $userMembership=$this->model->with('user')->where(['id'=>$id])->first();
            if (!$userMembership){
                return $this->respondWithError('No data found',[],Response::HTTP_NOT_FOUND);
            }
            $userMembership->delete();

            return $this->respondWithSuccess('User plan has been Deleted',[],Response::HTTP_OK);

        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
