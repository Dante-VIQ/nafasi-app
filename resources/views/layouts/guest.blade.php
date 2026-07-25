<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 selection:bg-blue-500 selection:text-white">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:300,400,500,600,700&display=swap" rel="stylesheet" />
    
    <meta name="theme-color" content="#2563eb">
    
    @livewireStyles()
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-slate-800">
    
    <!-- Sophisticated Background Accents -->
    <div class="absolute inset-0 -z-10 h-full w-full bg-slate-50 bg-[linear-gradient(to_right,#e2e8f0_1px,transparent_1px),linear-gradient(to_bottom,#e2e8f0_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)] opacity-40"></div>

    <!-- Main Wrapper -->
    <div class="flex-grow flex flex-col justify-center items-center px-4 sm:px-6 py-4 md:py-2">
        
        <!-- Brand Identity -->
        <div class="mb-8 transform transition-transform duration-300 hover:scale-105">
            <a href="/" wire:navigate class="block focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-xl p-2">
                <x-application-logo class="w-16 h-16 text-blue-600 fill-current filter drop-shadow-sm" />
            </a>
        </div>

        <!-- Auth/Guest Content Container (Card) -->
        <main class="w-full max-w-full sm:max-w-md bg-white/80 backdrop-blur-md border border-slate-200/80 shadow-xl shadow-slate-100/50 rounded-2xl p-6 sm:p-10 transition-all">
            {{ $slot }}
        </main>
    </div>

    <!-- Premium Minimalist Footer -->
    <footer class="mt-auto border-t border-slate-200/60 bg-white/50 backdrop-blur-sm py-8">
        <div class="max-w-5xl mx-auto px-6 flex flex-col items-center space-y-4">
            
            <!-- Navigation Links -->
            <nav class="flex flex-wrap justify-center gap-x-6 gap-y-2 text-sm font-medium text-slate-500">
                <a href="{{ route('about') }}" class="hover:text-blue-600 transition-colors duration-200">About</a>
                <span class="text-slate-300 hidden sm:inline">•</span>
                <a href="{{ route('privacy') }}" class="hover:text-blue-600 transition-colors duration-200">Privacy Policy</a>
                <span class="text-slate-300 hidden sm:inline">•</span>
                <a href="{{ route('terms') }}" class="hover:text-blue-600 transition-colors duration-200">Terms of Use</a>
            </nav>

            <!-- Legal and Compliance Detail -->
            <div class="text-center space-y-1.5 tracking-wide">
                <p class="text-xs font-medium text-slate-600">
                    Nafasi <span class="text-slate-400 font-light">— creating space for help to arrive.</span> &copy; {{ date('Y') }} Nafasi Technologies Ltd.
                </p>
                <p class="text-[10px] uppercase font-semibold tracking-widest text-slate-400 max-w-xl mx-auto leading-relaxed">
                    Licensed to county governments, healthcare facilities, and emergency service providers.
                </p>
            </div>
            
        </div>
    </footer>

    @livewireScripts()

    <!-- PWA Service Worker -->
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(() => console.log('Service Worker registered'))
                .catch((err) => console.error('Service Worker failed', err));
        }
    </script>
</body>

</html>