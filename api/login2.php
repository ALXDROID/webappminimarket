<?php
ob_start();
header("Content-Type: application/json; charset=UTF-8");

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require "conexion.php";
ob_clean();

$input = json_decode(file_get_contents("php://input"), true);

$nombre   = trim($input["nombre"] ?? "");
$telefono = trim($input["telefono"] ?? "");
$avatar   = trim($input["avatar"] ?? "");

if ($nombre === "" || $telefono === "") {
    echo json_encode(["success"=>false, "error"=>"Faltan datos"]);
    exit;
}

// --------------------------------------
// 1) BUSCAR USUARIO
// --------------------------------------
$stmt = $conexion->prepare("
    SELECT id, nombre, telefono, avatar 
    FROM usuarios 
    WHERE telefono = ? 
    LIMIT 1
");
$stmt->bind_param("s", $telefono);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {

    $id = (int)$row["id"];
    $avatarBD = $row["avatar"]; // avatar real guardado

    // SOLO actualizar avatar si viene uno nuevo real
    if ($avatar !== "" && $avatar !== $avatarBD) {
        $stmt2 = $conexion->prepare("UPDATE usuarios SET avatar=? WHERE id=?");
        $stmt2->bind_param("si", $avatar, $id);
        $stmt2->execute();
        $avatarBD = $avatar;
    }

    echo json_encode([
        "success"  => true,
        "id"       => $id,
        "nombre"   => $row["nombre"],
        "telefono" => $row["telefono"],
        "avatar"   => $avatarBD
    ]);
    exit;
}

// --------------------------------------
// 2) SI NO EXISTE → CREAR USUARIO
// --------------------------------------
$stmt2 = $conexion->prepare("
    INSERT INTO usuarios (nombre, telefono, avatar) 
    VALUES (?, ?, ?)
");
$stmt2->bind_param("sss", $nombre, $telefono, $avatar);
$stmt2->execute();

echo json_encode([
    "success"  => true,
    "id"       => (int)$stmt2->insert_id,
    "nombre"   => $nombre,
    "telefono" => $telefono,
    "avatar"   => $avatar
]);
exit;
