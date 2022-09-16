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

class UserFavouriteProperty extends Model
{
    use HasFactory;
    protected $table = 'tbl_favourite_property';
    protected $fillable = [
        'user_id',
        'property_id'
    ];

    public function UserDetails(){
        return $this->belongsTo(MainUsers::class,'user_id','id');
    }

    public function PropertyDetails(){
        return $this->belongsTo(Property::class,'property_id','id');
    }
}
