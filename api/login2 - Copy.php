<?php
ob_start(); // evita que cualquier warning arruine el JSON
header("Content-Type: application/json; charset=UTF-8");

// OPTIONS (preflight CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require "conexion.php";

// limpiar basura de conexion.php
ob_clean();

// leer JSON
$input = json_decode(file_get_contents("php://input"), true);

$nombre   = trim($input["nombre"] ?? "");
$telefono = trim($input["telefono"] ?? "");

if ($nombre === "" || $telefono === "") {
    echo json_encode([
        "success" => false,
        "error"   => "Faltan datos"
    ]);
    exit;
}

// --------------------------------------------------
// 1) BUSCAR USUARIO EXISTENTE
// --------------------------------------------------
$stmt = $conexion->prepare("
    SELECT id, nombre, telefono 
    FROM usuarios 
    WHERE nombre=? AND telefono=? 
    LIMIT 1
");
$stmt->bind_param("ss", $nombre, $telefono);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {

    echo json_encode([
        "success"  => true,
        "id"       => (int)$row["id"],
        "nombre"   => $row["nombre"],
        "telefono" => $row["telefono"]
    ]);
    exit;
}

// --------------------------------------------------
// 2) SI NO EXISTE → CREAR USUARIO NUEVO
// --------------------------------------------------
$stmt2 = $conexion->prepare("
    INSERT INTO usuarios (nombre, telefono)
    VALUES (?, ?)
");
$stmt2->bind_param("ss", $nombre, $telefono);
$stmt2->execute();

echo json_encode([
    "success"  => true,
    "id"       => (int)$stmt2->insert_id,
    "nombre"   => $nombre,
    "telefono" => $telefono
]);