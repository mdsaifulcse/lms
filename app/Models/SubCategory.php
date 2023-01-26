<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCategory extends Model
{
    use HasFactory,SoftDeletes;
    const ACTIVE=1;
    const INACTIVE=0;

    const YES=1;
    const NO=0;
    protected $table='sub_categories';
    protected $fillable=['category_id','name','description','icon_photo','sequence','status',];

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id','id');
    }
}
