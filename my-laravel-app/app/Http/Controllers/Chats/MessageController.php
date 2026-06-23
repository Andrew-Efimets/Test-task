<?php

namespace App\Http\Controllers\Chats;

use App\Http\Controllers\Controller;
use App\Http\Requests\Chats\StoreMessageRequest;
use App\Services\Chat\MessageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function __construct(
        protected MessageService $messageService
    ) {}

    public function store(StoreMessageRequest $request, int $chatId): RedirectResponse
    {
        try {
            $this->messageService->sendMessage(
                $chatId,
                Auth::id(),
                $request->validated()['body']
            );

            return redirect()->back();

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(StoreMessageRequest $request, int $messageId): RedirectResponse
    {
        try {
            $this->messageService->updateMessage(
                $messageId,
                Auth::id(),
                $request->validated()['body']
            );

            return redirect()->back()->with('success', 'Message updated.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /* SoftDelete */
    public function destroy(int $messageId): RedirectResponse
    {
        try {
            $this->messageService->deleteMessage($messageId, Auth::id());

            return redirect()->back()->with('success', 'Message deleted.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
