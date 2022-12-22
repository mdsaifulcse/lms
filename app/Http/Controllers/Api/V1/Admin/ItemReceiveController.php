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

            // Store Item Order Details -----------
            if (count($request->item_id)>0){
                $this->storeItemReceiveDetails($request,$itemReceive->id);
            }

            $this->updateInventoryStock($request,$itemReceive->id);

            DB::commit();
            return $this->respondWithSuccess('Item Receive Info has been created successful',new  ItemReceiveResource($itemReceive),Response::HTTP_OK);

        }catch(Exception $e){
            DB::rollback();
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function storeValidationRules($request){
        return [
            'item_order_id'  => "required|exists:item_orders,id",
            'vendor_id'  => "nullable|exists:vendors,id",
            'qty'  => "required|numeric|digits_between:1,4",
            'paid_amount'  => "numeric|digits_between:1,6",
            'comments'  => "nullable|max:200",

            "item_id"   => "required|array|min:1",
            'item_id.*' => "exists:items,id",

            "item_qty"   => "required|array|min:1",
            'item_qty.*' => "digits_between:1,4",

            'invoice_no' => 'nullable|max:50',
            'invoice_photo' => 'image|mimes:jpeg,jpg,png,gif|nullable|max:8048'
        ];
    }

    public function storeItemReceiveDetails($request,$itemReceiveId){
        $qty=0;
        $amount=0;
        foreach ($request->item_id as $key=>$itemId){
            $itemReceiveDetail[]=[
                'item_receive_id'=>$itemReceiveId,
                'item_id'=>$request->item_id[$key],
                'item_qty'=>$request->item_qty[$key]?$request->item_qty[$key]:0,
            ];

            $qty+=$request->item_qty[$key]?$request->item_qty[$key]:0;

            $itemOrderDetail=ItemOrderDetail::where(['item_order_id'=>$request->item_order_id,'item_id'=>$request->item_id[$key]])
                ->first();

            $amount+=$itemOrderDetail->item_price?$itemOrderDetail->item_price:0;
        }
        ItemReceiveDetail::insert($itemReceiveDetail);

        // --------- Calculate Payment Status ---------
        $dueAmount=$amount-$request->paid_amount;
        $paymentStatus=ItemReceive::UNPAID;
        if ($dueAmount==0){
            $paymentStatus=ItemReceive::PAID;
        }elseif ($request->paid_amount>0){
            $paymentStatus=ItemReceive::DUE;
        }

        // --------- update ItemReceive ---------------
        $itemReceive=ItemReceive::find($itemReceiveId);
        $itemReceive->update(['qty'=>$qty,
            'payable_amount'=>$amount,
            'payment_status'=>$paymentStatus,
        ]);
    }

    public function updateInventoryStock($request,$itemReceiveId){

        foreach ($request->item_id as $key=>$itemId){

            $itemInventoryStock=ItemInventoryStock::where(['id'=>$itemId])->first();
            $qty=$request->item_qty[$key]?$request->item_qty[$key]:0;

            if ($itemInventoryStock){
                $qty+=$request->item_qty[$key]?$request->item_qty[$key]:0;
                $itemInventoryStock->update(['qty'=>$request->item_qty[$key]]);
            }else{
                ItemInventoryStock::create(['item_id'=>$itemId,'qty'=>$qty]);
            }

            $request->item_id[$key];
            $request->item_qty[$key]?$request->item_qty[$key]:0;

        }
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
