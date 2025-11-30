<?php
header("Content-Type: application/json; charset=UTF-8");
require "conexion.php";

// ---------- LEER JSON CORRECTAMENTE ----------
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "error" => "JSON vacío"]);
    exit;
}

$id = intval($data["id"] ?? 0);
$estado = $conexion->real_escape_string($data["estado"] ?? "pendiente");

// ---------- VALIDACIONES ----------
if ($id <= 0) {
    echo json_encode(["success" => false, "error" => "ID inválido"]);
    exit;
}

if (!in_array($estado, ["pendiente", "pagado", "anulado"])) {
    echo json_encode(["success" => false, "error" => "Estado inválido"]);
    exit;
}

// ---------- ACTUALIZAR ----------
$q = $conexion->prepare("UPDATE pedidos SET estado=? WHERE id=?");
$q->bind_param("si", $estado, $id);

if (!$q->execute()) {
    echo json_encode(["success" => false, "error" => $q->error]);
    exit;
}

$q->close();

// ---------- RESPUESTA ----------
echo json_encode(["success" => true]);
