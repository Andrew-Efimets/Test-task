<?php

namespace App\Http\Resources\Chats;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $displayTitle = $this->type === 'group'
            ? $this->title
            : $this->users->where('id', '!=', Auth::id())->first()?->name ?? 'Private Chat';

        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $displayTitle,
            'creator_id' => $this->creator_id,
            'latest_message' => $this->latestMessage ? [
                'body' => $this->latestMessage->body,
                'sender_name' => $this->latestMessage->user->name ?? 'System',
                'time' => $this->latestMessage->created_at->format('H:i'),
            ] : null,
        ];
    }
}
