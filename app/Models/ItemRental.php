<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemRental extends Model
{
    use HasFactory,SoftDeletes;
    const RENTALNOLENGTH=6;
    const RENTAL=0;
    const RETURNBACK=1;
    const OVERDUE=2;

    const NOAMOUNT=0;
    const PAID=1;
    const DUE=2;
    protected $table='item_rentals';
    protected $fillable=['rental_no','rental_date','return_date','qty','user_id','amount_of_penalty',
        'penalty_status','note','status','created_by','updated_by'];

    public function itemRentalDetails(){
        return $this->hasMany(ItemRentalDetail::class,'item_rental_id','id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
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
