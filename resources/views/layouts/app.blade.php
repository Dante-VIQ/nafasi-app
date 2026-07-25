<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Nafasi — Find Help, Right Now') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @livewireStyles()
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <livewire:navigation.public-nav />

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

<footer class="bg-white border-t mt-12 py-6">
    <div class="max-w-7xl mx-auto px-4 text-center text-sm text-gray-400">
        <div class="space-x-4 mb-2">
            <a href="{{ route('about') }}" class="hover:text-gray-600">About</a>
            <a href="{{ route('privacy') }}" class="hover:text-gray-600">Privacy Policy</a>
            <a href="{{ route('terms') }}" class="hover:text-gray-600">Terms of Use</a>
        </div>
        <p>Nafasi — creating space for help to arrive. &copy; {{ date('Y') }} Nafasi Technologies Ltd.</p>
        <p class="text-xs mt-1">Licensed to county governments, healthcare facilities, and emergency service providers.</p>
    </div>
</footer>
    @livewireScripts()
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(() => console.log('Service Worker registered'))
                .catch((err) => console.error('Service Worker failed', err));
        }
    </script>
</body>

</html>
