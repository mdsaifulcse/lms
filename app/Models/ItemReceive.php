<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemReceive extends Model
{
    use HasFactory,SoftDeletes;
    const RECEIVENOLENGTH=6;
    const PAID=1;
    const UNPAID=2;
    const DUE=3;

    protected $table='item_receives';
    protected $fillable=['receive_no','item_order_id','vendor_id','qty','invoice_no','invoice_photo','payment_status','payable_amount','paid_amount',
        'due_amount','received_date','comments','created_by','updated_by'];

    public function itemReceiveDetails(){
        return $this->hasMany(ItemReceiveDetail::class,'item_receive_id','id');
    }
    public function itemReceivePayments(){
        return $this->hasMany(VendorPayment::class,'item_receive_id','id')->oldest();
    }
    public function vendor(){
        return $this->belongsTo(Vendor::class,'vendor_id','id');
    }
    public function itemOrder(){
        return $this->belongsTo(ItemOrder::class,'item_order_id','id');
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
