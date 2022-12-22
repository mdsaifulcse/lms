<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MembershipPlanResource;
use App\Http\Resources\MembershipPlanResourceCollection;
use App\Http\Traits\ApiResponseTrait;
use App\Models\MembershipPlan;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use DB,Hash,Validator,Auth;

class MembershipController extends Controller
{

    use ApiResponseTrait;

    public function __construct(MembershipPlan $membershipPlan)
    {
        $this->model=$membershipPlan;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $authors=$this->model->latest('sequence','DESC')->get();
            return $this->respondWithSuccess('All Membership Plan list',MembershipPlanResourceCollection::make($authors),Response::HTTP_OK);
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

        return $this->respondWithSuccess('Membership Plan max serial',['sequence'=>$maxSequence],Response::HTTP_OK);
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
            // Photo upload --------------------
            $image='';
            if ($request->hasFile('image')){
                $image=\MyHelper::photoUpload($request->file('image'),'images/membership-plan-image',150);
                $input['image']=$image;
            }
            $author=$this->model->create($input);
            return $this->respondWithSuccess('Membership Plan Info has been updated successful',new  MembershipPlanResource($author),Response::HTTP_OK);

        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeValidationRules($request){
        return [
            'name' => 'required|unique:membership_plans,name,NULL,id,deleted_at,NULL|max:200',
            'description'  => "nullable",
            'term_policy'  => "nullable",
            'fee_amount'  => "required|digits_between:1,9",
            'valid_duration'  => "required|digits_between:0,3",
            'status'  => "required|in:1,2",
            'sequence'  => "required",
            'image' => 'image|mimes:jpeg,jpg,png,gif|nullable|max:8048'
        ];
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
            $author=$this->model->where('id',$id)->first();
            if ($author){
                return $this->respondWithSuccess('Author Info',new  MembershipPlanResource($author),Response::HTTP_OK);
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
        $rules=$this->updateValidationRules($request,$id);
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidation('Validation Fail',$validator->errors()->first(),Response::HTTP_BAD_REQUEST);
        }

        try{
            $membershipPlan=$this->model->where('id',$id)->first();

            if (!$membershipPlan){
                return $this->respondWithError('No data found',[],Response::HTTP_NOT_FOUND);
            }

            $input=$request->all();
            // Photo upload --------------------
            $image=$membershipPlan->image;
            if ($request->hasFile('image')){
                if($membershipPlan->image!=null and file_exists($membershipPlan->image)){ unlink($membershipPlan->image);}

                $image=\MyHelper::photoUpload($request->file('image'),'images/membership-plan-image',150);
                $input['image']=$image;
            }

            $membershipPlan->update($input);

            return $this->respondWithSuccess('Author Info has been updated successful',new  MembershipPlanResource($membershipPlan),Response::HTTP_OK);

        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateValidationRules($request,$membershipPlanId){
        return [
            'name' => "required|unique:membership_plans,name,$membershipPlanId,id,deleted_at,NULL|max:200",
            'description'  => "nullable",
            'term_policy'  => "nullable",
            'fee_amount'  => "required|digits_between:1,9",
            'valid_duration'  => "required|digits_between:1,3",
            'status'  => "required|in:1,2",
            'sequence'  => "required",
            'image' => 'image|mimes:jpeg,jpg,png,gif|nullable|max:8048'
        ];
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
            $membershipPlan=$this->model->where('id',$id)->first();
            if ($membershipPlan){
                if($membershipPlan->image!=null and file_exists($membershipPlan->image)){ unlink($membershipPlan->image);}

                $membershipPlan->delete();
                return $this->respondWithSuccess('Membership Plan has been Deleted',[],Response::HTTP_OK);
            }else{
                return $this->respondWithError('No data found',[],Response::HTTP_NOT_FOUND);
            }

        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
