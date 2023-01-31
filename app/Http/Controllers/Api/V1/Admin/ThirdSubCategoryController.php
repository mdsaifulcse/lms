<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ThirdSubCategoryResource;
use App\Http\Resources\ThirdSubCategoryResourceCollection;
use App\Http\Traits\ApiResponseTrait;
use App\Models\ThirdSubCategory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use DB,Hash,Validator,Auth;

class ThirdSubCategoryController extends Controller
{
    use ApiResponseTrait;

    public function __construct(ThirdSubCategory $thirdSubCategory)
    {
        $this->model=$thirdSubCategory;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $thirdCategories=$this->model->with('suCategory')->latest('sequence','DESC')->get();
            return $this->respondWithSuccess('All Third Category list',ThirdSubCategoryResourceCollection::make($thirdCategories),Response::HTTP_OK);
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
        return $this->respondWithSuccess('Third Category max serial',['sequence'=>$maxSequence],Response::HTTP_OK);
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
            $this->model->create($request->all());

            $thirdCategory=$this->model->latest()->first();
            return $this->respondWithSuccess('Third Sub Category Info has been created successful',new  ThirdSubCategoryResource($thirdCategory),Response::HTTP_OK);

        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeValidationRules($request){
        return [
            'sub_category_id' => 'required|exists:sub_categories,id',
            'name' => 'required|max:100',
            'description' => 'nullable|max:200',
            'status'  => "required|in:0,1",
            'sequence'  => "required",
            //'photo' => 'image|mimes:jpeg,jpg,png,gif|nullable|max:8048'
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ThirdSubCategory  $thirdSubCategory
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $thirdCategory=$this->model->where('id',$id)->first();
            if ($thirdCategory){
                return $this->respondWithSuccess('Third Sub Category Info',new  ThirdSubCategoryResource($thirdCategory),Response::HTTP_OK);
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
     * @param  \App\Models\ThirdSubCategory  $thirdSubCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(ThirdSubCategory $thirdSubCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ThirdSubCategory  $thirdSubCategory
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
            $thirdCategory=$this->model->where('id',$id)->first();

            if (!$thirdCategory){
                return $this->respondWithError('No data found',[],Response::HTTP_NOT_FOUND);
            }
            $thirdCategory->update($request->all());

            $thirdCategory=$this->model->find($id);

            return $this->respondWithSuccess('Third Sub Category Info has been updated successful',new  ThirdSubCategoryResource($thirdCategory),Response::HTTP_OK);

        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateValidationRules($request){
        return [
            'sub_category_id' => 'required|exists:sub_categories,id',
            'name' => 'required|max:100',
            'description' => 'nullable|max:200',
            'status'  => "required|in:0,1",
            'sequence'  => "required",
            //'photo' => 'image|mimes:jpeg,jpg,png,gif|nullable|max:8048'
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ThirdSubCategory  $thirdSubCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $thirdCategory=$this->model->where('id',$id)->first();
            if ($thirdCategory){
                $thirdCategory->delete();
                return $this->respondWithSuccess('Third Sub Category has been Deleted',[],Response::HTTP_OK);
            }else{
                return $this->respondWithError('No data found',[],Response::HTTP_NOT_FOUND);
            }

        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
