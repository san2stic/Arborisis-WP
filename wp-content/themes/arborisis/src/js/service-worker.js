/**
 * Service Worker for PWA
 * Handles offline caching and background sync
 */

const CACHE_NAME = 'arborisis-v1';
const RUNTIME_CACHE = 'arborisis-runtime';

// Static assets to pre-cache
const PRECACHE_URLS = [
    '/',
    '/explore',
    '/map',
    '/graph',
    '/wp-content/themes/arborisis/dist/main.css',
    '/wp-content/themes/arborisis/dist/main.js',
    '/wp-content/themes/arborisis/assets/logo.svg',
];

// Install event - pre-cache static assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches
            .open(CACHE_NAME)
            .then((cache) => cache.addAll(PRECACHE_URLS))
            .then(() => self.skipWaiting())
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches
            .keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames
                        .filter((name) => name !== CACHE_NAME && name !== RUNTIME_CACHE)
                        .map((name) => caches.delete(name))
                );
            })
            .then(() => self.clients.claim())
    );
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', (event) => {
    // Skip cross-origin requests
    if (!event.request.url.startsWith(self.location.origin)) {
        return;
    }

    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            if (cachedResponse) {
                return cachedResponse;
            }

            return fetch(event.request).then((response) => {
                // Don't cache non-successful responses
                if (!response || response.status !== 200 || response.type !== 'basic') {
                    return response;
                }

                // Cache API responses and assets
                if (
                    event.request.url.includes('/wp-json/') ||
                    event.request.url.includes('/wp-content/')
                ) {
                    const responseToCache = response.clone();
                    caches.open(RUNTIME_CACHE).then((cache) => {
                        cache.put(event.request, responseToCache);
                    });
                }

                return response;
            });
        })
    );
});

// Background sync for offline actions
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-plays') {
        event.waitUntil(syncPlays());
    }
});

// Sync play counts when back online
async function syncPlays() {
    const db = await openDB();
    const plays = await db.getAll('pending-plays');

    for (const play of plays) {
        try {
            await fetch('/wp-json/arborisis/v1/sounds/' + play.soundId + '/play', {
                method: 'POST',
            });
            await db.delete('pending-plays', play.id);
        } catch (error) {
            console.error('Failed to sync play:', error);
        }
    }
}

// IndexedDB helper
function openDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('arborisis-db', 1);

        request.onerror = () => reject(request.error);
        request.onsuccess = () => resolve(request.result);

        request.onupgradeneeded = (event) => {
            const db = event.target.result;
            if (!db.objectStoreNames.contains('pending-plays')) {
                db.createObjectStore('pending-plays', { keyPath: 'id', autoIncrement: true });
            }
        };
    });
}
