<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;


class MainUsers extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $table = "customer";
    protected $guard = 'web';
    
    protected $fillable = [
        'user_type', 
        'driver_available', 
        'is_profile_setup', 
        'secure_number',
        'name',
        'email',
        'mobile_number',
        'country_code',
        'country_id',
        'otp',
        'otp_expire_time',
        'is_driver_online',
        'avg_rating',
        'total_rating',
        'optional_number',
        'agent_joined_date',
        'driver_experience',
        'driver_bio',
        'is_driver_approve',
        'customer_image', 
        'token',
        'status',
        'is_phone',
        'device_token',
        'device_id',
        'created_at',
        'updated_at',
        'file_1',
        'file_2',
        'gender',
        'address',
        'hobbies',
        'address_latitude',
        'address_longitude',
    ];

    // protected $visible = [
    //     'full_name', 
    //     'email', 
    //     'country_code', 
    //     'mobile_number',
    //     'otp',
    //     'is_otp_varified',
    //     'otp_expire_time',
    //     'user_type',
    //     'agent_type',
    //     'agent_joined_date',
    //     'profile_image',
    //     'created_by', 
    //     'updated_by'
    // ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

     /**
     * The attributes that should be cast.
     *
     * @var array
     */
   

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
  

    public function properties(){
        return $this->hasMany(Property::class,'agent_id','id');
    }

    public function subscriptionDetails(){
        return $this->hasMany(UserSubscription::class,'user_id','id');
    }
  

    // public function getPropertyAttribute()
    // {
    //     $properties = $this->properties()->getQuery()->orderBy('', 'desc')->get();
    //     return $properties;
    // }
}
