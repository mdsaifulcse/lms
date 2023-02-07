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
use App\Models\Language;
use App\Models\LengthUnit;
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

    public function collectionList()
    {
       return Collection::orderBy('sequence','ASC')->where('status',Collection::ACTIVE)->pluck('collection','id');
    }

    public function tagList()
    {
       return Tag::orderBy('sequence','ASC')->where('status',Tag::ACTIVE)->pluck('tag','id');
    }

    public function packSizeUnitList()
    {
       return PackSizeUnit::orderBy('sequence','ASC')->where('status',PackSizeUnit::ACTIVE)->pluck('size','id');
    }
    public function originList()
    {
       return Origin::orderBy('sequence','ASC')->where('status',Origin::ACTIVE)->pluck('origin','id');
    }

    public function vatTaxList()
    {
       return VatTax::orderBy('sequence','ASC')->where('status',VatTax::ACTIVE)->pluck('vat_tax_name','id');
    }

    public function brandList()
    {
       return Brand::orderBy('sequence','ASC')->where('status',Brand::ACTIVE)->pluck('brand_name','id');
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

    public function fourthSubCatList($thirdSubCategoryId=null)
    {
        if ($thirdSubCategoryId!=null)
        {
            return FourthSubCategory::orderBy('sequence','ASC')->where(['third_sub_category_id'=>$thirdSubCategoryId,'status'=>SubCategory::ACTIVE])->pluck('fourth_sub_category','id');

        }else{

            return FourthSubCategory::orderBy('sequence','ASC')->where(['status'=>SubCategory::ACTIVE])->pluck('fourth_sub_category','id');
        }
    }



    public function weightUnitList()
    {
        return WeightUnit::orderBy('sequence','ASC')->where('status',WeightUnit::ACTIVE)->pluck('weight_unit','id');
    }

    public function lengthUnitList()
    {
        return LengthUnit::orderBy('sequence','ASC')->where('status',LengthUnit::ACTIVE)->pluck('length_unit','id');
    }

    public function purchaseNoList($vendorId=null)
    {
        if ($vendorId!=null)
        {
            return ProductPurchase::orderBy('id','DESC')->where(['vendor_id'=>$vendorId])->pluck('purchase_no','id')->toArray();
        }else{
            return ProductPurchase::orderBy('id','DESC')->pluck('purchase_no ','id')->toArray();
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

    public function bankAccountList()
    {
       return BankAccount::select(
           DB::raw("CONCAT(account_number,' - ',account_title) AS account_name"),'id')->orderBy('sequence','ASC')
           ->where('status',BankAccount::ACTIVE)->pluck('account_name','id');
    }

    public function divisionList()
    {
       return Division::orderBy('sequence','ASC')->where('status',Division::ACTIVE)->pluck('division','id');
    }

    public function districtList($divisionId=null)
    {
        if ($divisionId!=null)
        {
            return District::orderBy('sequence','ASC')->where(['division_id'=>$divisionId,'status'=>District::ACTIVE])->pluck('district','id');
        }else{
            return District::orderBy('sequence','ASC')->where(['status'=>District::ACTIVE])->pluck('district','id');
        }

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


    public function incomeExpenseList($headType=null)
    {
        if ($headType!=null)
        {
            return IncomeExpenseHead::orderBy('sequence','ASC')->where(['head_type'=>$headType,'status'=>IncomeExpenseHead::ACTIVE])->pluck('head_title','id');
        }else{
            return IncomeExpenseHead::orderBy('sequence','ASC')->where(['status'=>IncomeExpenseHead::ACTIVE])->pluck('head_title','id');
        }
    }

    public function incomeExpenseSubHeads($headId=null)
    {
        if ($headId!=null)
        {
           return IncomeExpenseSubHead::orderBy('sequence','ASC')
                ->where(['income_expense_head_id'=>$headId,'status'=>IncomeExpenseSubHead::ACTIVE])->pluck('sub_head_title','id');
        }else{
           return IncomeExpenseSubHead::orderBy('sequence','ASC')
                ->where(['status'=>IncomeExpenseSubHead::ACTIVE])->pluck('sub_head_title','id');
        }
    }


    public function loadSubHeadsByHeadId($headId)
    {
        $subHeads=$this->incomeExpenseSubHeads($headId);

        return view('include.load-subhead',compact('subHeads'));
    }

    public function deliveryStatus()
    {
        return [
            OrderAssignDelivery::PENDING=>OrderAssignDelivery::PENDING,
            OrderAssignDelivery::CANCELLED=>OrderAssignDelivery::CANCELLED,
            OrderAssignDelivery::RECEIVED=>OrderAssignDelivery::RECEIVED,
            OrderAssignDelivery::SHIPPING=>OrderAssignDelivery::SHIPPING,
            OrderAssignDelivery::COMPLETE=>OrderAssignDelivery::COMPLETE,
        ];
    }



}