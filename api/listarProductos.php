<?php
require "conexion.php";

$res = $conexion->query("SELECT id, nombre, precio, categoria, imagen FROM productos WHERE activo=1");
$data = [];

while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
