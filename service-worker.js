const CACHE_NAME = "minimarket-v3";

const FILES_TO_CACHE = [
  "./",
  "./index.html",
  "./login.html",
  "./carrito.html",
  "./detalle.html",
  "./historial.html",
  "./app.js",
  "./manifest.json"
];

// INSTALAR (pre-cache)
self.addEventListener("install", event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(FILES_TO_CACHE))
  );
  self.skipWaiting();
});

// ACTIVAR (limpiar versiones viejas)
self.addEventListener("activate", event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(
        keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k))
      )
    )
  );
  self.clients.claim();
});

// FETCH
self.addEventListener("fetch", event => {

  const url = event.request.url;

  // 🔥 No interceptar API
  if (url.includes("/api/")) return;

  // 🔥 Cache dinámico de imágenes en /uploads/
  if (url.includes("/uploads/")) {
    event.respondWith(
      caches.open(CACHE_NAME).then(cache =>
        fetch(event.request)
          .then(response => {
            cache.put(event.request, response.clone());
            return response;
          })
          .catch(() => caches.match(event.request))
      )
    );
    return;
  }

  // 🔥 Cache estático para HTML/JS/CSS
  event.respondWith(
    caches.match(event.request).then(resp => resp || fetch(event.request))
  );
});
