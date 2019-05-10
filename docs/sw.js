importScripts("/precache-manifest.8bd881d2c7716226b9b24708d87df6ca.js", "https://storage.googleapis.com/workbox-cdn/releases/4.3.1/workbox-sw.js");

self.addEventListener('fetch', function(event) {
  if (!event.request.url.startsWith('http')) {
      return
  }

  event.respondWith(
    caches.match(event.request).then(function(resp) {
      return resp || fetch(event.request).then(function(response) {
        return caches.open('v1').then(function(cache) {
          cache.put(event.request, response.clone())

          return response
        })
      })
    })
  )
})

