<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BathroomTypes extends Model
{
    use HasFactory;
    protected $table = 'bathroom_types';

    protected $fillable = [
    	'language_id',
        'parent_id',
        'type',
    ];

    protected $visible = [
        'language_id',
        'parent_id',
        'type',
    ];
    
    public function childdata()
    {
        return $this->hasMany(BathroomTypes::class,'parent_id','id');
    }
}
