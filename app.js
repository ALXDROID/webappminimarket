/* ============================================================
   VARIABLES GLOBALES
============================================================ */
let productos = [];

/* ============================================================
   CARGAR PRODUCTOS
============================================================ */
async function cargarProductos() {
  try {
    const res = await fetch("http://localhost/MiniMarket/api/getProductos.php");
    const raw = await res.text();

    let data;
    try { data = JSON.parse(raw); }
    catch(e){
      console.error("❌ Respuesta no JSON:", raw);
      return;
    }

    if (!data.success) {
      alert("Error cargando productos");
      return;
    }

    productos = data.productos.map(p => ({
      id: Number(p.id),
      nombre: p.nombre,
      categoria: p.categoria,
      precio: Number(p.precio),
      img: "http://localhost/MiniMarket/uploads/" + p.imagen
    }));

    if (document.getElementById("productos")) {
      mostrarProductos();
    }

  } catch (e) {
    console.error(e);
    alert("Error de conexión con el servidor.");
  }
}

/* Helper */
function $(id){ return document.getElementById(id); }

/* ============================================================
   MOSTRAR PRODUCTOS
============================================================ */
function mostrarProductos(lista = productos){
  const div = $("productos");
  if (!div) return;

  div.innerHTML = "";

  lista.forEach(p => {
    div.innerHTML += `
      <div class="col-6">
        <div class="product-card"
     data-cat="${p.categoria}"
     data-name="${p.nombre}"
     onclick="handleClickProducto(${p.id})">


          <img src="${p.img}">
          <h6 class="mt-2 fw-bold">${p.nombre}</h6>

          <div class="d-flex justify-content-between align-items-center">
            <span class="price-tag">$${p.precio}</span>
            <button class="btn-add" >+</button>
          </div>

        </div>
      </div>
    `;
  });
}

/* ============================================================
   FILTROSonclick="agregar(${p.id}); event.stopPropagation();"
============================================================ */
function aplicarFiltros(){
  const cards = document.querySelectorAll(".product-card");
  const cat = window.filtroCategoria || "Todos";
  const txt = (window.filtroTexto || "").toLowerCase();

  cards.forEach(card => {
    const ccat = card.dataset.cat;
    const cname = card.dataset.name;

    const okCat = (cat === "Todos" || ccat === cat);
    const okTxt = cname.includes(txt);

    card.style.display = (okCat && okTxt) ? "block" : "none";
  });
}

/* ============================================================
   CARRITO
============================================================ */
function obtenerCarrito(){
  return JSON.parse(localStorage.getItem("carrito") || "[]");
}
function guardarCarrito(c){
  localStorage.setItem("carrito", JSON.stringify(c));
}

function agregar(id) {

  let carrito = obtenerCarrito();
  const item = carrito.find(p => p.id == id);

  if (item) item.cantidad++;
  else carrito.push({ id, cantidad: 1 });

  guardarCarrito(carrito);

  if (localStorage.getItem("editando") === "1") {
    // MUY IMPORTANTE → evitar que carrito.html recargue pedido original
    localStorage.setItem("skipReload", "1");

    location.href = "carrito.html?edit=1";
  } else {
    noti("Producto agregado 🛒");
  }
}



/* ============================================================
   DETALLE
============================================================ */
function verDetalle(id){
  location.href = "detalle.html?id=" + id;
}

/* ============================================================
   NOTIFICACIÓN
============================================================ */
function noti(msg){
  const box = document.createElement("div");
  box.style = `
    position:fixed; bottom:20px; left:50%;
    transform:translateX(-50%);
    background:#00b894; color:white;
    padding:15px 25px; border-radius:12px;
    z-index:9999; font-size:18px;
  `;
  box.textContent = msg;
  document.body.appendChild(box);

  setTimeout(() => box.remove(), 2000);
}
function handleClickProducto(id) {

  // 🔥 Si venimos desde "editar pedido"
  if (localStorage.getItem("editando") === "1") {

    agregar(id); // agrega o suma cantidad al carrito

    // volver al carrito en modo edición
    window.location.href = "carrito.html?edit=1";
    return;
  }

  // 🟢 Modo normal → abrir detalle
  verDetalle(id);
}
