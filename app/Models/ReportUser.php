<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportUser extends Model
{
    use HasFactory;

    protected $table = 'report_user';

    protected $fillable = [
        'user_id',
        'agent_id',
        'uname',
        'email',
        'phone',
        'country_code',
        'report_message'
    ];
    
}
