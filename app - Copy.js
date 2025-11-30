let productos = [];

async function cargarProductos(){
  try {
    const res = await fetch("http://localhost/MiniMarket/api/getProductos.php");
    const raw = await res.text();

    let data;
    try { data = JSON.parse(raw); }
    catch(e) {
      console.error("Respuesta no JSON:", raw);
      return;
    }

    if(data.success){
      productos = data.productos.map(p => ({
        id: Number(p.id),
        nombre: p.nombre,
        categoria: p.categoria,
        precio: Number(p.precio),
        img: "http://localhost/MiniMarket/uploads/" + p.imagen
      }));

      mostrarProductos();
    } else {
      alert("No se pudieron cargar los productos");
    }

  } catch (error) {
    alert("Error conectando al servidor de productos");
    console.error(error);
  }
}

/* Helper */
function $(id){ return document.getElementById(id); }

/* ============================================================
   DARK MODE
   ============================================================ */
function toggleDark(){
  document.body.classList.toggle("dark");
  localStorage.setItem("darkmode", document.body.classList.contains("dark"));
}

if(localStorage.getItem("darkmode") === "true"){
  document.body.classList.add("dark");
}

/* ============================================================
   LISTADO DE PRODUCTOS
   ============================================================ */
function mostrarProductos(lista = productos){
  const div = $("productos");
  if(!div) return;

  div.innerHTML = "";
  lista.forEach(p => {
    div.innerHTML += `
    <div class="col-6">
      <div class="product-card" onclick="verDetalle(${p.id})" style="cursor:pointer;">
        <img src="${p.img}">
        <h6 class="mt-2 fw-bold">${p.nombre}</h6>
        <div class="d-flex justify-content-between align-items-center">
          <span class="price-tag">$${p.precio}</span>
          <button class="btn-add" onclick="agregar(${p.id}); event.stopPropagation();">+</button>
        </div>
      </div>
    </div>`;
  });
}

function filtrarCat(cat){
  if(cat === "Todos") return mostrarProductos();
  const filtro = productos.filter(p => p.categoria === cat);
  mostrarProductos(filtro);
}

function filtrarProductos(txt){
  if(!$("productos")) return;
  const encontrado = productos.filter(p =>
    p.nombre.toLowerCase().includes(txt.toLowerCase())
  );
  mostrarProductos(encontrado);
}

/* ============================================================
   CARRITO — LOCALSTORAGE
   ============================================================ */
function obtenerCarrito(){ return JSON.parse(localStorage.getItem("carrito") || "[]"); }
function guardarCarrito(c){ localStorage.setItem("carrito", JSON.stringify(c)); }

function agregar(id) {
  let carrito = obtenerCarrito();
  const existe = carrito.find(x => x.id === id);

  if (existe) {
    existe.cantidad++;
  } else {
    carrito.push({ id, cantidad: 1 });
  }

  guardarCarrito(carrito);

  // 🔥 Si estamos editando → volver al carrito de edición
  if (localStorage.getItem("editando") === "1") {
    location.href = "carrito.html?edit=1";
    return;
  }

  noti("Producto agregado 🛒");
}

function mostrarCarrito(){
  const div = $("listaCarrito");
  if(!div) return;

  let c = obtenerCarrito();
  div.innerHTML = "";

  c.forEach(item => {
    let p = productos.find(x => x.id === item.id);
    div.innerHTML += `
    <div class="cart-card mb-2">
      <div class="d-flex justify-content-between">
        <div>
          <strong>${p.nombre}</strong><br>
          Cantidad: ${item.cantidad}
        </div>
        <div class="text-success fw-bold">$${p.precio * item.cantidad}</div>
      </div>
    </div>`;
  });
}

/* ============================================================
   DETALLE
   ============================================================ */
function verDetalle(id){
  location.href = "detalle.html?id=" + id;
}

function construirDetalleDesdeURL(){
  const cont = $("detalle");
  if(!cont) return;

  const id = new URLSearchParams(location.search).get("id");

  const p = productos.find(x => x.id == id);
  if(!p){
    cont.innerHTML = `
    <div class="p-4 text-center">
      <h2>❌ Producto no encontrado</h2>
      <button onclick="location.href='index.html'" class="btn btn-success mt-3">Volver</button>
    </div>`;
    return;
  }

  cont.innerHTML = `
  <img src="${p.img}" class="top-img">
  <div class="desc-box">
    <div class="d-flex justify-content-between">
      <h3>${p.nombre}</h3>
      <button class="fav-btn" onclick="toggleFav(${p.id})">❤️</button>
    </div>

    <h2 class="text-success fw-bold">$${p.precio}</h2>
    <p class="text-muted">Producto seleccionado del MiniMarket.</p>

    <button onclick="agregar(${p.id})" class="btn btn-add w-100 mt-3">
      Añadir al carrito 🛒
    </button>

    <button onclick="history.back()" class="btn btn-secondary w-100 mt-3">
      Volver
    </button>
  </div>`;
}

/* ============================================================
   FAVORITOS
   ============================================================ */
function obtenerFavs(){ return JSON.parse(localStorage.getItem("favoritos") || "[]"); }
function toggleFav(id){
  let f = obtenerFavs();
  if(f.includes(id)) f = f.filter(x => x !== id);
  else{
    f.push(id);
    navigator.vibrate?.(50);
  }
  localStorage.setItem("favoritos", JSON.stringify(f));
}

/* ============================================================
   LOGIN / LOGOUT
   ============================================================ */
function logout(){
  localStorage.removeItem("usuario");
  location.href = "login.html";
}

/* ============================================================
   INIT
   ============================================================ */
document.addEventListener("DOMContentLoaded", async () => {

  if($("productos")) {
    await cargarProductos();
    mostrarProductos();
  }

  if($("buscar")) {
    $("buscar").addEventListener("input", e => filtrarProductos(e.target.value));
  }

  if($("listaCarrito")) {
    await cargarProductos();
    mostrarCarrito();   // ← CORREGIDO
  }

  if($("detalle")) {
    await cargarProductos();
    construirDetalleDesdeURL();
  }

});
function agregar(id) {
  let carrito = obtenerCarrito();
  const item = carrito.find(p => p.id == id);

  if (item) {
    item.cantidad++;
  } else {
    carrito.push({ id, cantidad: 1 });
  }

  guardarCarrito(carrito);

  // 🔥 Solo redirigir si realmente estamos en modo edición
  if (localStorage.getItem("editando") === "1") {
    location.href = "carrito.html?edit=1";
  } else {
    noti("Producto agregado 🛒");
  }
}
