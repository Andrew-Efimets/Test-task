<?php

namespace App\Listeners\Chats;

use App\Events\Chats\ParticipantAdded;
use App\Models\Chats\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateSystemMessageAddedParticipant
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ParticipantAdded $event): void
    {
        Message::create([
            'chat_id' => $event->chat->id,
            'user_id' => null,
            'type'    => 'system',
            'body'    => "User {$event->user->name} add in chat.",
        ]);
    }
}
