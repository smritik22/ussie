<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = "tbl_trip_payment";
    protected $guard = 'web';

    protected $fillable = [
        'user_id', 
        'driver_id', 
        'ride_id', 
        'total_amount',
        'deducation',
        'receivable_amount',
        'payment_status',
        'cancel_fee',
        'card_no',
        'charge_id',
        'is_payment_release',
        'payment_release_date',
        'status',
        'created_date',
        'updated_date',
    ];
}
