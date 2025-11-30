<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

require "conexion.php";

// Leer el JSON enviado desde fetch()
$input = json_decode(file_get_contents("php://input"), true);

$nombre = $input["nombre"] ?? null;
$telefono = $input["telefono"] ?? null;

if (!$nombre || !$telefono) {
    echo json_encode(["success" => false, "error" => "Faltan datos"]);
    exit;
}

// Buscar usuario existente
$sql = "SELECT id FROM usuarios WHERE nombre=? AND telefono=?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ss", $nombre, $telefono);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $user = $res->fetch_assoc();
    echo json_encode(["success" => true, "id" => $user["id"]]);
    exit;
}

// Si no existe, crear usuario
$sql2 = "INSERT INTO usuarios (nombre, telefono) VALUES (?,?)";
$stmt2 = $conexion->prepare($sql2);
$stmt2->bind_param("ss", $nombre, $telefono);
$stmt2->execute();

echo json_encode(["success" => true, "id" => $stmt2->insert_id]);
?>

