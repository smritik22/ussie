<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    protected $table = 'governorate';

	protected $fillable = [
        'name',
        'language_id',
        'parent_id',
        'country_id',
        'status'
    ];


    public function childdata()
    {
        return $this->hasMany(Governorate::class,'parent_id','id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class,'country_id','id','countries.id=governorate.country_id');
    }
}
