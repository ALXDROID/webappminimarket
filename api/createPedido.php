<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

header("Content-Type: application/json");
require "conexion.php";

// ----------------------
// RECIBIR JSON
// ----------------------
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "error" => "JSON vacío"]);
    exit;
}

$id_usuario  = intval($data["id_usuario"] ?? 0);
$tipo        = $conexion->real_escape_string($data["tipo_pedido"] ?? ""); 
$metodo      = $conexion->real_escape_string($data["metodo_pago"] ?? ""); 
$items       = $data["items"] ?? [];
$entrega     = $data["entrega"] ?? null;
$hora        = trim($data["hora"] ?? "");

// ----------------------
// VALIDACIONES BÁSICAS
// ----------------------
if ($id_usuario <= 0) {
    echo json_encode(["success"=>false, "error"=>"Usuario inválido"]);
    exit;
}

if (!in_array($tipo, ["retiro","delivery"])) {
    echo json_encode(["success"=>false, "error"=>"Tipo inválido"]);
    exit;
}

if (!in_array($metodo, ["efectivo","paypal"])) {
    echo json_encode(["success"=>false, "error"=>"Método de pago inválido"]);
    exit;
}

if (!is_array($items) || count($items) == 0) {
    echo json_encode(["success"=>false, "error"=>"Carrito vacío"]);
    exit;
}

// ----------------------
// OBTENER TELÉFONO SIEMPRE
// ----------------------
$telefonoUsuario = "";
$stmtTel = $conexion->prepare("SELECT telefono FROM usuarios WHERE id = ?");
$stmtTel->bind_param("i", $id_usuario);
$stmtTel->execute();
$stmtTel->bind_result($telefonoUsuario);
$stmtTel->fetch();
$stmtTel->close();

// fallback si está vacío
if (!$telefonoUsuario) $telefonoUsuario = "0";

// ----------------------
// DATOS DELIVERY
// ----------------------
$direccion = null;
$receptor  = null;
$telefono  = $telefonoUsuario; // por defecto

if ($tipo === "delivery") {

    $direccion = trim($entrega["direccion"] ?? "");
    $receptor  = trim($entrega["nombre"] ?? "");
    $telefono  = trim($entrega["telefono"] ?? "");

    if (!$direccion || !$receptor || !$telefono) {
        echo json_encode(["success"=>false,"error"=>"Faltan datos de entrega"]);
        exit;
    }
}

// ----------------------
// CALCULAR TOTAL
// ----------------------
$total = 0;
$itemsFinal = [];

$qPrecio = $conexion->prepare("SELECT precio FROM productos WHERE id = ?");

foreach ($items as $it) {

    $idp = intval($it["id"]);
    $cant = intval($it["cantidad"]);

    if ($idp <= 0 || $cant <= 0) continue;

    $qPrecio->bind_param("i", $idp);
    $qPrecio->execute();

    $res = $qPrecio->get_result()->fetch_assoc();
    if (!$res) continue;

    $precio = floatval($res["precio"]);
    $sub = $precio * $cant;

    $total += $sub;

    $itemsFinal[] = [
        "idp" => $idp,
        "cant" => $cant,
        "precio" => $precio,
        "sub" => $sub
    ];
}

$qPrecio->close();

// ----------------------
// INSERTAR PEDIDO
// ----------------------
$q = $conexion->prepare("
    INSERT INTO pedidos 
    (id_usuario, tipo, metodo, direccion, receptor, telefono, total, hora_entrega)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$q->bind_param("issssdds",
    $id_usuario,
    $tipo,
    $metodo,
    $direccion,
    $receptor,
    $telefono,
    $total,
    $hora
);

if (!$q->execute()) {
    echo json_encode(["success"=>false, "error"=>"ERROR SQL: ".$q->error]);
    exit;
}

$idPedido = $q->insert_id;
$q->close();

// ----------------------
// INSERTAR ITEMS
// ----------------------
$qi = $conexion->prepare("
    INSERT INTO pedido_items (id_pedido, id_producto, cantidad, precio_unit, subtotal)
    VALUES (?, ?, ?, ?, ?)
");

foreach ($itemsFinal as $it) {
    $qi->bind_param("iiidd", 
        $idPedido,
        $it["idp"],
        $it["cant"],
        $it["precio"],
        $it["sub"]
    );
    $qi->execute();
}

$qi->close();

// ----------------------
// RESPUESTA
// ----------------------
echo json_encode([
    "success" => true,
    "id_pedido" => $idPedido,
    "total" => $total
]);
?>
