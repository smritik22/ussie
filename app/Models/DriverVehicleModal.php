<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverVehicleModal extends Model
{
     use HasFactory;
    protected $table = "driver_vehicle_detail";
    protected $guard = 'web';

    protected $fillable = [
        'vehicle_type_id', 
        'driver_id', 
        'car_type_id', 
        'vehicle_model_id', 
        'vehicle_name', 
        'vehicle_number', 
        'vehicle_seat_capacity', 
        'status',
        'created_at',
        'updated_at',
    ];
}
