<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require "conexion.php";

$data = json_decode(file_get_contents("php://input"), true);

$nombre   = trim($data["nombre"] ?? "");
$telefono = trim($data["telefono"] ?? "");

if ($nombre === "" || $telefono === "") {
    echo json_encode(["success" => false, "error" => "Datos incompletos"]);
    exit;
}

// Verificar si usuario existe
$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE telefono = ?");
$stmt->bind_param("s", $telefono);
$stmt->execute();
$res = $stmt->get_result();

if ($u = $res->fetch_assoc()) {
    echo json_encode(["success" => true, "user" => $u]);
    exit;
}
$stmt->close();

// Crear usuario
$stmt = $conexion->prepare("INSERT INTO usuarios (nombre, telefono) VALUES (?, ?)");
$stmt->bind_param("ss", $nombre, $telefono);
$stmt->execute();

$id = $stmt->insert_id;

echo json_encode([
    "success" => true,
    "user" => [
        "id" => $id,
        "nombre" => $nombre,
        "telefono" => $telefono
    ]
]);
