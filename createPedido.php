<?php
header("Content-Type: application/json");
require "conexion.php";

$input = json_decode(file_get_contents("php://input"), true);

$id_usuario   = intval($input["id_usuario"]);
$tipo         = $conexion->real_escape_string($input["tipo_pedido"]);
$metodo       = $conexion->real_escape_string($input["metodo_pago"]);
$entrega      = $input["entrega"]; // puede ser null
$items        = $input["items"];

// Validaciones
if (!$id_usuario || !$tipo || !$metodo || !is_array($items)) {
  echo json_encode(["success" => false, "error" => "Datos incompletos"]);
  exit;
}

// Insertar pedido
$sql = "INSERT INTO pedidos (id_usuario, tipo, metodo, direccion, receptor, telefono, fecha)
        VALUES (?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conexion->prepare($sql);

$dir = $entrega["direccion"] ?? null;
$nom = $entrega["nombre"] ?? null;
$tel = $entrega["telefono"] ?? null;

$stmt->bind_param("isssss", $id_usuario, $tipo, $metodo, $dir, $nom, $tel);
$stmt->execute();

$idPedido = $stmt->insert_id;

// Insertar ítems
$q = $conexion->prepare("INSERT INTO pedido_items (id_pedido, id_prod, cantidad) VALUES (?, ?, ?)");

foreach ($items as $i) {
  $q->bind_param("iii", $idPedido, $i["id"], $i["cantidad"]);
  $q->execute();
}

echo json_encode([
  "success" => true,
  "id_pedido" => $idPedido
]);
