<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemRentalDetail extends Model
{
    use HasFactory,SoftDeletes;

    const RENTAL=0;
    const RETURNBACK=1;
    const OVERDUE=2;

    const NOAMOUNT=0;
    const PAID=1;
    const DUE=2;
    protected $table='item_rental_details';
    protected $fillable=['item_rental_id','item_id','item_qty','return_date','status','item_amount_of_penalty',
        'penalty_status'];

    public function rentalItem(){
        return $this->belongsTo(Item::class,'item_id','id');
    }
}
