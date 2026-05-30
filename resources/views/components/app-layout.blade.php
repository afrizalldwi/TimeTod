<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'TimeTod') . ' - Productivity Timer')</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FFF8E8] text-black font-sans">
    <div class="min-h-screen flex flex-col">
        <header class="border-b-4 border-black bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16 sm:h-20">
                    <a href="{{ route('dashboard') }}" class="text-2xl sm:text-3xl font-black tracking-tight">
                        <span class="bg-black text-[#FFF8E8] px-2 py-0.5">Time</span><span class="text-black">Tod</span>
                    </a>

                    <div class="flex items-center gap-3 sm:gap-4">
                        <button id="darkModeToggle"
                            class="btn-neubrutalism w-10 h-10 flex items-center justify-center font-bold text-sm bg-white">
                            <span class="dark-hidden">☀️</span>
                            <span class="dark-block hidden">🌙</span>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 max-w-6xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            {{ $slot }}
        </main>
    </div>

    @stack('scripts')
</body>
</html>
