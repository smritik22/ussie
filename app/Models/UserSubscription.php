<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;
    protected $table = 'user_subscriptions';

	protected $fillable = [
        'transaction_id',
        'user_id',
        'plan_id',
        'plan_name',
        'plan_name_ar',
        'plan_description',
        'plan_description_ar',
        'plan_type',
        'no_of_plan_post',
        'is_free_plan',
        'plan_price',
        'plan_duration_value',
        'plan_duration_type',
        'extra_each_normal_post_price',
        'is_featured',
        'no_of_default_featured_post',
        'start_date',
        'end_date',
        'no_of_extra_featured_post',
        'extra_each_featured_post_price',
        'total_price',
    ];

    // protected $visible = [
    //     'user_id',
    //     'subscription_plan_type',
    //     'plan_id',
    //     'property_id',
    //     'start_date',
    //     'end_date',
    //     'status',
    // ];

    public function subscriptionPlanDetails(){
        return $this->belongsTo(SubscriptionPlan::class,'plan_id','id');
    }

    // public function propertyDetails(){
    //     return $this->belongsTo(Property::class,'property_id','id');
    // }

    public function agentDetails(){
        return $this->belongsTo(MainUsers::class,'user_id','id');
    }

    public function propertiesSubscribed(){
        return $this->hasMany(Property::class,'plan_id', 'id');
    }

}
