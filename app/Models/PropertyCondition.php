<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyCondition extends Model
{
    use HasFactory;
    protected $table = 'property_conditions';

    protected $fillable = [
    	'language_id',
        'parent_id',
        'condition_text',
    ];
    
    public function childdata()
    {
        return $this->hasMany(PropertyCondition::class,'parent_id','id');
    }
}
