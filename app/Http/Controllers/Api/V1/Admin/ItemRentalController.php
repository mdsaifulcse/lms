<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemRentalResource;
use App\Http\Resources\ItemRentalResourceCollection;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Item;
use App\Models\ItemInventoryStock;
use App\Models\ItemRental;
use App\Models\ItemRentalDetail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use DB,Hash,Validator,Auth;

class ItemRentalController extends Controller
{
    use ApiResponseTrait;

    public function __construct(ItemRental $itemRental)
    {
        $this->model=$itemRental;
    }

    public function index()
    {
        try{
            $itemRentals=$this->model->with('itemRentalDetails','user','itemRentalDetails.rentalItem')->latest()->get();
            return $this->respondWithSuccess('All Item Rental list',ItemRentalResourceCollection::make($itemRentals),Response::HTTP_OK);
        }catch(Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        $rentalNo=$this->generateItemRentalNo();
        $request['rental_no']=$rentalNo;
        $rules=$this->storeValidationRules($request);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondWithValidation('Validation Fail',$validator->errors()->first(),Response::HTTP_BAD_REQUEST);
        }

        DB::beginTransaction();
        try{
            $input=$request->all();

            $input['rental_date']=$request->rental_date?date('Y-m-d',strtotime($request->rental_date)):null;
            $input['return_date']=$request->return_date?date('Y-m-d',strtotime($request->return_date)):null;
            $input['status']=ItemRental::RENTAL;
            $itemRental=$this->model->create($input);

            // Store Item Rental Details -----------
            if (count($request->item_id)>0){

                $qtyAndAmountDate=$this->storeItemRentalDetails($request,$itemRental->id);
                // Qty Validation -------
                if ($qtyAndAmountDate['status']==false){
                    return $this->respondWithValidation('Validation Fail',$qtyAndAmountDate['validationMessage'],Response::HTTP_BAD_REQUEST);
                }

                // update qty and amount on ItemRental Table AND Return Date------
                $itemRental->update(['qty'=>$qtyAndAmountDate['qty'],'return_date'=>$qtyAndAmountDate['itemLatestReturnDate']]);
            }


            DB::commit();
            return $this->respondWithSuccess('Item Rental Info has been created successful',new  ItemRentalResource($itemRental),Response::HTTP_OK);

        }catch(\Exception $e){
            DB::rollback();
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeValidationRules($request){
        return [
            'rental_no' => 'unique:item_rentals,rental_no,NULL,id,deleted_at,NULL',
            //'qty'  => "required|numeric|digits_between:1,4",
            'rental_date'  => "nullable|date",
            'return_date'  => "nullable|date",
            'user_id'  => "nullable|exists:users,id",
            "item_id"   => "required|array|min:1",
            'item_id.*' => "exists:items,id",
            "item_qty"   => "required|array|min:1",
            'item_qty.*' => "digits_between:1,4",
        ];
    }

    public function storeItemRentalDetails($request,$itemRentalId,$update=false){

        $qty=0;
        foreach ($request->item_id as $key=>$itemId){
            $itemId=$request->item_id[$key];
            $requestItemQty=$request->item_qty[$key]?$request->item_qty[$key]:0;
            //Item Inventory Stock Validation -------
            //$itemStock=ItemInventoryStock::where(['item_id'=>$request->item_id[$key]])->first();
            $itemStock=Item::with('itemInventory')->where(['id'=>$request->item_id[$key]])->first();
            $itemTitle=$itemStock->title;
            if (empty($itemStock->itemInventory)){
                return ['status'=>false,'validationMessage'=>"Item Inventory Stock Not Found for $itemTitle"];
            }elseif(!empty($itemStock->itemInventory) && $requestItemQty>$itemStock->itemInventory->qty){ // if request qty greater than stock qty

                return ['status'=>false,'validationMessage'=>"Request qty greater than stock qty for $itemTitle"];
            }

            // Calculate Item Stock Qty ----------------
            $inventoryItemStockQty=$itemStock->itemInventory->qty;
            if ($update) {
                $oldRentalItem = ItemRentalDetail::where(['item_rental_id' => $itemRentalId, 'item_id' => $itemId])->first();
                $inventoryItemStockQty=$inventoryItemStockQty+$oldRentalItem->item_qty;
            }

            $afterDeductRequestQty=$inventoryItemStockQty-$requestItemQty;
            // Update Item Inventory Stock --------
            $itemStock->itemInventory->update(['qty'=>$afterDeductRequestQty]);

            // Generate Item Rental Details array ------
            $itemRentalDetails[]=[
                'item_rental_id'=>$itemRentalId,
                'item_id'=>$itemId,
                'item_qty'=>$requestItemQty,
                'return_date'=>$request->item_return_date[$key]?date('Y-m-d',strtotime($request->item_return_date[$key])):null,
            ];
            // calculate total qty-----
            $qty+=$request->item_qty[$key]?$request->item_qty[$key]:0;
        }
        //first Delete old rental item -------
        if ($update){
            ItemRentalDetail::where('item_rental_id',$itemRentalId)->delete();
        }
        //Insert new rental item -------
        ItemRentalDetail::insert($itemRentalDetails);

        // Update Return date by using immediate return able item return Date ------------
        $immediateReturnAbleItem=ItemRentalDetail::where(['item_rental_id'=>$itemRentalId])
            ->orderBy('return_date','DESC')->first();
        return ['status'=>true,'qty'=>$qty,'itemLatestReturnDate'=>$immediateReturnAbleItem->return_date];
    }

    public function generateItemRentalNo(){
        $lastOrderNo=ItemRental::max('rental_no');
        $lastOrderNo=$lastOrderNo?$lastOrderNo+1:1;

        $itemRentalLength= ItemRental::RENTALNOLENGTH;

        return str_pad($lastOrderNo,$itemRentalLength,"0",false);
    }

    public function show($id)
    {
        try{
            $itemRental=$this->model->with('itemRentalDetails','user','itemRentalDetails.rentalItem')->where('id',$id)->first();
            if ($itemRental){
                return $this->respondWithSuccess('Item rental Info',new  ItemRentalResource($itemRental),Response::HTTP_OK);
            }else{
                return $this->respondWithError('No data found',[],Response::HTTP_NOT_FOUND);
            }
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request,$id)
    {
        $rules=$this->updateValidationRules($request,$id);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondWithValidation('Validation Fail',$validator->errors()->first(),Response::HTTP_BAD_REQUEST);
        }

        DB::beginTransaction();
        try{
            $itemRental=$this->model->with('itemRentalDetails')->where('id',$id)->first();
            if (empty($itemRental)){
                return $this->respondWithError('No data found',[],Response::HTTP_NOT_FOUND);
            }

            $input=$request->all();

            $input['rental_date']=$request->rental_date?date('Y-m-d',strtotime($request->rental_date)):null;
            $input['return_date']=$request->return_date?date('Y-m-d',strtotime($request->return_date)):null;
            $input['status']=ItemRental::RENTAL;
            $this->model->update($input);

            // Store Item Rental Details -----------
            if (count($request->item_id)>0){
                $qtyAndAmountDate=$this->storeItemRentalDetails($request,$itemRental->id,true);
                // Qty Validation -------
                if ($qtyAndAmountDate['status']==false){
                    return $this->respondWithValidation('Validation Fail',$qtyAndAmountDate['validationMessage'],Response::HTTP_BAD_REQUEST);
                }
                // update qty and amount on ItemRental Table AND Return Date------
                $itemRental->update(['qty'=>$qtyAndAmountDate['qty'],'return_date'=>$qtyAndAmountDate['itemLatestReturnDate']]);
            }

            DB::commit();
            return $this->respondWithSuccess('Item Rental Info has been updated successful',new  ItemRentalResource($itemRental),Response::HTTP_OK);

        }catch(Exception $e){
            DB::rollback();
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateValidationRules($request,$id){
        return [
            'rental_no' => "unique:item_rentals,rental_no,$id,id,deleted_at,NULL",
            'rental_date'  => "nullable|date",
            'return_date'  => "nullable|date",
            'user_id'  => "nullable|exists:users,id",
            "item_id"   => "required|array|min:1",
            'item_id.*' => "exists:items,id",
            "item_qty"   => "required|array|min:1",
            'item_qty.*' => "digits_between:1,4",
        ];
    }

    public function updateItemRentalDetails($request,$itemRentalId){

        $qty=0;
        foreach ($request->item_id as $key=>$itemId){
            $itemRentalDetails[]=[
                'item_rental_id'=>$itemRentalId,
                'item_id'=>$request->item_id[$key],
                'item_qty'=>$request->item_qty[$key]?$request->item_qty[$key]:0,
                'return_date'=>$request->return_date?date('Y-m-d',strtotime($request->return_date)):null,
            ];
            $qty+=$request->item_qty[$key]?$request->item_qty[$key]:0;
        }
        ItemRentalDetail::insert($itemRentalDetails);
        return ['qty'=>$qty];
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try{
            $itemRental=$this->model->with('itemRentalDetails')->where('id',$id)->first();
            if (!$itemRental){
                return $this->respondWithError('No data found',[],Response::HTTP_NOT_FOUND);
            }

            $itemRental->load('itemRentalDetails');
            // Update Item Stock -------
            // add rental item qty to inventory item qty
            foreach ($itemRental->itemRentalDetails as $key=>$itemRentalDetail){
                $itemInventoryStock=ItemInventoryStock::where(['item_id'=>$itemRentalDetail->item_id])->first();
                $itemStock=$itemInventoryStock->qty+$itemRentalDetail->item_qty;
                $itemInventoryStock->update(['qty'=>$itemStock]);
            }
            $itemRental->itemRentalDetails()->delete();
            $itemRental->delete();

            DB::commit();
            return $this->respondWithSuccess('Item rental info has been Deleted',[],Response::HTTP_OK);

        }catch(\Exception $e){
            DB::rollback();
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
