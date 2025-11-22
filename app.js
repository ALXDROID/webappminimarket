let productos = [];

async function cargarProductos(){
  try {
    const res = await fetch("https://marketapp.kesug.com/api/getProductos.php");
    const data = await res.json();

    if(data.success){

      productos = data.productos.map(p => ({
        ...p,
        precio: Number(p.precio), // Convertir DECIMAL → número
        img: "https://marketapp.kesug.com/uploads/" + p.imagen // URL completa
      }));

      mostrarProductos(); // ya funciona con backend
    } 
    else {
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

function agregar(id){
  let c = obtenerCarrito();
  let item = c.find(x => x.id === id);

  if(item) item.cantidad++;
  else c.push({id, cantidad:1});

  guardarCarrito(c);
  alert("Agregado al carrito");
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
   INIT — SOLO CARGA LO QUE CORRESPONDE A CADA PÁGINA
   ============================================================ */
document.addEventListener("DOMContentLoaded", async () => {

  // Página principal: carga productos desde backend
  if($("productos")) {
    await cargarProductos();
    mostrarProductos();
  }

  // Buscador
  if($("buscar")) {
    $("buscar").addEventListener("input", e => filtrarProductos(e.target.value));
  }

  // Carrito: también necesita productos desde backend
  if($("listaCarrito")) {
    await cargarProductos(); // NECESARIO para que carrito tenga precios e imágenes
    mostrarCarritoPro();
  }

  // Detalle del producto
  if($("detalle")) {
    await cargarProductos(); // también necesario
    construirDetalleDesdeURL();
  }

});





