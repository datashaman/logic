fetch('/')
    .then(function (response) {
        return caches.open('v1').then(function(cache) {
          cache.put('/', response.clone())

          return response
        })
    })
    .then(function (response) {
        self.addEventListener('fetch', function(event) {
        console.log(event.request)

        if (
            !event.request.url.startsWith('http')
        ) {
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
    })