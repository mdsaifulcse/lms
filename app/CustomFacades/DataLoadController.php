<?php
/**
 * Created by PhpStorm.
 * User: mdsaiful
 * Date: 12/23/2019
 * Time: 11:50 AM
 */
namespace App\CustomFacades;


use App\Models\Author;
use App\Models\BankAccount;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Country;
use App\Models\District;
use App\Models\Division;
use App\Models\FourthSubCategory;
use App\Models\IncomeExpenseHead;
use App\Models\IncomeExpenseSubHead;
use App\Models\Item;
use App\Models\Language;
use App\Models\LengthUnit;
use App\Models\Membership;
use App\Models\MembershipPlan;
use App\Models\OrderAssignDelivery;
use App\Models\Origin;
use App\Models\PackSizeUnit;
use App\Models\Product;
use App\Models\ProductPurchase;
use App\Models\Publisher;
use App\Models\Setting;
use App\Models\SubCategory;
use App\Models\Tag;
use App\Models\ThirdSubCategory;
use App\Models\User;
use App\Models\VatTax;
use App\Models\Vendor;
use App\Models\WeightUnit;
use phpDocumentor\Reflection\Types\Null_;
use DB;
class DataLoadController
{
    public function relatedProductList($productId=null)
    {
        if ($productId!=null){
            return Product::orderBy('sequence','ASC')->where('id','!=',$productId)->where(['status'=>Product::PUBLISHED])->pluck('name','id');
        }else{
            return Product::orderBy('sequence','ASC')->where('status',Product::PUBLISHED)->pluck('name','id');
        }
    }

    public function membershipPlanList()
    {
        return MembershipPlan::orderBy('sequence','ASC')->where('status',MembershipPlan::ACTIVE)->get(['name','id']);
    }

    public function generalUserList()
    {
        return User::orderBy('id','ASC')->where(['user_role'=>User::GENERALUSER,'status'=>User::APPROVED])->get(['name','id']);
    }

    public function countryList()
    {
        return Country::orderBy('sequence','ASC')->where('status',Country::ACTIVE)->get(['name','id']);
    }

    public function languageList()
    {
        return Language::orderBy('sequence','ASC')->where('status',Language::ACTIVE)->get(['name','id']);
    }

    public function authorList()
    {
        return Author::orderBy('sequence','ASC')->where('status',Author::ACTIVE)->get(['id','name']);
        //return Author::orderBy('sequence','ASC')->where('status',Author::ACTIVE)->get(['id as value','name as label']);
    }
    public function vendorList()
    {
        return Vendor::select(DB::raw("CONCAT(name,'-',mobile) AS name"),'id')
            ->where('status',Vendor::ACTIVE)->get('name','id');
    }

    public function publisherList()
    {
        return Publisher::orderBy('sequence','ASC')->where('status',Publisher::ACTIVE)->get(['name','id']);
    }

    public function categoryList()
    {
       return Category::select('name','id')->orderBy('sequence','ASC')->where('status',Category::ACTIVE)->get();
    }

    public function subCatList($categoryId=null)
    {
        if ($categoryId!=null)
        {
            return SubCategory::select('name','id','category_id')->orderBy('sequence','ASC')->where(['category_id'=>$categoryId,'status'=>SubCategory::ACTIVE])->get();

        }else{

            return SubCategory::select('name','id','category_id')->orderBy('sequence','ASC')->where(['status'=>SubCategory::ACTIVE])->get();
        }
    }

    public function thirdSubCatList($subCategoryId=null)
    {
        if ($subCategoryId!=null)
        {
            return ThirdSubCategory::select('name','id')->orderBy('sequence','ASC')->where(['sub_category_id'=>$subCategoryId,'status'=>ThirdSubCategory::ACTIVE])->get('name','id');

        }else{

            return ThirdSubCategory::select('name','id')->orderBy('sequence','ASC')->where(['status'=>ThirdSubCategory::ACTIVE])->get('name','id');
        }
    }

    public function loadPurchaseNumbersByVendor($vendorId)
    {
        $purchaseNoList= $this->purchaseNoList($vendorId);
        return view('include.load-purchasenumber',compact('purchaseNoList'));
    }

    public function vendorRemainingDueCalculation($vendorId)
    {
        $vendor=Vendor::findOrFail($vendorId);

        return $totalDueRemaining=$vendor->total_due-$vendor->balance;
        // return response()->json($totalDueRemaining);
    }


    public function loadSubCatsByCat($categoryId)
    {
        $subCats=$this->subCatList($categoryId);
        return view('include.load-subcategory',compact('subCats'));
    }

    public function loadThirdSubCatsByCat($subCategoryId)
    {
        $thirdSubCats=$this->thirdSubCatList($subCategoryId);
        return view('include.load-third-subcategory',compact('thirdSubCats'));
    }

    public function loadFourthSubCatsByCat($thirdSubCategoryId)
    {
        $fourthSubCats=$this->fourthSubCatList($thirdSubCategoryId);
        return view('include.load-fourth-subcategory',compact('fourthSubCats'));
    }

    public function setting()
    {
       return Setting::first();
    }

    public function loadSubHeadsByHeadId($headId)
    {
        $subHeads=$this->incomeExpenseSubHeads($headId);

        return view('include.load-subhead',compact('subHeads'));
    }
}