<?php

namespace App\Http\Traits;
use Validator;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Hash;
trait ApiResponseTrait
{
    public $projectName='Library Management System.';

    public function adminLogin($message=null,$user=null,$tokenName='token-name',$password)
    {
        if (! $user || ! Hash::check($password, $user->password)) {
        return $this->respondWithError('The provided credentials are incorrect.',[],Response::HTTP_UNAUTHORIZED);
       }

       $token= $user->createToken($tokenName)->plainTextToken;
       return $this->respondWithToken($token,$message,$user);

        //$credentials = request(['mobile_number', 'password']);

        // if (! $token = auth('api')->attempt($credentials))
        // {
        //     return $this->respondWithError('Credential does not match !',[],Response::HTTP_UNAUTHORIZED);
        // }
    }

    public function clientLogin($message=null,$user=null,$tokenName='token-name',$password)
    {
        if (! $user || ! Hash::check($password, $user->password)) {
            return $this->respondWithError('The provided credentials are incorrect.',[],Response::HTTP_UNAUTHORIZED);
        }

        $token= $user->createToken($tokenName)->plainTextToken;
        return $this->respondWithToken($token,$message,$user);
    }

    public function mobileLogin($message='Code matched & Verified',$user=null,$tokenName='token-name',$password)
    {
       if (! $user || ! Hash::check($password, $user->password)) {
        return $this->respondWithError('The provided credentials are incorrect.',[],Response::HTTP_UNAUTHORIZED);
       }

       $token= $user->createToken($tokenName)->plainTextToken;

       return $this->respondWithTokenForMobile($token,$message,$user);
    }

    public function otpLogin($message='Code matched & Verified',$user=null)
    {

        if (!$userToken=JWTAuth::fromUser($user)) {
            return $this->respondWithError('Credential does not match !',[],Response::HTTP_UNAUTHORIZED);
        }

        return $this->respondWithTokenForMobile($userToken,$message,$user);
    }

    protected function respondWithToken($token,$message=null,$user=[])
    {
        return response()->json([
            //'status' => true,
            'status' => 'OK',
            'access_token' => $token,
            'token_type' => 'bearer',
            'message' => $message,
            'user' => $user,
            //'expires_in' => auth('api')->factory()->getTTL() * 60
        ],Response::HTTP_OK)->setStatusCode(Response::HTTP_OK);
    }

    protected function respondWithTokenForMobile($token,$message=null,$user=[])
    {

//        $user['access_token']=$token;
//        $user['token_type']='bearer';

        return response()->json([
            'status' => 'OK',
            'messages' => $message,
            //'result' => $user,
            'result' => ['user'=>$user,'access_token' => $token,'token_type' => 'bearer',],
        ],Response::HTTP_OK)->setStatusCode(Response::HTTP_OK);
    }

    protected function respondWithSuccess($message='', $datas=[],$code = Response::HTTP_OK)
    {
        //return $datas;
        return response()->json([
            'status' => 'OK',
            'message' => $message,
            'result' => $datas
        ],$code);

    }

    protected function respondWithError($message='',$data=[],$code=Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        return response()->json([
            'status'=>'FAIL',
            'message'=>$message,
            'result'=>$data
        ], $code);
    }

    protected function respondWithValidation($message,$messageBag,$code=Response::HTTP_BAD_REQUEST)
    {
        return response()->json([
                'status'=>'FAIL',
                'message'=>$message,
                'errors'=>$messageBag,
                'result'=>[]
            ],$code);
    }

    protected function noDataFoundException($message='',$data=[],$code=Response::HTTP_NOT_FOUND)
    {
        return response()->json([
            'status'=>'FAIL',
            'messages'=>$message,
            'result'=>$data
        ],$code);
    }
    protected function alreadyExists($message='',$data=[],$code=Response::HTTP_CONFLICT)
    {
        return response()->json([
            'status'=>'FAIL',
            'message'=>$message,
            'result'=>$data
        ],$code);
    }

}
