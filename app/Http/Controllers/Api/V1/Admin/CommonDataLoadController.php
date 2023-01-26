<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use Symfony\Component\HttpFoundation\Response;
use Image,DB,Auth,Validator,MyHelper,Route,DataLoad;

class CommonDataLoadController extends Controller
{
    use ApiResponseTrait;
    public function categoryList(){
        try{
            $categories=DataLoad::categoryList();
            return $this->respondWithSuccess('All Sub Category list',$categories,Response::HTTP_OK);
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
