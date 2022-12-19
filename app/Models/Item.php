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
        'language_id','country_id','category_id','subcategory_id','third_category_id','status','created_by','updated_by'];
}
