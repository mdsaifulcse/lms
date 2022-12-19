<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */


    const REJECTED=0;
    const APPROVED=1;
    const PENDING=2;

    const DEVELOPER=1;
    const SUPERADMIN=2;
    const ADMIN=3;
    const LIBRARIAN=4;
    const GENERALUSER=5;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'status',
        'profile_photo_path',
        'user_role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        //'profile_photo_url',
    ];



    public function userRoles() {
        return [
            self::SUPERADMIN=>'Super Admin',
            self::ADMIN=>'Admin',
            self::LIBRARIAN=>'Librarian',
            self::GENERALUSER=>'General User',
        ];
    }
    public function getApprovalStatusAttribute() {

        if ($this->status==1){
            return 'Approved';
        }elseif($this->status==0){
            return 'Rejected';
        }elseif($this->status==2){
            return 'Pending';
        }
    }

    public function scopeAllUser($q,$userRole=null){
        return $q->whereIn('user_role',$userRole);
    }

    public function scopeActiveUser($q,$userRole){
        return $q->where(['status'=>self::APPROVED])->whereIn('user_role',$userRole);
    }


    public function allGeneralUsers(){
        return $this->where('user_role',self::GENERALUSER)->get();
    }

    public function getIsAdminAttribute(){
         if ($this->user_role==self::ADMIN){
             return true;
         }else{
            return false;
         }
    }
}
