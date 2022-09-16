<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyType extends Model
{
    use HasFactory;

    protected $table = 'property_types';

    protected $fillable = [
    	'language_id',
        'parent_id',
        'type',
    ];
    
    public function childdata()
    {
        return $this->hasMany(PropertyType::class,'parent_id','id');
    }
}
