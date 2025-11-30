<?php
header("Content-Type: application/json");
require "conexion.php";

$id = intval($_GET["id"] ?? 0);
if ($id <= 0) {
    echo json_encode(["success"=>false, "pedidos"=>[]]);
    exit;
}

// OBTENER PEDIDOS DEL USUARIO
$q = $conexion->prepare("
    SELECT 
        id,
        tipo,
        metodo,
        total,
        fecha,
        hora_entrega,
        estado
    FROM pedidos
    WHERE id_usuario = ?
    ORDER BY id DESC
");
$q->bind_param("i", $id);
$q->execute();
$res = $q->get_result();

$pedidos = [];

while ($p = $res->fetch_assoc()) {

    // ===========================
    // CARGAR ITEMS DEL PEDIDO
    // ===========================
    $qi = $conexion->prepare("
        SELECT 
            pi.id_producto AS id,
            pr.nombre,
            pi.cantidad,
            pi.subtotal
        FROM pedido_items pi
        LEFT JOIN productos pr ON pr.id = pi.id_producto
        WHERE pi.id_pedido = ?
    ");
    $qi->bind_param("i", $p["id"]);
    $qi->execute();
    $items = $qi->get_result()->fetch_all(MYSQLI_ASSOC);

    // ===========================
    // VALIDAR SI ES EDITABLE
    // ===========================
    $hora = $p["hora_entrega"] ?? "00:00";

    if (!preg_match("/^\d{2}:\d{2}(:\d{2})?$/", $hora)) {
        $hora = "00:00";
    }

    list($h,$m) = explode(":", $hora);
    $minEntrega = intval($h)*60 + intval($m);

    $minActual = intval(date("H"))*60 + intval(date("i"));

    $restantes = $minEntrega - $minActual;

    $editable = true;

    if ($p["estado"] !== "pendiente") {
        $editable = false;
    }

    if ($restantes < 30) {
        $editable = false;
    }

    // ===========================
    // ARMAR PEDIDO
    // ===========================
    $pedidos[] = [
        "id" => $p["id"],
        "tipo" => $p["tipo"],
        "estado" => $p["estado"],
        "total" => $p["total"],
        "hora_entrega" => $hora,
        "fecha" => $p["fecha"],
        "items" => $items,
        "minutos_restantes" => $restantes,
        "es_editable" => $editable
    ];
}

echo json_encode([
    "success" => true,
    "pedidos" => $pedidos
]);

