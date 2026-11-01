/* Duno keeps a small cache of public static files only. */
const CACHE_PREFIX = 'duno-public-static-';
const CACHE_NAME = `${CACHE_PREFIX}20261030-1`;
const APP_BASE = new URL('./', self.location.href);
const OFFLINE_PAGE = new URL('offline.html', APP_BASE);
const PRECACHE_URLS = [
    'offline.html',
    'manifest.webmanifest',
    'favicon.ico',
    'assets/css/duno-ui.css',
    'assets/css/student-navigation.css',
    'assets/css/pwa.css',
    'assets/js/pwa.js',
    'assets/images/duno-mascot-welcome.webp',
    'assets/images/duno-pwa-icon-192.png',
    'assets/images/duno-pwa-icon-512.png',
].map((path) => new URL(path, APP_BASE).href);
// Cache only this reviewed static shell list. Matching by pathname permits
// duno_asset() version query strings without allowing arbitrary /assets routes.
const PUBLIC_STATIC_PATHS = new Set(PRECACHE_URLS.map((url) => new URL(url).pathname));
const PUBLIC_ROOT_PATHS = new Set([
    new URL('manifest.webmanifest', APP_BASE).pathname,
    new URL('favicon.ico', APP_BASE).pathname,
    OFFLINE_PAGE.pathname,
]);

self.addEventListener('install', (event) => {
    event.waitUntil((async () => {
        const cache = await caches.open(CACHE_NAME);
        await cache.addAll(PRECACHE_URLS);
        await self.skipWaiting();
    })());
});

self.addEventListener('activate', (event) => {
    event.waitUntil((async () => {
        const keys = await caches.keys();
        await Promise.all(keys
            .filter((key) => key.startsWith(CACHE_PREFIX) && key !== CACHE_NAME)
            .map((key) => caches.delete(key)));
        await self.clients.claim();
    })());
});

function isPublicStaticRequest(url, request) {
    if (url.origin !== self.location.origin || request.method !== 'GET') {
        return false;
    }

    return PUBLIC_STATIC_PATHS.has(url.pathname) || PUBLIC_ROOT_PATHS.has(url.pathname);
}

self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    if (request.method !== 'GET' || url.origin !== self.location.origin) {
        return;
    }

    if (request.mode === 'navigate') {
        // Never cache server-rendered pages: they may contain private account data.
        event.respondWith(fetch(request).catch(async () => {
            const cachedOfflinePage = await caches.match(OFFLINE_PAGE.href);
            return cachedOfflinePage || Response.error();
        }));
        return;
    }

    if (!isPublicStaticRequest(url, request)) {
        return;
    }

    event.respondWith((async () => {
        const cache = await caches.open(CACHE_NAME);
        // Preserve version query strings emitted by duno_asset() so a new asset URL refreshes offline copies.
        const cacheKey = new Request(url.href);
        const cached = await cache.match(cacheKey);
        if (cached) return cached;

        const response = await fetch(request);
        const cacheControl = response.headers.get('Cache-Control') || '';
        if (response.ok && response.type === 'basic' && !/\b(?:private|no-store)\b/i.test(cacheControl)) {
            await cache.put(cacheKey, response.clone());
        }
        return response;
    })());
});
