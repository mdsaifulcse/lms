<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThirdSubCategory extends Model
{
    use HasFactory,SoftDeletes;
    const ACTIVE=1;
    const INACTIVE=0;

    const YES=1;
    const No=0;

    protected $table='third_sub_categories';
    protected $fillable=['sub_category_id','name','description','icon_photo','sequence','status',];

    public function suCategory(){
        return $this->belongsTo(SubCategory::class,'sub_category_id','id');
    }

}
