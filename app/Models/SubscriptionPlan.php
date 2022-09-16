<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;
    protected $table = 'subscription_plans';

    protected $fillable = [
    	'language_id',
        'parent_id',
        'plan_name',
        'plan_description',
        'plan_type',
        'no_of_plan_post',
        'is_free_plan',
        'plan_price',
        'plan_duration_value',
        'plan_duration_type',
        'extra_each_normal_post_price',
        'is_featured',
        'no_of_default_featured_post',
        'bg_color',
        'status',
    ];
    
    public function childdata()
    {
        return $this->hasMany(SubscriptionPlan::class,'parent_id','id');
    }

    public function planSubscriptions(){
        return $this->hasMany(UserSubscription::class, 'plan_id','id');
        // return $this->belongsToMany(UserSubscription::class,'user_subscriptions','plan_id','id');
    }
}
