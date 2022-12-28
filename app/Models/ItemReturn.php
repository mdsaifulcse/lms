<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemReturn extends Model
{
    use HasFactory,SoftDeletes;
    const RETURNNOLENGTH=6;

    protected $table='item_returns';
    protected $fillable=['return_no','item_rental_id','qty','return_date','comments','created_by','updated_by'];

    public function itemReturnDetails(){
        return $this->hasMany(ItemReturnDetail::class,'item_return_id','id');
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
