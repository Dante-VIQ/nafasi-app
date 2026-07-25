const CACHE_NAME = 'nafasi-v2';
const ASSETS_TO_CACHE = [
    '/',
    '/offline',
    '/manifest.json',
    '/build/assets/app-DDIM-JRk.css',   // adjust these paths to match your actual built assets
    '/build/assets/app-CIomGrQN.js',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
];

// Install event – cache each file individually so one failure doesn't break everything
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return Promise.allSettled(
                ASSETS_TO_CACHE.map((url) =>
                    cache.add(url).catch((err) => {
                        console.warn('Failed to cache', url, err);
                    })
                )
            );
        })
    );
});

// Activate event – clean old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.filter((key) => key !== CACHE_NAME)
                    .map((key) => caches.delete(key))
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch event – network first, fallback to cache, then offline page
self.addEventListener('fetch', (event) => {
    // Skip Livewire and API requests
    if (event.request.url.includes('/livewire/') ||
        event.request.url.includes('/api/') ||
        event.request.method !== 'GET') {
        return;
    }

    event.respondWith(
        fetch(event.request)
            .then((response) => {
                // Cache a copy of the successful response
                const clone = response.clone();
                caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
                return response;
            })
            .catch(() => {
                return caches.match(event.request).then((cached) => {
                    return cached || caches.match('/offline');
                });
            })
    );
});