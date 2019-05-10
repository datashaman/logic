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
