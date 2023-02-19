<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemOrder extends Model
{
    use HasFactory,SoftDeletes;
    const INVOICENOLENGTH=6;
    const ACTIVE=1;
    const INACTIVE=0;
    const RECEIVED=1;
    const UNRECEIVED=0;

    const YES=1;
    const NO=0;
    protected $table='item_orders';
    protected $fillable=['order_no','qty','amount','discount','total','tentative_date','vendor_id','note','status','order_status','created_by','updated_by'];

    public function vendor(){
        return $this->belongsTo(Vendor::class,'vendor_id','id');
    }

    public function itemOrderDetails(){
        return $this->hasMany(ItemOrderDetail::class,'item_order_id','id');
    }
    public function orderReceived(){
        return $this->hasMany(ItemReceive::class,'item_order_id','id');
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
