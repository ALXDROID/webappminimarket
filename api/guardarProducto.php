<?php
header("Content-Type: application/json");
require __DIR__ . "/conexion.php";

// ==========================
// LEER JSON
// ==========================
$input = json_decode(file_get_contents("php://input"), true);

$nombre    = trim($input["nombre"]    ?? "");
$precio    = floatval($input["precio"] ?? 0);
$categoria = trim($input["categoria"] ?? "");
$imagen    = trim($input["imagen"]    ?? "");

// ==========================
// VALIDACIONES
// ==========================
if ($nombre === "" || $categoria === "" || $imagen === "" || $precio <= 0) {
    echo json_encode([
        "success" => false,
        "error" => "Datos inválidos o incompletos"
    ]);
    exit;
}

// Validar que la imagen sea SOLO el nombre
if (strpos($imagen, "/") !== false || strpos($imagen, "http") !== false) {
    echo json_encode([
        "success" => false,
        "error" => "El campo imagen debe ser solo el nombre de archivo"
    ]);
    exit;
}

// ==========================
// INSERTAR
// ==========================
$sql = "INSERT INTO productos (nombre, precio, categoria, imagen, stock, activo)
        VALUES (?, ?, ?, ?, 10, 1)";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "error" => "Error preparando SQL: " . $conexion->error
    ]);
    exit;
}

$stmt->bind_param("sdss", $nombre, $precio, $categoria, $imagen);

if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "id"      => $stmt->insert_id,
        "mensaje" => "Producto agregado correctamente"
    ]);

} else {

    echo json_encode([
        "success" => false,
        "error"   => "Error ejecutando SQL: " . $stmt->error
    ]);

}

$stmt->close();
$conexion->close();
?>
