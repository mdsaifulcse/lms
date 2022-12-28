<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemReturnResource;
use App\Http\Resources\ItemReturnResourceCollection;
use App\Http\Traits\ApiResponseTrait;
use App\Models\ItemRental;
use App\Models\ItemRentalDetail;
use App\Models\ItemReturn;
use App\Models\ItemReturnDetail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use DB,Hash,Validator,Auth,Carbon\Carbon;

class ItemReturnController extends Controller
{
    use ApiResponseTrait;

    public function __construct(ItemReturn $itemReturn)
    {
        $this->model=$itemReturn;
    }

    public function index()
    {
        try{
            $itemReturns=$this->model->with('itemReturnDetails')->latest()->get();
            return $this->respondWithSuccess('All Item Return list',ItemReturnResourceCollection::make($itemReturns),Response::HTTP_OK);
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        $rentalNo=$this->generateItemReturnNo();
        $request['return_no']=$rentalNo;
        $rules=$this->storeValidationRules($request);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondWithValidation('Validation Fail',$validator->errors()->first(),Response::HTTP_BAD_REQUEST);
        }

        DB::beginTransaction();
        try{
            $input=$request->all();
            $input['return_date']=$request->return_date?date('Y-m-d',strtotime($request->return_date)):null;

            $itemReturn=ItemReturn::where(['item_rental_id'=>$request->item_rental_id])->first();

            if ($itemReturn){ // Update ItemReturn
                unset($input['return_no']);
                $itemReturn->update($input);
            }else{ // Create ItemReturn
                $itemReturn=$this->model->create($input);
            }

            // Store Item Return Details -----------
            if (count($request->item_id)>0){
                $returnQty=$this->storeItemReturnDetails($request,$itemReturn->id);
                // update qty on ItemReturn Table
                $totalReturnQty=$itemReturn->qty+$returnQty['qty'];
                $itemReturn->update(['qty'=>$totalReturnQty]);

                // Item Rental Status change
                $itemRental=ItemRental::find($request->item_rental_id);
                if ($itemRental->qty==$totalReturnQty){ // As long as Rental Qyt == Return qty then rental status is RETURN BACk
                    $itemRental->update(['status'=>ItemRental::RETURNBACK]);
                }
            }

            DB::commit();
            return $this->respondWithSuccess('Item Rental Info has been created successful',new  ItemReturnResource($itemReturn),Response::HTTP_OK);

        }catch(Exception $e){
            DB::rollback();
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeValidationRules($request){
        return [
            'return_no' => 'unique:item_returns,return_no,NULL,id,deleted_at,NULL',
            'item_rental_id'  => "nullable|exists:item_rentals,id",
            //'qty'  => "required|numeric|digits_between:1,4",
            'return_date'  => "nullable|date",

            "item_id"   => "required|array|min:1",
            'item_id.*' => "exists:items,id",

            "item_qty"   => "required|array|min:1",
            'item_qty.*' => "digits_between:1,4",
        ];
    }

    public function storeItemReturnDetails($request,$itemReturnId,$update=false){

        if ($update){ // Delete old rental item -------
            ItemReturn::where('item_return_id',$itemReturnId)->delete();
        }

        $qty=0;
        foreach ($request->item_id as $key=>$itemId){
            $itemReturnDetails[]=[
                'item_return_id'=>$itemReturnId,
                'item_id'=>$request->item_id[$key],
                'item_qty'=>$request->item_qty[$key]?$request->item_qty[$key]:0,
                'return_date'=>$request->return_date?date('Y-m-d',strtotime($request->return_date)):null,
            ];
            $qty+=$request->item_qty[$key]?$request->item_qty[$key]:0;
        }
        ItemReturnDetail::insert($itemReturnDetails);
        return ['qty'=>$qty];
    }

    public function generateItemReturnNo(){
        $lastOrderNo=ItemReturn::max('return_no');
        $lastOrderNo=$lastOrderNo?$lastOrderNo+1:1;

        $itemReturnLength= ItemReturn::RETURNNOLENGTH;

        return str_pad($lastOrderNo,$itemReturnLength,"0",false);
    }

    public function show($id)
    {
        try{
            $itemReturn=$this->model->with('itemReturnDetails')->where('id',$id)->first();
            if ($itemReturn){
                return $this->respondWithSuccess('Item Return Info',new  ItemReturnResource($itemReturn),Response::HTTP_OK);
            }else{
                return $this->respondWithError('No data found',[],Response::HTTP_NOT_FOUND);
            }
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try{
            $itemReturn=$this->model->with('itemReturnDetails')->where('id',$id)->first();
            if (!$itemReturn){
                return $this->respondWithError('No data found',[],Response::HTTP_NOT_FOUND);
            }

            $itemRental=ItemRental::find($itemReturn->item_rental_id);
            if ($itemRental){
                $status=ItemRental::RENTAL;

                // Item Rental status change based on return Supposed to return date and return date
                $supposedToReturnDate=New Carbon($itemRental->return_date);
                $actualReturnDate=New Carbon($itemReturn->return_date);
                //$dayDifference=$actualReturnDate->diffInDays($supposedToReturnDate);
                if ($actualReturnDate->gt($supposedToReturnDate)){
                    $status=ItemRental::OVERDUE;
                    $itemRental->update(['status'=>$status]);
                }

            }

            $itemReturn->load('itemReturnDetails');
            $itemReturn->itemReturnDetails()->delete();
            $itemReturn->delete();

            return $this->respondWithSuccess('Item rental info has been Deleted',[],Response::HTTP_OK);

        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
