<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorPayment extends Model
{
    use HasFactory,SoftDeletes;
    const PAYMENTNOLENGTH=6;
    const CASH=1;
    const BANK=2;
    const CHECK=3;

    protected $table='vendor_payments';
    protected $fillable=['vendor_payment_no','item_receive_id','vendor_id','paid_amount','total_last_due_amount','payment_date','payment_photo','payment_through','comments','created_by','updated_by'];

    public function vendor(){
        return $this->belongsTo(Vendor::class,'vendor_id','id');
    }

    public function itemReceive(){
        return $this->belongsTo(ItemReceive::class,'item_receive_id','id');
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
