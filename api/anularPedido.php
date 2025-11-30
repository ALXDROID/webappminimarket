<?php
header("Content-Type: application/json");
require "conexion.php";

$id = intval($_POST["id"] ?? 0);

$q = $conexion->prepare("UPDATE pedidos SET estado='anulado' WHERE id=? AND estado='pendiente'");
$q->bind_param("i", $id);
$q->execute();

if ($q->affected_rows > 0) {
    echo json_encode(["success"=>true]);
} else {
    echo json_encode(["success"=>false, "error"=>"No se puede anular (ya procesado)"]);
}
