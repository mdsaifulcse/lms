<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\VendorResource;
use App\Http\Resources\VendorResourceCollection;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use DB,Hash,Validator,Auth;

class VendorController extends Controller
{
    use ApiResponseTrait;

    public function __construct(Vendor $vendor)
    {
        $this->model=$vendor;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $vendors=$this->model->latest('sequence','DESC')->get();
            return $this->respondWithSuccess('All Vendor list',VendorResourceCollection::make($vendors),Response::HTTP_OK);
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getMaxSequence()
    {
        $maxSequence=$this->model::max('sequence')+1;

        return $this->respondWithSuccess('Vendor max serial',['sequence'=>$maxSequence],Response::HTTP_OK);
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

        try{
            $input=$request->all();

            // Photo upload ------
            $photo='';
            if ($request->hasFile('photo')){
                $photo=\MyHelper::photoUpload($request->file('photo'),'images/vendor-photo',150);
                $input['photo']=$photo;
            }
            $this->model->create($input);
            $vendor=$this->model->latest()->first();
            return $this->respondWithSuccess('Vendor Info has been updated successful',new  VendorResource($vendor),Response::HTTP_OK);

        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeValidationRules($request){
        return [
            'name' => 'required|max:100',
            'email'  => "nullable|max:80",
            'mobile'  => "nullable:max:15",
            'contact_person'  => "nullable|max:100",
            'contact_person_mobile'  => "nullable|max:30",
            'office_address'  => "nullable|max:800",
            'warehouse_address'  => "nullable|max:800",
            'primary_supply_products'  => "nullable|max:15",
            'status'  => "required|in:1,2",
            'sequence'  => "required",
            'photo' => 'image|mimes:jpeg,jpg,png,gif|nullable|max:8048'
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $vendor=$this->model->where('id',$id)->first();
            if ($vendor){
                return $this->respondWithSuccess('Vendor Info',new  VendorResource($vendor),Response::HTTP_OK);
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
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function edit(Vendor $vendor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules=$this->updateValidationRules($request);
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidation('Validation Fail',$validator->errors()->first(),Response::HTTP_BAD_REQUEST);
        }

        try{
            $vendor=$this->model->where('id',$id)->first();

            if (!$vendor){
                return $this->respondWithError('No data found',[],Response::HTTP_NOT_FOUND);
            }

            $input=$request->all();
            // Photo upload ------
            $photo=$vendor->photo;
            if ($request->hasFile('photo')){
                if($vendor->photo!=null and file_exists($vendor->photo)){ unlink($vendor->photo);}

                $photo=\MyHelper::photoUpload($request->file('photo'),'images/vendor-photo',150);
                $input['photo']=$photo;
            }

            $vendor->update($input);

            $vendor=$this->model->find($id);

            return $this->respondWithSuccess('Vendor Info has been updated successful',new  VendorResource($vendor),Response::HTTP_OK);

        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateValidationRules($request){
        return [
            'name' => 'required|max:100',
            'email'  => "nullable|max:80",
            'mobile'  => "nullable:max:15",
            'contact_person'  => "nullable|max:100",
            'contact_person_mobile'  => "nullable|max:30",
            'office_address'  => "nullable|max:800",
            'warehouse_address'  => "nullable|max:800",
            'primary_supply_products'  => "nullable|max:15",
            'status'  => "required|in:1,2",
            'sequence'  => "required",
            'photo' => 'image|mimes:jpeg,jpg,png,gif|nullable|max:8048'
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $vendor=$this->model->where('id',$id)->first();
            if ($vendor){
                $vendor->delete();
                return $this->respondWithSuccess('Vendor has been Deleted',[],Response::HTTP_OK);
            }else{
                return $this->respondWithError('No data found',[],Response::HTTP_NOT_FOUND);
            }

        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
