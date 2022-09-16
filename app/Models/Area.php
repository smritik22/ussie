<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Area extends Model
{
    protected $table = 'area';

	protected $fillable = [
        'name',
        'language_id',
        'parent_id',
        'country_id',
        'governorate_id',
        'image',
        'latitude',
        'longitude',
        'default_range',
        'updated_range',
        'status'
    ];


    public function childdata()
    {
        return $this->hasMany(Area::class,'parent_id','id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class,'country_id','id');
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class,'governorate_id', 'id');
    }

    public function properties(){
        return $this->hasMany(Property::class, 'area_id', 'id');
    }

    // public function getImageAttribute()
    // {
    //     return $this->getAbsoluteImagePath($isUrl = true);
    // }

    // public function getAbsoluteImagePath($isUrl = false)
    // {
    //     if(@$this->image){
    //         $fileName = 'assets/images/area/' . $this->image;
    //         if(Storage::disk('public')->exists($fileName)) {
    //             return Storage::disk('public')->{$isUrl ? 'url' : 'path'}($fileName);
    //         } else {
    //             return null;
    //         }
    //     }else{
    //         return [];
    //     }
    // }
}
