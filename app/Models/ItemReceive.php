<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemReceive extends Model
{
    use HasFactory,SoftDeletes;
    const PAID=1;
    const UNPAID=2;
    const DUE=3;

    protected $table='item_receives';
    protected $fillable=['item_order_id','vendor_id','qty','invoice_no','invoice_photo','payment_status','payable_amount','paid_amount',
        'due_amount','comments','created_by','updated_by'];
}
