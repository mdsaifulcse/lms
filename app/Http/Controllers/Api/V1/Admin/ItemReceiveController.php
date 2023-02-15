<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemReceiveDetailsResource;
use App\Http\Resources\ItemReceiveResource;
use App\Http\Resources\ItemReceiveResourceCollection;
use App\Http\Traits\ApiResponseTrait;
use App\Models\ItemInventoryStock;
use App\Models\ItemOrder;
use App\Models\ItemOrderDetail;
use App\Models\ItemReceive;
use App\Models\ItemReceiveDetail;
use App\Models\VendorPayment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use DB,Hash,Validator,Auth;
class ItemReceiveController extends Controller
{

    use ApiResponseTrait;

    public function __construct(ItemReceive $itemReceive)
    {
        $this->model=$itemReceive;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $itemReceive=$this->model->with('itemReceiveDetails')->latest()->get();
            return $this->respondWithSuccess('All Item Receive list',ItemReceiveResourceCollection::make($itemReceive),Response::HTTP_OK);
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
        // 1. Store to item_receives -----------
        // 2. Store Item Order Details (item_receive_details) and calculate payment status, payable amount  ----
        // 3. Update item_inventory_stocks --------------------
        // 4. Vendor payment Create ------
        $receiveNo=$this->generateItemReceiveNo();
        $request['receive_no']=$receiveNo;
        $rules=$this->storeValidationRules($request);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondWithValidation('Validation Fail',$validator->errors()->first(),Response::HTTP_BAD_REQUEST);
        }

