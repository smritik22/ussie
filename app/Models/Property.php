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


class Property extends Model
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $table = 'properties';

    protected $fillable = [
        
        'agent_id',
        'property_id',
        'property_name',
        'slug',
        'property_description',
        'property_for',
        'property_type',
        'area_id',
        'property_address',
        'property_address_latitude',
        'property_address_longitude',
        'property_amenities_ids',
        'bedroom_type',
        'total_bedrooms',
        'bathroom_type',
        'total_bathrooms',
        'total_toilets',
        'property_sqft_area',
        'base_price',
        'price_area_wise',
        'condition_type_id',
        'completion_status_id',
        'property_subscription_enddate',
        'plan_id',
        'status',
        'is_approved',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'ip_address',
    ];

    // protected $visible = [
    //     'agent_id',
    //     'property_id',
    //     'property_name',
    //     'slug',
    //     'property_description',
    //     'property_for',
    //     'property_type',
    //     'area_id',
    //     'property_address',
    //     'property_address_latitude',
    //     'property_address_longitude',
    //     'property_amenities_ids',
    //     'bedroom_type',
    //     'total_bedrooms',
    //     'bathroom_type',
    //     'total_bathrooms',
    //     'total_toilets',
    //     'property_sqft_area',
    //     'base_price',
    //     'price_area_wise',
    //     'condition_type_id',
    //     'completion_status_id',
    //     'property_subscription_enddate',
    //     'plan_id',
    //     'status',
    //     'created_by',
    //     'updated_by',
    //     'created_at',
    //     'updated_at',
    // ];
    
    // protected $appends = ['aminity_data'];

    public function agentDetails()
    {
        return $this->belongsTo(MainUsers::class,'agent_id','id');
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class);
    }

    public function areaDetails(){
        return $this->belongsTo(Area::class,'area_id','id');
    }

    public function propertyTypeDetails(){
        return $this->belongsTo(PropertyType::class,'property_type','id');
    }

    public function propertyFor(){
        return $this->belongsTo(PropertyFor::class,'property_for','id');
    }

    public function propertyCondition(){
        return $this->belongsTo(PropertyCondition::class,'condition_type_id','id');
    }

    public function propertyCompletionStatus(){
        return $this->belongsTo(PropertyCompletionStatus::class,'completion_status_id','id');
    }

    public function favouriteProperty(){
        return $this->hasMany(UserFavouriteProperty::class,'property_id','id');
    }

    public function bedroomTypeDetails(){
        return $this->belongsTo(BedroomTypes::class,'bedroom_type','id');
    }
    
    public function bathroomTypeDetails(){
        return $this->belongsTo(BathroomTypes::class,'bathroom_type','id');
    }

    public function propertyImages(){
        return $this->hasMany(PropertyImages::class,'property_id','id');
    }

    public function subscribedPlanDetails(){
        return $this->belongsTo(UserSubscription::class,'plan_id','id');
    }
    // public function getAmenityListAttribute()
    // {
    //     return $this->amenities->pluck('amenity_name')->implode(',');
    // }

}
