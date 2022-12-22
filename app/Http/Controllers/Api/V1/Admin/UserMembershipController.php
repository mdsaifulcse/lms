<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserMembershipPlanResource;
use App\Http\Resources\UserMembershipPlanResourceCollection;
use App\Http\Resources\UserResourceCollection;
use App\Http\Traits\ApiResponseTrait;
use App\Models\UserMembership;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use DB,Hash,Validator,Auth;

class UserMembershipController extends Controller
{
    use ApiResponseTrait;

    public function __construct(UserMembership $userMembership)
    {
        $this->model=$userMembership;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{

            $items=$this->model->get();
            return $this->respondWithSuccess('All User Membership Plan list',UserMembershipPlanResourceCollection::make($items),Response::HTTP_OK);
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
        $rules=$this->storeValidationRules($request);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondWithValidation('Validation Fail',$validator->errors()->first(),Response::HTTP_BAD_REQUEST);
        }

        try{

            $userWiseActivePlan=$this->model->where(['user_id'=>$request->user_id])->first();

            if (!empty($userWiseActivePlan)){

            }

            $userMembershipPlan=$this->model->create([
                'user_id'=>$request->user_id,
                'membership_plan_id'=>$request->membership_plan_id,
                'status'=>''
            ]);

            return $this->respondWithSuccess('Item has been created successful',new  UserMembershipPlanResource($userMembershipPlan),Response::HTTP_OK);

        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeValidationRules($request){
        return [
            'user_id' => 'required|exists:users,id',
            'membership_plan_id'  => "required|exists:membership_plans,id",
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
            $item=$this->model->with('itemAuthors','itemThumbnails')->where('id',$id)->first();
            if ($item){
                return $this->respondWithSuccess('Item Info',new  UserMembershipPlanResource($item),Response::HTTP_OK);
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
        //
    }
}
