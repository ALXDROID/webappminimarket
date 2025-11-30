<?php
// ==========================
// CORS
// ==========================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

// Preflight
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

// ==========================
// VERIFICAR ARCHIVO
// ==========================
if (!isset($_FILES["imagen"])) {
    echo json_encode([
        "success" => false,
        "error" => "No se recibió ninguna imagen."
    ]);
    exit;
}

$archivo = $_FILES["imagen"];

// ==========================
// VALIDAR ERRORES
// ==========================
if ($archivo["error"] !== UPLOAD_ERR_OK) {
    echo json_encode([
        "success" => false,
        "error" => "Error al subir la imagen (código {$archivo['error']})"
    ]);
    exit;
}

// ==========================
// VALIDAR TIPO DE ARCHIVO
// ==========================
$permitidos = ["image/jpeg", "image/jpg", "image/png"];
if (!in_array($archivo["type"], $permitidos)) {
    echo json_encode([
        "success" => false,
        "error" => "Formato no permitido. Solo JPG o PNG."
    ]);
    exit;
}

// ==========================
// GENERAR NOMBRE
// ==========================
$ext = strtolower(pathinfo($archivo["name"], PATHINFO_EXTENSION));
$nuevoNombre = uniqid("img_") . "." . $ext;

// ==========================
// RUTA FINAL (FUNCIONA EN INFINITYFREE)
// ==========================
$carpeta = $_SERVER["DOCUMENT_ROOT"] . "/img/";

if (!is_dir($carpeta)) {
    mkdir($carpeta, 0755, true);
}

$destino = $carpeta . $nuevoNombre;

// ==========================
// MOVER EL ARCHIVO
// ==========================
if (!move_uploaded_file($archivo["tmp_name"], $destino)) {
    echo json_encode([
        "success" => false,
        "error" => "No se pudo guardar la imagen (permiso o ruta incorrecta)."
    ]);
    exit;
}

// ==========================
// RESPUESTA
// ==========================
echo json_encode([
    "success" => true,
    "archivo" => $nuevoNombre,
    "url" => "img/" . $nuevoNombre
]);
?>
