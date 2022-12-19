<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserResourceCollection;
use App\Http\Traits\ApiResponseTrait;
use App\Models\User;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use DB,Hash,Validator,Auth;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(User $user)
    {
        $this->user=$user;
    }

    public function userInfoById($useId)
    {
        try{
            return $this->respondWithSuccess('Clint user list',['roles'=>$this->user->userRoles(),'user'=>new UserResource($this->user->where(['id'=>$useId])->first())],Response::HTTP_OK);
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allUserList($roleType=null)
    {
        try{
            if ($roleType=='admin'){
                $users=$this->user->allUser([$this->user::ADMIN,$this->user::LIBRARIAN,$this->user::SUPERADMIN]);
                $message='Admin user list';
            }elseif ($roleType=='client'){
                $users=$this->user->allUser([$this->user::GENERALUSER]);
                $message='Client user list';
            }else{
                $roleType='users';
                $message='All User List';
                $users=$this->user;
            }
            $users=$users->get();


            return $this->respondWithSuccess('Admin user list',['roles'=>$this->user->userRoles(),$roleType=>UserResourceCollection::make($users)],Response::HTTP_OK);
        }catch(Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

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
                'status'=>$request->status,
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
            'status'  => "required|in:1,2",
            'user_role'  => "required|in:3,4,5",
            'password' => 'required|same:confirm_password',
            'photo' => 'image|mimes:jpeg,jpg,png,gif|nullable|max:4048'
        ];
        return $rules;
    }

    public function userUpdateById(Request $request,$useId)
    {
        $id=$request->user_id;
        $rules=$this->updateValidationRules($request,$id);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondWithValidation('Validation Fail',$validator->errors()->first(),Response::HTTP_BAD_REQUEST);
        }

        try{

            $user = $this->user->find($id);
            $input=[
                'name'=>$request->name,
                'email'=>$request->email,
                'phone'=>$request->phone,
                'user_role'=>$request->user_role,
            ];
            // Photo upload ------
            $avatarPath=$user->profile_photo_path;
            if ($request->hasFile('photo')){
                if($user->profile_photo_path!=null and file_exists($user->profile_photo_path)){
                    unlink($user->profile_photo_path);
                }
                $avatarPath=\MyHelper::photoUpload($request->file('photo'),'images/user-photo',150);
                $input['profile_photo_path']=$avatarPath;
            }
            $user->update($input);

            return $this->respondWithSuccess('Data has been Updated',new UserResource($user),Response::HTTP_OK);
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function userChangePassword(Request $request,$useId) {

        $validator = Validator::make($request->json()->all(), [
            'user_id' => 'required|exists:users,id',
            'new_password' => 'required|min:8|same:confirm_password'
        ]);

        if ($validator->fails()) {
            return $this->respondWithValidation('Validation Fail',$validator->errors()->first(),Response::HTTP_BAD_REQUEST);
        }


        try{
            $user = User::where('id', $request->user_id)->first();

            if (!$user){
                return $this->respondWithError('No user data found',[],Response::HTTP_NOT_FOUND);
            }

            $user->update(['password' => bcrypt($request->new_password)]);
            return $this->respondWithSuccess('Password has been successfully updated',new UserResource($user),Response::HTTP_OK);

        }catch (\Exception $e){

            return $this->respondWithError('Something went wrong',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateValidationRules($request,$id){
        $rules=[
            'user_id' => 'required|exists:users,id',
            'name' => 'required|max:100',
            'email' => "required|unique:users,email,$id,id,deleted_at,NULL|email|max:100",
            'phone'  => "nullable|unique:users,phone,$id,id,deleted_at,NULL|max:15",
            'photo' => 'image|mimes:jpeg,jpg,png,gif|nullable|max:4048'
        ];
        return $rules;
    }

    public function destroy($useId)
    {
        try{
            $user=$this->user->find($useId);
            if ($user){
                $user->delete();
                return $this->respondWithSuccess('Deleted successfully',[],Response::HTTP_OK);
            }else{
                return $this->respondWithError('No user data found','No user data found',Response::HTTP_NOT_FOUND);
            }

        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
