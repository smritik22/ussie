<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;
    protected $table = "sub_categories";
    protected $guard = 'web';

    protected $fillable = [
        'category_id', 
        'name',
        'status', 
        'created_at', 
        'updated_at',
    ];
}
