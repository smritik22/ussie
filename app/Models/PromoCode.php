<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    use HasFactory;
    protected $table = "tbl_promocode";
    protected $guard = 'web';

    protected $fillable = [
        'promocode_name',
        'promocode',
        'page_content',
        'promocode_percentage',
        'promocode_image',
        'start_date',
        'end_date', 
        'status', 
        'created_at', 
        'updated_at',
    ];
}
