<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory,SoftDeletes;

    const ACTIVE=1;
    const INACTIVE=0;

    const YES=1;
    const NO=0;
    protected $table='vendors';
    protected $fillable=['name','email','mobile','photo','total_due','balance','contact_person','contact_person_mobile','office_address',
        'warehouse_address','primary_supply_products','sequence','status','created_by','updated_by'];

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
