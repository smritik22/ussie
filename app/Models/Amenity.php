<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    protected $table = 'amenity';

	protected $fillable = [
        'amenity_name',
        'language_id',
        'parent_id',
        'image',
        'status'
    ];


    public function childdata()
    {
        return $this->hasMany(Amenity::class,'parent_id','id');
    }

    public function properties()
    {
        return $this->belongsToMany(Property::class);
    }


}
