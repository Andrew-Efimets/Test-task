<?php

namespace App\Http\Controllers\Chats;

use App\Http\Controllers\Controller;
use App\Http\Requests\Chats\CreateGroupChatRequest;
use App\Http\Requests\Chats\ParticipantRequest;
use App\Http\Resources\Chats\ChatResource;
use App\Models\Chats\Chat;
use App\Models\User;
use App\Services\Chat\ChatService;
use App\Services\Chat\MessageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function __construct(
        protected ChatService $chatService,
        protected MessageService $messageService
    ) {}

    public function index(): View
    {
        $authUser = Auth::user();

        $chatModels = $authUser->chats()
            ->with(['users:id,name', 'latestMessage.user:id,name'])
            ->get()
            ->sortByDesc(function ($chat) {
                return $chat->latestMessage ? $chat->latestMessage->created_at : $chat->created_at;
            });
        $chats = ChatResource::collection($chatModels)->resolve();
        $users = User::select('id', 'name', 'email')->get();

        return view('chats.index', compact('chats', 'users'));
    }

    public function show(Chat $chat): View|RedirectResponse
    {
        $isParticipant = $chat->participants()->where('user_id', Auth::id())->exists();

        if (!$isParticipant) {
            return redirect()->back()->with('error', 'Access denied');
        }

        $messages = $chat->messages()
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('chats.show', compact('chat', 'messages'));
    }

    public function storePrivate(ParticipantRequest $request): RedirectResponse
    {
        try {
            $chat = $this->chatService->getOrCreatePrivateChat(
                Auth::id(),
                $request->validated()['user_id']
            );

            return redirect()->route('chats.show', $chat->id)
                ->with('success', 'Chat created successfully');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function storeGroup(CreateGroupChatRequest $request): RedirectResponse
    {
        try {
            $chat = $this->chatService->createGroupChat(
                Auth::id(),
                $request->validated()['title']
            );

            return redirect()->route('chats.show', $chat->id)
                ->with('success', 'Group created successfully');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Cannot create a group: ' . $e->getMessage());
        }
    }

    public function addParticipant(ParticipantRequest $request, int $chatId): RedirectResponse
    {
        try {
            $this->chatService->addParticipant(
                $chatId,
                $request->validated()['user_id']
            );

            return redirect()->route('chats.show', $chatId)
                ->with('success', 'Participant added successfully');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /* SoftDelete */
    public function destroyChat(int $chatId): RedirectResponse
    {
        try {
            $this->chatService->deleteChat($chatId, Auth::id());

            return redirect()->route('chats.index')
                ->with('success', 'Chat deleted.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
