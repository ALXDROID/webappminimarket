<?php
header("Content-Type: application/json");
require "conexion.php";

$data = json_decode(file_get_contents("php://input"), true);

$id       = intval($data["id"] ?? 0);
$nombre   = trim($data["nombre"] ?? "");
$telefono = trim($data["telefono"] ?? "");
$avatar   = trim($data["avatar"] ?? "");

if ($id<=0 || $nombre=="" || $telefono=="") {
    echo json_encode(["success"=>false,"error"=>"Datos inválidos"]);
    exit;
}

$u = $conexion->prepare("
    UPDATE usuarios
    SET nombre=?, telefono=?, avatar=?
    WHERE id=?
");
$u->bind_param("sssi", $nombre, $telefono, $avatar, $id);

if (!$u->execute()) {
    echo json_encode(["success"=>false, "error"=>$u->error]);
    exit;
}

// 🔥 devolver avatar actualizado
echo json_encode([
    "success" => true,
    "avatar"  => $avatar
]);
