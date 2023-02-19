<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemOrderResource;
use App\Http\Resources\ItemOrderResourceCollection;
use App\Http\Traits\ApiResponseTrait;
use App\Models\ItemOrder;
use App\Models\ItemOrderDetail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use DB,Hash,Validator,Auth;

class ItemOrderController extends Controller
{
    use ApiResponseTrait;

    public function __construct(ItemOrder $itemOrder)
    {
        $this->model=$itemOrder;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $itemOrders=$this->model->with('vendor','itemOrderDetails')->latest();
            if ($request->order_status=='unreceived'){
                $itemOrders=$itemOrders->where(['order_status'=>0]);
            }elseif($request->order_status=='received'){
                $itemOrders=$itemOrders->where(['order_status'=>1]);
            }
            $itemOrders=$itemOrders->get();
            return $this->respondWithSuccess('All Item list',ItemOrderResourceCollection::make($itemOrders),Response::HTTP_OK);
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        $orderNo=$this->generateOrderInvoiceNo();
        $request['order_no']=$orderNo;
        $rules=$this->storeValidationRules($request);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondWithValidation('Validation Fail',$validator->errors()->first(),Response::HTTP_BAD_REQUEST);
        }

        DB::beginTransaction();
        try{
            $input=$request->all();
            if ($request->tentative_date){
                $input['tentative_date']=date('Y-m-d',strtotime($request->tentative_date));
            }

            $itemOrder=$this->model->create($input);

            // Store Item Order Details -----------
            if (count($request->item_id)>0){
                $qtyAndAmount=$this->storeItemOrderDetails($request,$itemOrder->id);

                // update qty and amount on ItemOrder Table
                $itemOrder->update([
                    'qty'=>$qtyAndAmount['qty'],
                    'amount'=>$qtyAndAmount['amount'],
                    'discount'=>$request->discount,
                    'total'=>$qtyAndAmount['amount']-$request->discount // After discount from amount
                ]);
            }

            DB::commit();
            return $this->respondWithSuccess('Order Info has been created successful',new  ItemOrderResource($itemOrder),Response::HTTP_OK);

        }catch(Exception $e){
            DB::rollback();
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeValidationRules($request){
        return [
            'order_no' => 'unique:item_orders,order_no,NULL,id,deleted_at,NULL',
            'qty'  => "numeric|digits_between:1,4",
            'amount'  => "required|numeric|digits_between:1,999999",
            'discount'  => "required|numeric|digits_between:1,999999|max:$request->amount",
            'tentative_date'  => "nullable|date",
            'vendor_id'  => "nullable|exists:vendors,id",
            'status'  => "required|in:0,1",
            "item_id"   => "required|array|min:1",
            'item_id.*' => "exists:items,id",

            "item_qty"   => "required|array|min:1",
            'item_qty.*' => "digits_between:1,4",

            "item_price"   => "required|array|min:1",
            'item_price.*' => "digits_between:1,6",
            //'photo' => 'image|mimes:jpeg,jpg,png,gif|nullable|max:8048'
        ];
    }

    public function storeItemOrderDetails($request,$itemOrderId){

        $qty=0;
        $amount=0;
        foreach ($request->item_id as $key=>$itemId){

            $itemQty=$request->item_qty[$key]?$request->item_qty[$key]:0;
            $itemPrice=$request->item_price[$key]?$request->item_price[$key]:0;
            $itemOrderDetails[]=[
                'item_order_id'=>$itemOrderId,
                'item_id'=>$request->item_id[$key],
                'item_qty'=>$itemQty,
                'item_price'=>$itemPrice,
                ];
            $qty+=$request->item_qty[$key]?$request->item_qty[$key]:0;
            $amount+=$itemPrice*$itemQty;
        }
        ItemOrderDetail::insert($itemOrderDetails);
        return ['qty'=>$qty,'amount'=>$amount];
    }

    public function generateOrderInvoiceNo(){

        $lastOrderNo=ItemOrder::max('order_no');
        if (empty($lastOrderNo)){
            $lastOrderNo=1;
        }else{
            $lastOrderNo+=1;
        }

        $invoiceLength= ItemOrder::INVOICENOLENGTH;

        return str_pad($lastOrderNo,$invoiceLength,"0",false);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ItemOrder  $itemOrder
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $itemOrder=$this->model->with('itemOrderDetails','itemOrderDetails.item')->where('id',$id)->first();
            if ($itemOrder){
                return $this->respondWithSuccess('Item Order Info',new  ItemOrderResource($itemOrder),Response::HTTP_OK);
            }else{
                return $this->respondWithError('No data found',[],Response::HTTP_NOT_FOUND);
            }
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ItemOrder  $itemOrder
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ItemOrder  $itemOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ItemOrder $itemOrder)
    {
        $rules=$this->updateValidationRules($request,$itemOrder->id);

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidation('Validation Fail',$validator->errors()->first(),Response::HTTP_BAD_REQUEST);
        }


        DB::beginTransaction();
        try{
            $itemOrder->update($request->except(['order_no']));

            // Update Item Order Details -----------
            if (count($request->item_id)>0){
                $qtyAndAmount=$this->updateItemOrderDetails($request,$itemOrder->id);

                // update qty and amount on ItemOrder Table
                $itemOrder->update([
                    'qty'=>$qtyAndAmount['qty'],
                    'amount'=>$qtyAndAmount['amount'],
                    'discount'=>$request->discount,
                    'total'=>$qtyAndAmount['amount']-$request->discount // After discount from amount
                ]);
            }

            DB::commit();
            return $this->respondWithSuccess('Item Order Info has been updated successful',new  ItemOrderResource($itemOrder),Response::HTTP_OK);
        }catch(Exception $e){
            DB::rollback();
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateItemOrderDetails($request,$itemOrderId){

        ItemOrderDetail::where('item_order_id',$itemOrderId)->delete();

        $qty=0;
        $amount=0;
        foreach ($request->item_id as $key=>$itemId){
            $itemOrderDetails[]=[
                'item_order_id'=>$itemOrderId,
                'item_id'=>$request->item_id[$key],
                'item_qty'=>$request->item_qty[$key]?$request->item_qty[$key]:0,
                'item_price'=>$request->item_price[$key]?$request->item_price[$key]:0,
            ];
            $qty+=$request->item_qty[$key]?$request->item_qty[$key]:0;
            $amount+=$request->item_price[$key]?$request->item_price[$key]*$request->item_qty[$key]:0;
        }
        ItemOrderDetail::insert($itemOrderDetails);
        return ['qty'=>$qty,'amount'=>$amount];
    }

    public function updateValidationRules($request,$itemOrderId){
        return [
            'order_no' => "required|unique:item_orders,order_no,$itemOrderId,id,deleted_at,NULL",
            'qty'  => "numeric|digits_between:1,4",
            'amount'  => "required|numeric|digits_between:1,999999",
            'tentative_date'  => "nullable",
            'vendor_id'  => "nullable|exists:vendors,id",
            'status'  => "required|in:0,1",
            "item_id"   => "required|array|min:1",
            'item_id.*' => "exists:items,id",

            "item_qty"   => "required|array|min:1",
            'item_qty.*' => "digits_between:1,4",

            "item_price"   => "required|array|min:1",
            'item_price.*' => "digits_between:1,6",
            //'photo' => 'image|mimes:jpeg,jpg,png,gif|nullable|max:8048'
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ItemOrder  $itemOrder
     * @return \Illuminate\Http\Response
     */

    public function destroy(ItemOrder $itemOrder)
    {
        try{
             $itemOrder->load('orderReceived');
            if (!$itemOrder){
                return $this->respondWithError('No data found',[],Response::HTTP_NOT_FOUND);
            }
            if (count($itemOrder->orderReceived)>0){
                return $this->respondWithError('You can not delete,cause order of already has been Received',[],Response::HTTP_BAD_REQUEST);
            }

            $itemOrder->load('itemOrderDetails');
            ;
            $itemOrder->itemOrderDetails()->delete();
            $itemOrder->delete();

            return $this->respondWithSuccess('Item Order has been Deleted',[],Response::HTTP_OK);

        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
