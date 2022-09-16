<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BedroomTypes extends Model
{
    use HasFactory;
    protected $table = 'bedroom_types';

    protected $fillable = [
    	'language_id',
        'parent_id',
        'type',
    ];
    
    public function childdata()
    {
        return $this->hasMany(BedroomTypes::class,'parent_id','id');
    }
}
