<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Author;
use App\Models\Item;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Image,DB,Auth,Validator,MyHelper,Route,DataLoad;

class CommonDataLoadController extends Controller
{
    use ApiResponseTrait;

    public function activeItemSearch(Request $request)
    {
        if ($request->q && !empty($request->q)){
            return Item::select('title','id')
                ->where('title', 'like', '%' .$request->q. '%')->get();
        }else{
            return [];
        }
    }

    public function activeGeneralUserListList(){
        try{
            $generalUsers=DataLoad::generalUserList();
            return $this->respondWithSuccess('Active General User List',$generalUsers,Response::HTTP_OK);
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }  public function activeMembershipPlanList(){
        try{
            $membershipPlans=DataLoad::membershipPlanList();
            return $this->respondWithSuccess('Active Membership Plan list',$membershipPlans,Response::HTTP_OK);
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function activeVendorsList(){
        try{
            $categories=DataLoad::vendorList();
            return $this->respondWithSuccess('Active Vendors list',$categories,Response::HTTP_OK);
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function activeAuthorList(){

        try{
            $authors=DataLoad::authorList();
            return $this->respondWithSuccess('Active Author list',$authors,Response::HTTP_OK);
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function activePublisherList(){
        try{
            $authors=DataLoad::publisherList();
            return $this->respondWithSuccess('Active Publisher list',$authors,Response::HTTP_OK);
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function activeLanguageList(){
        try{
            $languages=DataLoad::languageList();
            return $this->respondWithSuccess('Active Language list',$languages,Response::HTTP_OK);
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function activeCountryList(){
        try{
            $countries=DataLoad::countryList();
            return $this->respondWithSuccess('Active Country list',$countries,Response::HTTP_OK);
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function activeCategoryList(){
        try{
            $categories=DataLoad::categoryList();
            return $this->respondWithSuccess('Active Category list',$categories,Response::HTTP_OK);
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function activeSubcategoryList($categoryId=null){
        try{
            $categories=DataLoad::subCatList($categoryId);
            return $this->respondWithSuccess('Active Sub Category list',$categories,Response::HTTP_OK);
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function activeThirdSubcategoryList($subCategoryId=null){
        try{
            $categories=DataLoad::thirdSubCatList($subCategoryId);
            return $this->respondWithSuccess('Active Third Sub Category list',$categories,Response::HTTP_OK);
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
