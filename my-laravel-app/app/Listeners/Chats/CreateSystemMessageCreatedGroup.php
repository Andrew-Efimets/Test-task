<?php

namespace App\Listeners\Chats;

use App\Events\Chats\GroupChatCreated;
use App\Models\Chats\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateSystemMessageCreatedGroup
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
    public function handle(GroupChatCreated $event): void
    {
        Message::create([
            'chat_id' => $event->chat->id,
            'user_id' => null,
            'type'    => 'system',
            'body'    => "Group chat created by {$event->user->name}.",
        ]);
    }
}
