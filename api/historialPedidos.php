<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require "conexion.php";

$data = json_decode(file_get_contents("php://input"), true);

$id_usuario = $data["id_usuario"] ?? 0;

$sql = "SELECT id, total, estado, fecha FROM pedidos WHERE id_usuario = ? ORDER BY id DESC";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i",$id_usuario);
$stmt->execute();

$res = $stmt->get_result();
$rows = [];

while($row = $res->fetch_assoc()){
    $rows[] = $row;
}

echo json_encode([
    "success" => true,
    "pedidos" => $rows
]);
