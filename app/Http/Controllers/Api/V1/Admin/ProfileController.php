<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\User;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Symfony\Component\HttpFoundation\Response;
use DB,Hash,Validator,Auth;

class ProfileController extends Controller
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function profileInfo()
    {
        try{
            $user = $this->user->find(Auth::user()->id);

            if (!$user) {
                return $this->respondWithError('No user found',['No user found'],Response::HTTP_NOT_FOUND);
            }

            return $this->respondWithSuccess('My Profile Info',new UserResource($user),Response::HTTP_OK);
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function profileUpdate(Request $request)
    {
        $id=Auth::user()->id;
        $rules=$this->registerValidationRules($request,$id);
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
            ];
            // Photo upload ------
            $avatarPath='';
            if ($request->hasFile('photo')){
                if($user->profile_photo_path!=null and file_exists($user->profile_photo_path)){
                    unlink($user->profile_photo_path);
                }
                $avatarPath=\MyHelper::photoUpload($request->file('photo'),'images/user-photo',150);
                $input['profile_photo_path']=$avatarPath;
            }
            $user->update($input);

            return $this->respondWithSuccess('Your Profile has been Updated',new UserResource($user),Response::HTTP_OK);
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function registerValidationRules($request,$id){
        $rules=[
            'name' => 'required|max:100',
            'email' => "required|unique:users,email,$id,id,deleted_at,NULL|email|max:100",
            'phone'  => "nullable|unique:users,phone,$id,id,deleted_at,NULL|max:15",
            'photo' => 'image|mimes:jpeg,jpg,png,gif|nullable|max:4048'
        ];
        return $rules;
    }


    public function changeMyPassword(Request $request) {
        $validator = Validator::make($request->json()->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:8|same:confirm_password'
        ]);

        if ($validator->fails()) {
            return $this->respondWithValidation('Validation Fail',$validator->errors()->first(),Response::HTTP_BAD_REQUEST);
        }


        try{
            $user = User::where('id', Auth::user()->id)->first();

            if (!$user){return $this->respondWithError('No user data found',[],Response::HTTP_NOT_FOUND); }

            if(Hash::check($request->old_password, $user->password)) {

                $user->update(['password' => bcrypt($request->new_password)]);
                return $this->adminLogin('Your password successfully update',new UserResource($user),'admin',$request->new_password);

            } else {
                return $this->respondWithError('Validation Fail','your old password did not match',Response::HTTP_UNPROCESSABLE_ENTITY);
            }

        }catch (\Exception $e){

            return $this->respondWithError('Something went wrong',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(User $user)
    {
        try{
            $user->delete();
            return redirect()->back()->with('success', 'Deleted successfully!');

        }catch(\Exception $e){
            return back()->with('errors',$e->getMessage());
        }
    }
}
