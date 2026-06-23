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
    <div class="w-full max-w-md space-y-6 rounded-xl bg-white p-8 shadow-md border
        border-gray-100 text-center">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">Verify your Email</h1>
        <p class="text-sm text-gray-600 leading-relaxed">
            Check the link in your email.
        </p>

        <form action="{{ route('verification.send') }}" method="POST" class="pt-2">
            @csrf
            <button type="submit"
                    class="inline-flex items-center justify-center
                        rounded-lg bg-gray-100 hover:bg-gray-200 text-sm font-semibold
                        text-blue-600 hover:text-blue-700 hover:underline px-5 py-2.5
                        transition cursor-pointer w-full sm:w-auto">
                Send again
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
