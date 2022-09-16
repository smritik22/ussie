<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleModal extends Model
{
    use HasFactory;

    protected $table = "vehicle_model";
    protected $guard = 'web';

    protected $fillable = [
        'vehicle_type_id', 
        'name',
        'status', 
        'created_at', 
        'updated_at',
    ];
}
