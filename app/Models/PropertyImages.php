<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyImages extends Model
{
    use HasFactory;
    protected $table = 'property_images';

    protected $fillable = [
        'property_id',
        'property_image',
        'created_at',
        'updated_at'
    ];

    protected $visible = [
        'property_id',
        'property_image',
        'created_at',
        'updated_at'
    ];
}
