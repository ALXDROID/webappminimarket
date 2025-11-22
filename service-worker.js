const CACHE_NAME = "minimarket-v3";

// RUTA BASE DEL REPO EN GITHUB PAGES
const BASE = "/webappminimarket
/";

const FILES_TO_CACHE = [
  BASE,
  BASE + "index.html",
  BASE + "login.html",
  BASE + "carrito.html",
  BASE + "detalle.html",
  BASE + "historial.html",
  BASE + "app.js",
  BASE + "manifest.json",

  // IMÁGENES
  BASE + "imgs/aceite.jpg",
  BASE + "imgs/arroz.jpg",
  BASE + "imgs/atun.jpg",
  BASE + "imgs/azucar.jpg",
  BASE + "imgs/cafe.jpg",
  BASE + "imgs/coca.jpg",
  BASE + "imgs/coca15.jpg",
  BASE + "imgs/cocazero15.jpg",
  BASE + "imgs/donuts.jpg",
  BASE + "imgs/fanta15.jpg",
  BASE + "imgs/fideos.jpg",
  BASE + "imgs/hallulla.jpg",
  BASE + "imgs/marraqueta.jpg",
  BASE + "imgs/moldeintegral.jpg",
  BASE + "imgs/monster.jpg",
  BASE + "imgs/pan.jpg",
  BASE + "imgs/panmolde.jpg",
  BASE + "imgs/pepsi15.jpg",
  BASE + "imgs/porotos.jpg",
  BASE + "imgs/queque.jpg",
  BASE + "imgs/redbull.jpg",

  // ICONOS PWA
  BASE + "icons/icon-192.png",
  BASE + "icons/icon-512.png"
];

// INSTALAR
self.addEventListener("install", e => {
  e.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(FILES_TO_CACHE))
  );
  self.skipWaiting();
});

// ACTIVAR → borra versiones viejas
self.addEventListener("activate", e => {
  e.waitUntil(
    caches.keys().then(keys =>
      Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
    )
  );
  self.clients.claim();
});

// FETCH
self.addEventListener("fetch", e => {

  const url = new URL(e.request.url);

  // NO interceptar backend en InfinityFree
  if (url.origin === "https://marketapp.kesug.com") return;
  if (url.pathname.startsWith("/api/")) return;

  // SOLO cachear archivos del GitHub Pages
  if (!url.pathname.startsWith(BASE)) return;

  e.respondWith(
    caches.match(e.request).then(resp => resp || fetch(e.request))
  );
});
