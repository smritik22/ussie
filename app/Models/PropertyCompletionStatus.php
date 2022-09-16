<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyCompletionStatus extends Model
{
    use HasFactory;
    protected $table = 'property_completion_statuses';

    protected $fillable = [
    	'language_id',
        'parent_id',
        'completion_type',
    ];
    
    public function childdata()
    {
        return $this->hasMany(PropertyCompletionStatus::class,'parent_id','id');
    }
}
