<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverDocumentModal extends Model
{
    use HasFactory;
    protected $table = "driver_verification_document";
    protected $guard = 'web';

    protected $fillable = [
        'user_id', 
        'document_type', 
        'document_image', 
        'is_approve', 
        'status', 
        'created_at', 
        'updated_at',
    ];
}
