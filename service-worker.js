self.addEventListener("install", e => {
  e.waitUntil(
    caches.open("minimarket-v1").then(cache => {
      return cache.addAll([
        "./",
        "./index.html",
        "./login.html",
        "./carrito.html",
        "./detalle.html",
        "./historial.html",
        "./app.js",
        "./manifest.json"
      ]);
    })
  );
});

self.addEventListener("fetch", e => {

  // Si el request NO es del mismo origen → no lo interceptes
  if (!e.request.url.startsWith(self.location.origin)) {
    return;
  }

  e.respondWith(
    caches.match(e.request).then(resp => {
      return resp || fetch(e.request);
    })
  );
});

