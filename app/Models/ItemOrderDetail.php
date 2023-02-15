<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemOrderDetail extends Model
{
    use HasFactory,SoftDeletes;

    const ACTIVE=1;
    const INACTIVE=0;

    const YES=1;
    const NO=0;
    protected $table='item_order_details';
    protected $fillable=['item_order_id','item_id','item_qty','item_price'];

    public function item(){
        return $this->belongsTo(Item::class,'item_id','id');
    }
}
