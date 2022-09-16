<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    protected $table = 'labels';

    protected $fillable = [
    	'language_id','Label_key','Label_value','parentid','status'
    ];
    
    public function childdata()
    {
        return $this->hasMany('App\Models\Label','parentid','id');
    }
}
