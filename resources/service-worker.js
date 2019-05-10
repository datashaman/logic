import { precacheAndRoute } from 'workbox-precaching/precacheAndRoute.mjs'

self.addEventListener('fetch', function(event) {
  event.respondWith(
    caches.match(event.request).then(function(resp) {
      return resp || fetch(event.request).then(function(response) {
        return caches.open('v1').then(function(cache) {
          cache.put(event.request, response.clone());

          return response;
        });
      });
    })
  );
});

precacheAndRoute(self.__precacheManifest)
