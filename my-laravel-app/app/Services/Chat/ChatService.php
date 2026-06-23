<?php

namespace App\Services\Chat;

use App\Events\Chats\GroupChatCreated;
use App\Events\Chats\ParticipantAdded;
use App\Models\Chats\Chat;
use App\Models\Chats\Message;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ChatService
{
    public function getOrCreatePrivateChat(int $userId, int $recipientId): Chat
    {
        if ($userId === $recipientId) {
            throw new \Exception("You can't chat with yourself!");
        }

        $existingChatId = DB::table('chat_participants')
            ->join(
                'chats',
                'chat_participants.chat_id',
                '=',
                'chats.id')
            ->where('chats.type', 'private')
            ->whereIn('chat_participants.user_id', [$userId, $recipientId])
            ->groupBy('chat_participants.chat_id')
            ->havingRaw('COUNT(DISTINCT chat_participants.user_id) = 2')
            ->value('chat_participants.chat_id');

        if ($existingChatId) {
            return Chat::findOrFail($existingChatId);
        }

        return DB::transaction(function () use ($userId, $recipientId) {
            $chat = Chat::create([
                'type' => 'private',
                'creator_id' => $userId,
            ]);

            $chat->participants()->createMany([
                ['user_id' => $userId, 'role' => 'member', 'last_read_at' => now()],
                ['user_id' => $recipientId, 'role' => 'member', 'last_read_at' => null],
            ]);

            return $chat;
        });
    }

    public function createGroupChat(int $creatorId, string $title): Chat
    {
        $creator = User::findOrFail($creatorId);

        $chat = DB::transaction(function () use ($creatorId, $title) {
            $chat = Chat::create([
                'type' => 'group',
                'title' => trim($title),
                'creator_id' => $creatorId,
            ]);

            $chat->participants()->create([
                'user_id' => $creatorId,
                'role' => 'owner',
                'last_read_at' => now(),
            ]);

            return $chat;
        });

        event(new GroupChatCreated($chat, $creator));

        return $chat;
    }

    public function addParticipant(int $chatId, int $userId): void
    {
        $chat = Chat::findOrFail($chatId);

        if ($chat->type === 'private') {
            throw new \Exception('Cannot create private group chat!');
        }

        $alreadyParticipant = $chat->participants()->where('user_id', $userId)->exists();

        if ($alreadyParticipant) {
            throw new \Exception('User is already a member of this group!');
        }

        $user = User::findOrFail($userId);

        DB::transaction(function () use ($chat, $user) {
            $chat->participants()->create([
                'user_id' => $user->id,
                'role' => 'member',
                'last_read_at' => null,
            ]);

            event(new ParticipantAdded($chat, $user));
        });
    }

    /* Soft Delete */
    public function deleteChat(int $chatId, int $userId): void
    {
        $chat = Chat::findOrFail($chatId);

        if ($chat->type === 'group') {
            if ($chat->creator_id !== $userId) {
                throw new \Exception('You are mot an owner.');
            }
        } else {
            $isParticipant = $chat->participants()->where('user_id', $userId)->exists();
            if (!$isParticipant) {
                throw new \Exception('You are not a participant.');
            }
        }

        $chat->delete();
    }
}
