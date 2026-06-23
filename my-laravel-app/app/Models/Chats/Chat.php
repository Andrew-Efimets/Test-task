<?php

namespace App\Models\Chats;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chat extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'type',
        'title',
        'avatar',
        'creator_id'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'chat_participants',
            'chat_id',
            'user_id'
        )->withPivot('role', 'last_read_at')
            ->withTimestamps();
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ChatParticipant::class, 'chat_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'chat_id');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class, 'chat_id')->latestOfMany();
    }
}
