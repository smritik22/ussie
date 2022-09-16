<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RideDetails extends Model
{
    use HasFactory;

     protected $table = "ride_detail";
    protected $guard = 'web';

    protected $fillable = [
        'user_id', 
        'driver_id', 
        'ride_type_id', 
        'vehicle_type_id',
        'start_date',
        'end_date',
        'complated_date',
        'ride_status',
        'payment_mode',
        'ride_km',
        'estimate_fare',
        'total_amount',
        'deduction_amount',
        'ride_time',
        'waiting_timer',
        'map_image',
        'estimated_distance',
        'estimated_duration',
        'pickup_lat', 
        'pickup_long',
        'pickup_area',
        'pickup_address',
        'dest_lat',
        'dest_long',
        'dest_area',
        'dest_address',
        'actual_fare',
        'actual_base',
        'actual_distance',
        'expired_date',
        'status',
        'created_date',
        'updated_date',
        'updated_at',
    ];
}
