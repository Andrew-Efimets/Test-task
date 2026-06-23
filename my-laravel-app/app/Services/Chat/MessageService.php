<?php

namespace App\Services\Chat;

use App\Events\Chats\MessageDeleted;
use App\Events\Chats\MessageSent;
use App\Events\Chats\MessageUpdated;
use App\Models\Chats\Chat;
use App\Models\Chats\Message;
use Illuminate\Support\Facades\DB;

class MessageService
{
    /**
     * Отправить сообщение в чат.
     */
    public function sendMessage(int $chatId, int $userId, string $body): Message
    {
        $chat = Chat::findOrFail($chatId);

        $isParticipant = $chat->participants()->where('user_id', $userId)->exists();

        if (!$isParticipant) {
            throw new \Exception('You are not a participant.');
        }

        $message = DB::transaction(function () use ($chat, $userId, $body) {

            $message = Message::create([
                'chat_id' => $chat->id,
                'user_id' => $userId,
                'type'    => 'text',
                'body'    => trim($body),
            ]);

            $chat->participants()
                ->where('user_id', $userId)
                ->update(['last_read_at' => now()]);

            return $message;
        });

        event(new MessageSent($message));

        return $message;
    }

    /**
     * Редактировать текст сообщения.
     */
    public function updateMessage(int $messageId, int $userId, string $newBody): Message
    {
        $message = Message::findOrFail($messageId);

        if ($message->user_id !== $userId) {
            throw new \Exception('You are not the author.');
        }

        $message->update([
            'body' => trim($newBody),
        ]);

        event(new MessageUpdated($message));

        return $message;
    }

    /**
     * Удалить сообщение.
     */
    public function deleteMessage(int $messageId, int $userId): void
    {
        $message = Message::findOrFail($messageId);
        $chat = $message->chat;

        $isAuthor = $message->user_id === $userId;

        $isChatOwner = $chat->participants()
            ->where('user_id', $userId)
            ->where('role', 'owner')
            ->exists();

        if (!$isAuthor && !$isChatOwner) {
            throw new \Exception('No rights to delete messages.');
        }

        $chatId = $message->chat_id;

        $message->delete();

        event(new MessageDeleted($messageId, $chatId));
    }
}
