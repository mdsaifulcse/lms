<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorPayment extends Model
{
    use HasFactory,SoftDeletes;

    protected $table='vendor_payments';
    protected $fillable=['item_receive_id','paid_amount','due_amount','comments','created_by','updated_by'];
}
