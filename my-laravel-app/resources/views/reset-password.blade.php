<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @fonts

    <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="flex flex-col gap-4 justify-center items-center my-10">
    <div class="w-full max-w-md space-y-6 rounded-xl bg-white p-8 shadow-md border border-gray-100">
        <h2 class="text-center text-2xl font-bold tracking-tight text-gray-900">
            Reset password
        </h2>
        <p class="text-sm text-center text-gray-600">
            Come up with a complex password.
        </p>
        <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">
            <div class="space-y-1">
                <label class="block text-sm font-medium text-gray-700">New password</label>
                <input type="password" name="password" required
                       class="block w-full rounded-lg border border-gray-300 px-3 py-2
                           text-gray-900 placeholder-gray-400 outline-none transition
                           focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                       placeholder="••••••••">
            </div>
            <div class="space-y-1">
                <label class="block text-sm font-medium text-gray-700">
                    Confirm password
                </label>
                <input type="password" name="password_confirmation" required
                       class="block w-full rounded-lg border border-gray-300 px-3
                           py-2 text-gray-900 placeholder-gray-400 outline-none
                           transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                       placeholder="••••••••">
            </div>

            <button type="submit"
                    class="flex w-full justify-center rounded-lg bg-gray-600 py-2.5
                        px-4 text-sm font-semibold text-white shadow-sm transition
                        hover:bg-gray-700 focus-visible:outline focus-visible:outline-2
                        focus-visible:outline-offset-2 focus-visible:outline-gray-600 cursor-pointer">
                Reset password
            </button>
        </form>

    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-sm text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

</div>
</body>
</html>
