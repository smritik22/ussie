<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturedAddons extends Model
{
    use HasFactory;

    protected $table = 'featured_addons';
    protected $primaryKey = 'id';

    protected $fillable = [
        'no_of_extra_featured_post',
        'extra_each_featured_post_price',
        'status'
    ];


}
