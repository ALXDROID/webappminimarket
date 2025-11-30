<?php
header("Content-Type: application/json");
require "conexion.php";

$id = intval($_GET["id"] ?? 0);

$q = $conexion->prepare("
    SELECT id, id_usuario, tipo, metodo, total, hora_entrega, estado
    FROM pedidos
    WHERE id = ?
");
$q->bind_param("i", $id);
$q->execute();
$p = $q->get_result()->fetch_assoc();

if (!$p) {
    echo json_encode(["success" => false, "error" => "Pedido no existe"]);
    exit;
}

// Items reales
$qi = $conexion->prepare("
    SELECT 
        pi.id_producto AS id,
        pi.cantidad,
        pi.subtotal,
        pr.nombre
    FROM pedido_items pi
    LEFT JOIN productos pr ON pr.id = pi.id_producto
    WHERE pi.id_pedido = ?
");
$qi->bind_param("i", $id);
$qi->execute();
$items = $qi->get_result()->fetch_all(MYSQLI_ASSOC);

// Validación edición
$hora = $p["hora_entrega"] ?? "00:00";

list($h,$m) = explode(":", $hora);

$minEntrega = $h*60 + $m;
$minActual  = intval(date("H"))*60 + intval(date("i"));

$editable = true;
if ($p["estado"] !== "pendiente") $editable = false;
if (($minEntrega - $minActual) < 30) $editable = false;

echo json_encode([
    "success" => true,
    "editable" => $editable,
    "items" => $items,
    "pedido" => $p
]);
