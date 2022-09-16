<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationModal extends Model
{
    use HasFactory;

    protected $table = "notification";
    protected $guard = 'web';

    protected $fillable = [
        'user_id',
        'driver_id',
        'read_status',
        'created_date',
        'updated_date',
    ];
}
