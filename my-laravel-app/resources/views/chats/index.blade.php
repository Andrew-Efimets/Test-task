<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="flex min-h-screen bg-gray-50 text-gray-900">

    <div class="w-1/2 border-r border-gray-200 bg-white p-6 space-y-6">

        <div class="border-b pb-3 flex justify-between items-center">
            <h2 class="text-xl font-bold tracking-tight text-gray-800">
                Chats
            </h2>
            <button onclick="document.getElementById('create-group-form').classList.toggle('hidden')"
                    class="rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs
                    font-semibold px-3 py-1.5 transition cursor-pointer">
                ＋ Create group
            </button>
        </div>

        @if (session('success'))
            <div class="p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div id="create-group-form" class="hidden p-4 bg-gray-50 border border-gray-200 rounded-xl
        transition animate-fade-in">
            <form action="{{ route('chats.group.store') }}" method="POST" class="m-0 space-y-3">
                @csrf
                <div>
                    <label for="group_title" class="block text-xs font-medium text-gray-600 mb-1">
                        Title of group
                    </label>
                    <input type="text" name="title" id="group_title"
                           required minlength="3" maxlength="255"
                           class="block w-full rounded-lg border border-gray-300 bg-white
                           x-3 py-1.5 text-sm text-gray-900 placeholder-gray-400 outline-none
                           transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                           placeholder="Group">
                </div>

                <div class="flex justify-end gap-2 text-xs">
                    <button type="button"
                            onclick="document.getElementById('create-group-form').classList.add('hidden')"
                            class="rounded-lg border border-gray-300 bg-white hover:bg-gray-50
                            text-gray-700 px-3 py-1.5 font-medium transition cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit"
                            class="rounded-lg bg-gray-600 hover:bg-gray-700 text-white px-3 py-1.5
                            font-medium transition shadow-sm cursor-pointer">
                        Create
                    </button>
                </div>
            </form>
        </div>

        <div class="space-y-3">
            @forelse($chats as $chat)
                <div class="relative group">
                    <a href="{{ route('chats.show', $chat['id']) }}"
                       class="block p-4 rounded-xl border border-gray-100 hover:border-blue-200 hover:bg-blue-50/30 transition shadow-sm pr-12">

                        <div class="flex justify-between items-start mb-1">
                            <span class="font-semibold text-base text-gray-900">
                        {{ $chat['title'] }}
                    </span>
                            <span class="text-xs text-gray-400">
                        {{ $chat['latest_message']['time'] ?? '' }}
                    </span>
                        </div>

                        <p class="text-sm text-gray-500 truncate">
                            @if($chat['latest_message'])
                                <span class="font-medium text-gray-600">
                            {{ $chat['latest_message']['sender_name'] }}:
                        </span>
                                {{ $chat['latest_message']['body'] }}
                            @else
                                <span class="italic text-gray-400">
                            No messages...
                        </span>
                            @endif
                        </p>
                    </a>

                    @if($chat['type'] !== 'group' || $chat['creator_id'] === Auth::id())
                        <form action="{{ route('chats.destroy', $chat['id']) }}"
                              method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this chat?')"
                              class="absolute right-3 top-1/2 -translate-y-1/2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="event.stopPropagation()"
                                    class="p-1.5 rounded-md hover:bg-red-50 text-gray-400 hover:text-red-500 cursor-pointer transition">
                                ✕
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <p class="text-sm text-gray-400 italic text-center py-6">
                    No chats...
                </p>
            @endforelse
        </div>

    </div>

    <div class="w-1/2 bg-gray-50 p-6 space-y-6">
        <h2 class="text-xl font-bold tracking-tight text-gray-800 border-b pb-3">
            Users
        </h2>

        <div class="space-y-3">
            @foreach($users as $user)
                @if($user->id !== Auth::id())
                    <div class="flex items-center justify-between p-4 bg-white
                    rounded-xl border border-gray-200 shadow-sm">
                        <div>
                            <div class="font-semibold text-gray-900">
                                {{ $user->name }}
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ $user->email }}
                            </div>
                        </div>

                        <div class="flex gap-2 items-center">
                            <form action="{{ route('chats.private.store') }}"
                                  method="POST" class="m-0">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                <button type="submit" class="rounded-lg bg-gray-600
                                hover:bg-gray-700 text-white text-xs font-medium px-3
                                py-2 transition cursor-pointer h-9">
                                    Write
                                </button>
                            </form>

                            @php
                                $groupChats = array_filter($chats, function($c) {
                                    return $c['type'] === 'group';
                                });
                            @endphp

                            @if(!empty($groupChats))
                                <form action=""
                                      method="POST"
                                      onsubmit="this.action='/chats/' + this.chat_id.value + '/participants'"
                                      class="flex gap-1 m-0 items-center">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">

                                    <select name="chat_id" required class="rounded-lg border border-gray-300 px-2 py-1 text-xs bg-white h-9 outline-none focus:border-blue-500">
                                        <option value="" disabled selected>
                                            Select group...
                                        </option>
                                        @foreach($groupChats as $groupChat)
                                            <option value="{{ $groupChat['id'] }}">
                                                {{ $groupChat['title'] }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <button type="submit" class="rounded-lg bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-600 text-xs font-medium px-2 py-2 h-9 transition cursor-pointer">
                                        ＋
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
</body>
</html>
