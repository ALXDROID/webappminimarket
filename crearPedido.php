<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
require "conexion.php";

$data = json_decode(file_get_contents("php://input"), true);

$id_usuario = intval($data["id_usuario"]);
$tipo = $data["tipo_pedido"];
$items = $data["items"];

if ($id_usuario <= 0 || empty($items)) {
    echo json_encode(["success"=>false,"error"=>"Datos inválidos"]);
    exit;
}

$total = 0;

// Primero, obtener precios reales
foreach ($items as &$it){
    $idp = intval($it["id"]);
    $cantidad = intval($it["cantidad"]);

    $q = $conexion->query("SELECT precio FROM productos WHERE id=$idp LIMIT 1");
    $p = $q->fetch_assoc();

    $it["precio_unitario"] = $p["precio"];
    $total += $p["precio"] * $cantidad;
}

// Insert pedido
$stmt = $conexion->prepare(
  "INSERT INTO pedidos (id_usuario, tipo_pedido, total) VALUES (?, ?, ?)"
);
$stmt->bind_param("isd", $id_usuario, $tipo, $total);
$stmt->execute();
$idPedido = $stmt->insert_id;

// Insert detalle
foreach ($items as $it){
    $stmt = $conexion->prepare(
        "INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio_unitario)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "iiid",
        $idPedido,
        $it["id"],
        $it["cantidad"],
        $it["precio_unitario"]
    );
    $stmt->execute();
}

echo json_encode(["success"=>true, "id_pedido"=>$idPedido, "total"=>$total]);