        DB::beginTransaction();
        try{
            $input=$request->all();

            // upload invoice photo --------------------
            if ($request->hasFile('invoice_photo')){
                $input['photo']=\MyHelper::photoUpload($request->file('invoice_photo'),'images/invoice-photo',150);
            }
            $itemReceive=$this->model->create($input);

            // Store Item Order Details and calculate payment status, payable amount-----------
            if (count($request->item_id)>0){
                $this->storeItemReceiveDetails($request,$itemReceive->id);
            }

            $this->updateInventoryStock($request,$itemReceive->id);


            // To get update data --------------------------------
            $itemReceive=$this->model->find($itemReceive->id);
            DB::commit();
            return $this->respondWithSuccess('Item Receive Info has been created successful',new  ItemReceiveResource($itemReceive),Response::HTTP_OK);

        }catch(Exception $e){
            DB::rollback();
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function storeValidationRules($request){
        return [
            'receive_no' => 'unique:item_receives,receive_no,NULL,id,deleted_at,NULL',
            'item_order_id'  => "required|exists:item_orders,id",
            'vendor_id'  => "required|exists:vendors,id",
            'qty'  => "required|numeric|digits_between:1,4",
            'paid_amount'  => "numeric|digits_between:1,7",
            'comments'  => "nullable|max:200",

            "item_id"   => "required|array|min:1",
            'item_id.*' => "exists:items,id",

            "item_qty"   => "required|array|min:1",
            'item_qty.*' => "numeric|digits_between:1,4",

            'invoice_no' => 'nullable|max:50',
            'invoice_photo' => 'image|mimes:jpeg,jpg,png,gif|nullable|max:8048'
        ];
    }

    public function storeItemReceiveDetails($request,$itemReceiveId){
        $qty=0;
        $totalAmount=0;
        foreach ($request->item_id as $key=>$itemId){
            $itemReceiveDetail[]=[
                'item_receive_id'=>$itemReceiveId,
                'item_id'=>$request->item_id[$key],
                'item_qty'=>$request->item_qty[$key]?$request->item_qty[$key]:0,
            ];

            $qty+=$request->item_qty[$key]?$request->item_qty[$key]:0;

            // get Item price when order placed ---------
            $itemOrderDetail=ItemOrderDetail::select('item_price')->where(['item_order_id'=>$request->item_order_id,'item_id'=>$request->item_id[$key]])
                ->first();

            // Calculate Item Total Amount ----
            $totalAmount+=$itemOrderDetail->item_price?$itemOrderDetail->item_price:0;
        }
        ItemReceiveDetail::insert($itemReceiveDetail);

        // --------- Calculate Payment Status, due Amount ---------
        $dueAmount=$totalAmount-$request->paid_amount;
        $paymentStatus=ItemReceive::UNPAID;

        if ($dueAmount==0){
            $paymentStatus=ItemReceive::PAID;
        }elseif ($request->paid_amount!=$totalAmount){
            $paymentStatus=ItemReceive::DUE;
        }

        // --------- update ItemReceive ---------------
        $itemReceive=ItemReceive::find($itemReceiveId);
        $itemReceive->update([
            'qty'=>$qty,
            'payable_amount'=>$totalAmount,
            'due_amount'=>$dueAmount,
            'payment_status'=>$paymentStatus,
        ]);

        //----- Create Vendor payment -------------------------
        VendorPayment::create([
            'vendor_payment_no'=>$this->generateVendorPaymentNo(),
            'vendor_id'=>$request->vendor_id,
            'item_receive_id'=>$itemReceiveId,
            'paid_amount'=>$request->paid_amount,
            'total_last_due_amount'=>$dueAmount,
        ]);

    }

    public function updateInventoryStock($request,$itemReceiveId){

        foreach ($request->item_id as $key=>$itemId){

            $itemInventoryStock=ItemInventoryStock::where(['id'=>$itemId])->first();
            $qty=$request->item_qty[$key]?$request->item_qty[$key]:0;

            // Update Item Qty with previous qty
            if ($itemInventoryStock){
                $qty+=$request->item_qty[$key]?$request->item_qty[$key]:0;
                $itemInventoryStock->update(['qty'=>$qty]); //$request->item_qty[$key]
            }else{
                // Create new inventoryStock -------
                ItemInventoryStock::create(['item_id'=>$itemId,'qty'=>$qty]);
            }

            $request->item_id[$key];
            $request->item_qty[$key]?$request->item_qty[$key]:0;

        }
    }
    public function generateItemReceiveNo(){

        $lastReceiveNo=ItemReceive::max('receive_no');

        $lastReceiveNo=$lastReceiveNo?$lastReceiveNo+1:1;


        $receiveNoLength= ItemReceive::RECEIVENOLENGTH;

        return str_pad($lastReceiveNo,$receiveNoLength,"0",false);
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
     * @param  \App\Models\ItemReceive  $itemReceive
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
         $itemReceive=$this->model->with('itemReceiveDetails')->find($id);
        try{
            if ($itemReceive){
                return $this->respondWithSuccess('Item Receive Info',new  ItemReceiveResource($itemReceive),Response::HTTP_OK);
            }else{
                return $this->respondWithError('No item receive data found',[],Response::HTTP_NOT_FOUND);
            }
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ItemReceive  $itemReceive
     * @return \Illuminate\Http\Response
     */
    public function edit(ItemReceive $itemReceive)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ItemReceive  $itemReceive
     * @return \Illuminate\Http\Response
     */


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ItemReceive  $itemReceive
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try{
            $itemReceive=$this->model->find($id);
            if (!$itemReceive){
                return $this->respondWithError('No data found',[],Response::HTTP_NOT_FOUND);
            }

           $itemReceive->load('itemReceiveDetails');

            $itemReceiveDetails=$itemReceive->itemReceiveDetails;

            if (count($itemReceiveDetails)>0){
                foreach ($itemReceiveDetails as $itemReceiveDetail){
                    $itemStock=ItemInventoryStock::where(['item_id'=>$itemReceiveDetail->item_id])->first();

                    // Reduce Qty
                    $afterReduceQty=$itemStock->qty-$itemReceiveDetail->item_qty;
                    $itemStock->update(['qty'=>$afterReduceQty]);
                }
            }

            $itemReceive->itemReceiveDetails()->delete();
            $itemReceive->delete();

            DB::commit();
            return $this->respondWithSuccess('Item Receive has been Deleted',[],Response::HTTP_OK);

        }catch(Exception $e){
            DB::rollback();
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
