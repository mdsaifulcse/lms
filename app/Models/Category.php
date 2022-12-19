<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory,SoftDeletes;
    const ACTIVE=1;
    const INACTIVE=0;

    const YES=1;
    const NO=0;

    protected $table='categories';
    protected $fillable=['name','description','icon_photo','sequence','status','show_home',];
}
