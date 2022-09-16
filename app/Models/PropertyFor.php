<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyFor extends Model
{
    use HasFactory;

    protected $table = 'property_for';

    protected $fillable = [
        'id',
        'for_text',
        'for_text_ar'
    ];

    public function properties(){
        return $this->hasMany(Property::class,'id','property_for');
    }
}
