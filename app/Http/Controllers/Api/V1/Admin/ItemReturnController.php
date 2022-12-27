<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemReturnResource;
use App\Http\Resources\ItemReturnResourceCollection;
use App\Http\Traits\ApiResponseTrait;
use App\Models\ItemRentalDetail;
use App\Models\ItemReturn;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use DB,Hash,Validator,Auth;
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
            $itemReturns=$this->model->with('itemRentalDetails')->latest()->get();
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

            $input['rental_date']=$request->rental_date?date('Y-m-d',strtotime($request->rental_date)):null;
            $input['return_date']=$request->return_date?date('Y-m-d',strtotime($request->return_date)):null;
            $input['status']=ItemRental::RENTAL;
            $itemReturn=$this->model->create($input);

            // Store Item Return Details -----------
            if (count($request->item_id)>0){
                $qtyAndAmount=$this->storeItemReturnDetails($request,$itemReturn->id);

                // update qty and amount on ItemReturn Table
                $itemReturn->update(['qty'=>$qtyAndAmount['qty']]);
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
                'item_rental_id'=>$itemReturnId,
                'item_id'=>$request->item_id[$key],
                'item_qty'=>$request->item_qty[$key]?$request->item_qty[$key]:0,
                'return_date'=>$request->return_date?date('Y-m-d',strtotime($request->return_date)):null,
            ];
            $qty+=$request->item_qty[$key]?$request->item_qty[$key]:0;
        }
        ItemRentalDetail::insert($itemReturnDetails);
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
            $itemReturn=$this->model->with('itemRentalDetails')->where('id',$id)->first();
            if ($itemReturn){
                return $this->respondWithSuccess('Item rental Info',new  ItemRentalResource($itemReturn),Response::HTTP_OK);
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
            $itemReturn=$this->model->with('itemRentalDetails')->where('id',$id)->first();
            if (!$itemReturn){
                return $this->respondWithError('No data found',[],Response::HTTP_NOT_FOUND);
            }

            $itemReturn->load('itemRentalDetails');
            $itemReturn->itemRentalDetails()->delete();
            $itemReturn->delete();

            return $this->respondWithSuccess('Item rental info has been Deleted',[],Response::HTTP_OK);

        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
