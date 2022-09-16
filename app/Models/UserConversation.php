<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserConversation extends Model
{
    use HasFactory;
    protected $table = 'user_conversation';

	protected $fillable = [
        'from_id',
        'to_id',
        'message',
        'read_status',
        'ip_address'
    ];

    public function senderDetails() {
        return $this->belongsTo(MainUsers::class, 'from_id', 'id');
    }

    public function receiverDetails() {
        return $this->belongsTo(MainUsers::class, 'to_id', 'id');
    }
}
