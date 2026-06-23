<?php

namespace App\Models\Chats;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatParticipant extends Model
{
    protected $table = 'chat_participants';

    protected $fillable = [
        'chat_id',
        'user_id',
        'role',
        'last_read_at'
    ];

    protected $casts = [
        'last_read_at' => 'datetime',
    ];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class, 'chat_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
