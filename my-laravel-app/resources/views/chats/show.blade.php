<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
<div class="flex min-h-screen items-center justify-center p-4">

    <div class="w-full max-w-2xl bg-white rounded-xl shadow-md border border-gray-100 flex flex-col h-[80vh]">
        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 rounded-t-xl">
            <div>
                <h1 class="text-lg font-bold text-gray-800">
                    @if($chat->type === 'group')
                        {{ $chat->title }}
                    @else
                        {{ $chat->users->where('id', '!=', Auth::id())->first()?->name ?? 'Private Chat' }}
                    @endif
                </h1>
                <p class="text-xs text-gray-400 capitalize">{{ $chat->type }} chat</p>
            </div>
            <a href="{{ route('chats.index') }}" class="text-xs font-semibold text-gray-500
            hover:text-gray-700 transition">
                Back to List
            </a>
        </div>

        @if (session('success'))
            <div class="mx-4 mt-3 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mx-4 mt-3 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div id="messages-container" class="flex-1 overflow-y-auto p-4 space-y-4">

            <div class="flex justify-center pb-2">
                {{ $messages->links() }}
            </div>

            @forelse($messages->reverse() as $message)
                @if($message->type === 'system')
                    <div class="flex justify-center">
                        <span class="bg-gray-100 text-gray-500 text-xs px-3 py-1 rounded-full border
                        border-gray-200 shadow-sm font-medium">
                            {{ $message->body }}
                        </span>
                    </div>
                @else
                    <div id="msg-container-{{ $message->id }}"
                         class="flex flex-col {{ $message->user_id === Auth::id() ? 'items-end' : 'items-start' }}">
                        <div class="max-w-[75%] rounded-2xl px-4 py-2.5 shadow-sm border
                            {{ $message->user_id === Auth::id()
                                ? 'bg-gray-600 border-gray-600 text-white rounded-br-none'
                                : 'bg-gray-100 border-gray-200 text-gray-800 rounded-bl-none' }}">

                            @if($message->user_id !== Auth::id())
                                <span class="block text-xs font-bold mb-1 opacity-75">
                                    {{ $message->user->name ?? 'Deleted User' }}
                                </span>
                            @endif

                            <p id="msg-body-{{ $message->id }}" class="text-sm break-words whitespace-pre-wrap leading-relaxed">
                                {{ $message->body }}
                            </p>

                            <div class="flex items-center justify-end gap-1 mt-1 opacity-60 text-[10px]">
                                @if($message->updated_at->gt($message->created_at))
                                    <span class="italic">edited</span>
                                    <span>•</span>
                                @endif
                                <span>{{ $message->created_at->format('H:i') }}</span>
                            </div>
                        </div>

                        <div class="flex gap-2 mt-1 px-1 text-[11px]">
                            @if($message->user_id === Auth::id())
                                <button onclick="document.getElementById('edit-box-{{ $message->id }}').classList.toggle('hidden')"
                                        class="text-blue-600 hover:underline cursor-pointer">
                                    Edit
                                </button>
                            @endif

                            @if($message->user_id === Auth::id() || $chat->creator_id === Auth::id())
                                <form action="{{ route('chats.messages.destroy', $message->id) }}" method="POST" onsubmit="return confirm('Delete this message?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline cursor-pointer">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </div>

                        @if($message->user_id === Auth::id())
                            <div id="edit-box-{{ $message->id }}" class="hidden w-full max-w-[75%] mt-2 p-2 bg-gray-50 border border-gray-200 rounded-lg">
                                <form action="{{ route('chats.messages.update', $message->id) }}" method="POST" class="m-0 space-y-2">
                                    @csrf
                                    @method('PUT')
                                    <textarea name="body" required rows="2"
                                              class="block w-full rounded-md border border-gray-300 bg-white px-2 py-1 text-xs text-gray-900 outline-none focus:border-blue-500">{{ $message->body }}</textarea>
                                    <div class="flex justify-end gap-2 text-[10px]">
                                        <button type="button" onclick="document.getElementById('edit-box-{{ $message->id }}').classList.add('hidden')"
                                                class="rounded border border-gray-300 bg-white px-2 py-1 text-gray-600">Cancel</button>
                                        <button type="submit" class="rounded bg-gray-600 text-white px-2 py-1 font-medium">Save</button>
                                    </div>
                                </form>
                            </div>
                        @endif

                    </div>
                @endif
            @empty
                <p class="text-sm text-gray-400 italic text-center py-12">No messages in this chat yet.</p>
            @endforelse

        </div>

        <div class="p-4 border-t border-gray-100 bg-gray-50/50 rounded-b-xl">
            <form action="{{ route('chats.messages.store', $chat->id) }}" method="POST" class="m-0 flex gap-2 items-end">
                @csrf
                <div class="flex-1">
                    <textarea name="body" required rows="1" placeholder="Write a message..."
                              class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 resize-none h-10"></textarea>
                </div>
                <button type="submit" class="rounded-lg bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 text-sm font-semibold transition shadow-sm h-10 cursor-pointer flex items-center justify-center">
                    Send
                </button>
            </form>
            @error('body')
            <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
            @enderror
        </div>

    </div>

</div>


<script type="module">
    document.addEventListener('DOMContentLoaded', function () {
        const chatId = "{{ $chat->id }}";
        const currentUserId = parseInt("{{ Auth::id() }}");
        const container = document.getElementById('messages-container');

        window.Echo.private(`chat.${chatId}`)
            .listen('Chats\\MessageSent', (data) => {
                if (parseInt(data.user_id) === currentUserId) {
                    return;
                }
                const messageHtml = `
                    <div id="msg-container-${data.id}" class="flex flex-col items-start animate-fade-in">
                        <div class="max-w-[75%] rounded-2xl px-4 py-2.5 shadow-sm border bg-gray-100 border-gray-200 text-gray-800 rounded-bl-none">
                            <span class="block text-xs font-bold mb-1 opacity-75">
                                ${data.user_name}
                            </span>
                            <p id="msg-body-${data.id}" class="text-sm break-words whitespace-pre-wrap leading-relaxed">${data.body}</p>
                            <div id="msg-meta-${data.id}" class="flex items-center justify-end gap-1 mt-1 opacity-60 text-[10px]">
                                <span>${data.time}</span>
                            </div>
                        </div>
                    </div>
                `;

                container.insertAdjacentHTML('beforeend', messageHtml);
                container.scrollTop = container.scrollHeight;
            })
            .listen('Chats\\MessageUpdated', (data) => {
                const msgElement = document.getElementById(`msg-body-${data.id}`);

                if (msgElement) {
                    msgElement.innerText = data.body;
                    const metaContainer = document.getElementById(`msg-meta-${data.id}`) || msgElement.parentElement;
                    if (metaContainer && !metaContainer.innerText.includes('edited')) {
                        const editedSpan = document.createElement('span');
                        editedSpan.className = 'text-[10px] text-gray-400 italic';
                        editedSpan.innerText = 'edited • ';
                        metaContainer.insertBefore(editedSpan, metaContainer.firstChild);
                    }
                }
            })
            .listen('Chats\\MessageDeleted', (data) => {
                const msgContainer = document.getElementById(`msg-container-${data.id}`);
                if (msgContainer) {
                    msgContainer.remove();
                }
            });
    });
</script>
</body>
</html>


