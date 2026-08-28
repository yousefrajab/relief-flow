const CACHE_VERSION = 'reliefflow-static-v1';
const RUNTIME_CACHE = 'reliefflow-runtime-v1';

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_VERSION).then((cache) => cache.add('/offline.html'))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => Promise.all(
            keys
                .filter((key) => key !== CACHE_VERSION && key !== RUNTIME_CACHE)
                .map((key) => caches.delete(key))
        ))
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Never intercept non-GET requests (form submissions) — the app's own
    // offline queue (resources/js/offline-queue.js) handles those instead.
    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    // Leave cross-origin requests (map tiles, geocoding, etc.) alone entirely.
    if (url.origin !== self.location.origin) {
        return;
    }

    // Built assets are content-hashed by Vite, so once fetched they never
    // change — safe to cache indefinitely and serve instantly offline.
    if (url.pathname.startsWith('/build/') || url.pathname.startsWith('/icons/')) {
        event.respondWith(
            caches.open(CACHE_VERSION).then((cache) => cache.match(request).then((cached) => {
                if (cached) return cached;

                return fetch(request).then((response) => {
                    cache.put(request, response.clone());
                    return response;
                });
            }))
        );
        return;
    }

    // Page navigations: network-first so data is always fresh when online,
    // falling back to the last cached copy (or the offline page) when not.
    if (request.mode === 'navigate' || request.destination === 'document') {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    caches.open(RUNTIME_CACHE).then((cache) => cache.put(request, response.clone()));
                    return response;
                })
                .catch(() => caches.match(request).then((cached) => cached || caches.match('/offline.html')))
        );
        return;
    }

    // Everything else (JSON polling endpoints, etc.) goes straight to the
    // network untouched, so live data is never served stale from cache.
});
