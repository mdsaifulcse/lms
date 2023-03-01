<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory,SoftDeletes;
    const ACTIVE=1;
    const INACTIVE=0;

    const YES=1;
    const NO=0;
    protected $table='items';
    protected $fillable=['title','isbn','edition','number_of_page','summary','video_url','brochure','publisher_id',
        'language_id','country_id','category_id','sub_category_id','third_category_id','show_home','sequence','status','publish_status','created_by','updated_by'];


    public function category(){
        return $this->belongsTo(Category::class,'category_id','id');
    }
    public function subCategory(){
        return $this->belongsTo(SubCategory::class,'sub_category_id','id');
    }
    public function thirdSubCategory(){
        return $this->belongsTo(ThirdSubCategory::class,'third_category_id','id');
    }
    public function country(){
        return $this->belongsTo(Country::class,'country_id','id');
    }
    public function publisher(){
        return $this->belongsTo(Publisher::class,'publisher_id','id');
    }
    public function language(){
        return $this->belongsTo(Language::class,'language_id','id');
    }
    public function itemAuthors(){
        return $this->hasMany(ItemAuthor::class,'item_id','id');
    }
    public function relItemAuthorsName(){
        return $this->belongsToMany(Author::class,'item_authors')->whereNull('item_authors.deleted_at')
            ->select(['name','email','mobile','authors.id']);
    }
    public function itemThumbnails(){
        return $this->hasMany(ItemThumbnail::class,'item_id','id')->orderBy('id','DESC');
    }
    public function itemInventory(){
        return $this->hasOne(ItemInventoryStock::class,'item_id','id');
    }

    public static function boot(){
        parent::boot();
        static::creating(function($query){
            if(\Auth::check()){
                $query->created_by = @\Auth::user()->id;
            }
        });
        static::updating(function($query){
            if(\Auth::check()){
                $query->updated_by = @\Auth::user()->id;
            }
        });
    }
}
