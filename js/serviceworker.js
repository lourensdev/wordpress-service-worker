importScripts('https://storage.googleapis.com/workbox-cdn/releases/3.0.0/workbox-sw.js');

const cacheName = 'wp_sw_cache';

// Cache JS and CSS files
workbox.routing.registerRoute(/.*\.(?:js|css)/,
  new workbox.strategies.StaleWhileRevalidate({
    cacheName
  })
);

// Cache the home page
workbox.routing.registerRoute('/',
  new workbox.strategies.StaleWhileRevalidate({
    cacheName
  })
);

 // Cache the Google Fonts stylesheets with a stale while revalidate strategy.
 workbox.routing.registerRoute(
  /^https:\/\/fonts\.googleapis\.com/,
  new workbox.strategies.StaleWhileRevalidate({
    cacheName: 'google-fonts-stylesheets',
  }),
);

// Cache the Google Fonts webfont files with a cache first strategy for 1 year.
workbox.routing.registerRoute(
  /^https:\/\/fonts\.gstatic\.com/,
  new workbox.strategies.CacheFirst({
    cacheName: 'google-fonts-webfonts',
    plugins: [
      new workbox.cacheableResponse.Plugin({
        statuses: [0, 200],
      }),
      new workbox.expiration.Plugin({
        maxAgeSeconds: 60 * 60 * 24 * 365,
      }),
    ],
  }),
); 