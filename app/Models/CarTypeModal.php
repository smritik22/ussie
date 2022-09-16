<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarTypeModal extends Model
{
    use HasFactory;

    protected $table = "car_type";
    protected $guard = 'web';

    protected $fillable = [
        'car_type', 
        'description', 
        'image', 
        'base_fare', 
        'per_km_charge', 
        'per_km_charge_pool', 
        'status', 
        'created_at', 
        'updated_at',
    ];
}
