<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressModal extends Model
{
    use HasFactory;
    protected $table = "tbl_address_customer";
    protected $guard = 'web';

    protected $fillable = [
        'user_id', 
        'title', 
        'address', 
        'latitude', 
        'longitude', 
        'status', 
        'created_at', 
        'updated_at',
    ];
}
