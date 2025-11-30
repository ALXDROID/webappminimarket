<?php
header("Content-Type: application/json");
require "conexion.php";

// ------------------------------------------
// LEER JSON
// ------------------------------------------
$data = json_decode(file_get_contents("php://input"), true);

$id_usuario = intval($data["id_usuario"] ?? 0);
$tipo       = $data["tipo_pedido"] ?? "";
$items      = $data["items"] ?? [];

// ------------------------------------------
// VALIDACIONES
// ------------------------------------------
if ($id_usuario <= 0) {
    echo json_encode(["success"=>false, "error"=>"Usuario inválido"]);
    exit;
}

if (!in_array($tipo, ["retiro", "delivery"])) {
    echo json_encode(["success"=>false, "error"=>"Tipo de pedido inválido"]);
    exit;
}

if (empty($items)) {
    echo json_encode(["success"=>false, "error"=>"Carrito vacío"]);
    exit;
}

// ------------------------------------------
// CALCULAR TOTAL DESDE BD (PROTEGIDO)
// ------------------------------------------
$total = 0;
$productosProcesados = [];

$stmtPrecio = $conexion->prepare("SELECT precio FROM productos WHERE id = ? LIMIT 1");

foreach ($items as $it) {

    $idp       = intval($it["id"] ?? 0);
    $cantidad  = intval($it["cantidad"] ?? 0);

    if ($idp <= 0 || $cantidad <= 0) {
        echo json_encode(["success"=>false, "error"=>"Datos de producto inválidos"]);
        exit;
    }

    // Obtener precio real desde BD
    $stmtPrecio->bind_param("i", $idp);
    $stmtPrecio->execute();
    $r = $stmtPrecio->get_result()->fetch_assoc();

    if (!$r) {
        echo json_encode(["success"=>false, "error"=>"Producto no encontrado: $idp"]);
        exit;
    }

    $precio = floatval($r["precio"]);

    $productosProcesados[] = [
        "id" => $idp,
        "cantidad" => $cantidad,
        "precio_unitario" => $precio
    ];

    $total += $precio * $cantidad;
}

$stmtPrecio->close();

// ------------------------------------------
// INSERTAR PEDIDO
// ------------------------------------------
$stmt = $conexion->prepare(
  "INSERT INTO pedidos (id_usuario, tipo_pedido, total, fecha)
   VALUES (?, ?, ?, NOW())"
);

$stmt->bind_param("isd", $id_usuario, $tipo, $total);

if (!$stmt->execute()) {
    echo json_encode(["success"=>false, "error"=>"Error guardando pedido"]);
    exit;
}

$idPedido = $stmt->insert_id;
$stmt->close();

// ------------------------------------------
// INSERTAR DETALLE
// ------------------------------------------
$stmtDet = $conexion->prepare(
    "INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio_unitario)
     VALUES (?, ?, ?, ?)"
);

foreach ($productosProcesados as $p) {
    $stmtDet->bind_param(
        "iiid",
        $idPedido,
        $p["id"],
        $p["cantidad"],
        $p["precio_unitario"]
    );

    if (!$stmtDet->execute()) {
        echo json_encode(["success"=>false,"error"=>"Error guardando detalle"]);
        exit;
    }
}

$stmtDet->close();
$conexion->close();

// ------------------------------------------
// RESPUESTA FINAL
// ------------------------------------------
echo json_encode([
    "success"=>true,
    "id_pedido"=>$idPedido,
    "total"=>$total
]);
?>
