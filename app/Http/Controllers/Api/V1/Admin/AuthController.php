<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use DB,Hash,Validator,Auth;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function __construct(User $user)
    {
        $this->user=$user;
    }

    public function adminRegister(Request $request){

        $rules=$this->registerValidationRules($request);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondWithValidation('Validation Fail',$validator->errors()->first(),Response::HTTP_BAD_REQUEST);
        }

        //DB::beginTransaction();
        try{
            $input=[
                'name'=>$request->name,
                'email'=>$request->email,
                'phone'=>$request->phone,
                'user_role'=>$request->user_role,
                'status'=>User::APPROVED,
                'password'=>Hash::make($request->password)
            ];
            // Photo upload ------
            $avatarPath='';
            if ($request->hasFile('photo')){
                $avatarPath=\MyHelper::photoUpload($request->file('photo'),'images/user-photo',150);
                $input['profile_photo_path']=$avatarPath;
            }
            $user = $this->user->create($input);

            //$user->assignRole($request->input('roles'));
            //DB::commit();
            return $this->respondWithSuccess('User has been successfully registered',new UserResource($user),Response::HTTP_OK);
            //return $this->adminLogin('User has been successfully registered and logged in',$user,'',$request->password);
        }catch (\Exception $e){
            //DB::rollback();
            return $this->respondWithError('Something went wrong',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function registerValidationRules($request){
        $rules=[
            'name' => 'required|max:100',
            'email'  => "nullable|unique:users,email,NULL,id,deleted_at,NULL|email|max:100",
            'phone'  => "nullable|unique:users,phone,NULL,id,deleted_at,NULL|max:15",
            'user_role'  => "required|in:3,4",
            'password' => 'required|same:confirm_password',
            'photo' => 'image|mimes:jpeg,jpg,png,gif|nullable|max:4048'
        ];
        return $rules;
    }

    public function login(Request $request){
        $rules=$this->loginValidationRules($request);
        $validator = Validator::make($request->json()->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidation('Validation Fail',$validator->errors()->first(),Response::HTTP_BAD_REQUEST);
        }

        try{

            $user = User::where('phone', $request->username)
                ->orWhere('email',$request->username)
                //->orWhere('username',$request->username)
                //->where('user_role',User::ADMIN)
                ->activeUser([$this->user::ADMIN,$this->user::LIBRARIAN,$this->user::SUPERADMIN])
                ->first();

            if (!$user) {
                return $this->respondWithError('Credentials do not match',['Credentials do not match'],Response::HTTP_NOT_FOUND);
            }

            return $this->adminLogin('You successfully logged in',new UserResource($user),'admin-login',$request->password);
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function loginValidationRules($request){

        $rules=[
            'username' => 'required',
            'password' => 'required',

        ];

        return $rules;

//        if ($request->has('mobile_number')){
//            $rules+=[
//                'mobile_number' => 'required'
//            ];
//        }elseif ($request->has('email')){
//            $rules+=[
//                'email' => 'required'
//            ];
//        }
//        return $rules;
    }

    public function logout(){
        $user= Auth::user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        return $this->respondWithSuccess('You successfully logged out',[],Response::HTTP_OK);
    }

}
