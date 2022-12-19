<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Author extends Model
{
    use HasFactory,SoftDeletes;
    const ACTIVE=1;
    const INACTIVE=0;

    const YES=1;
    const NO=0;
    protected $table='authors';
    protected $fillable=['name','email','mobile','photo','contact','address1','address2','bio','show_home',
        'sequence','status','created_by','updated_by'];

    public function scopeActiveAuthor($q){
        return $q->where(['status'=>self::ACTIVE]);
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
