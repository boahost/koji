// Service Worker
const CACHE_NAME = 'projeto-df-v1';
const INITIAL_CACHED_RESOURCES = [
    '/',
    '/dashboard',
    '/products',
    '/manifest.json',
    '/images/icon-72x72.png',
    '/images/icon-96x96.png',
    '/images/icon-128x128.png',
    '/images/icon-144x144.png',
    '/images/icon-152x152.png',
    '/images/icon-192x192.png',
    '/images/icon-384x384.png',
    '/images/icon-512x512.png',
    '/build/assets/app.css',
    '/build/assets/app.js'
];

// Install event - cache initial resources
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                // Cache what we can, but don't fail if some resources are not available
                return Promise.allSettled(
                    INITIAL_CACHED_RESOURCES.map(resource =>
                        cache.add(resource).catch(error => {
                            console.warn(`Failed to cache ${resource}:`, error);
                            return null;
                        })
                    )
                );
            })
            .then(() => self.skipWaiting())
    );
});

// Activate event - cleanup old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch event - network first, fallback to cache
self.addEventListener('fetch', (event) => {
    // Only handle GET requests
    if (event.request.method !== 'GET') return;

    event.respondWith(
        fetch(event.request)
            .then(response => {
                // Cache successful responses
                if (response.ok) {
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then(cache => {
                        cache.put(event.request, responseClone);
                    });
                }
                return response;
            })
            .catch(() => {
                // Fallback to cache if network fails
                return caches.match(event.request)
                    .then(cachedResponse => {
                        if (cachedResponse) {
                            return cachedResponse;
                        }
                        // If no cache exists, return a basic offline page or error response
                        return new Response('Offline', {
                            status: 503,
                            statusText: 'Service Unavailable'
                        });
                    });
            })
    );
});
