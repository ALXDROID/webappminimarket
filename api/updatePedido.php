<?php
header("Content-Type: application/json");
require "conexion.php";

$data = json_decode(file_get_contents("php://input"), true);

$id_pedido = intval($data["id_pedido"] ?? 0);
$items     = $data["items"] ?? [];

if ($id_pedido <= 0 || empty($items)) {
    echo json_encode(["success"=>false,"error"=>"Datos inválidos"]);
    exit;
}

// VALIDAR QUE ESTÉ EDITABLE
$q = $conexion->prepare("SELECT estado, hora_entrega FROM pedidos WHERE id=?");
$q->bind_param("i", $id_pedido);
$q->execute();
$p = $q->get_result()->fetch_assoc();

if (!$p) {
    echo json_encode(["success"=>false,"error"=>"Pedido no existe"]);
    exit;
}

if ($p["estado"] !== "pendiente") {
    echo json_encode(["success"=>false,"error"=>"No se puede editar (ya procesado)"]);
    exit;
}

// Validar hora
list($h,$m) = explode(":", $p["hora_entrega"]);
$minPedido = $h*60+$m;
$minNow = intval(date("H"))*60+intval(date("i"));

if (($minPedido - $minNow) < 30) {
    echo json_encode(["success"=>false,"error"=>"Quedan menos de 30 minutos"]);
    exit;
}

// BORRAR items actuales
$conexion->query("DELETE FROM pedido_items WHERE id_pedido = $id_pedido");

// RECALCULAR total
$total = 0;

$qIns = $conexion->prepare("
    INSERT INTO pedido_items (id_pedido,id_producto,cantidad,precio_unit,subtotal)
    VALUES (?, ?, ?, ?, ?)
");

foreach ($items as $it) {

    $idp = intval($it["id"]);
    $cant = intval($it["cantidad"]);

    $r = $conexion->query("SELECT precio FROM productos WHERE id=$idp");
    $prod = $r->fetch_assoc();
    if (!$prod) continue;

    $precio = floatval($prod["precio"]);
    $sub = $precio * $cant;
    $total += $sub;

    $qIns->bind_param("iiidd", $id_pedido, $idp, $cant, $precio, $sub);
    $qIns->execute();
}

// actualizar total
$conexion->query("UPDATE pedidos SET total=$total WHERE id=$id_pedido");

echo json_encode(["success"=>true,"total"=>$total]);
