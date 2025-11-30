<?php
header("Content-Type: application/json");

require "conexion.php";

$sql = "SELECT id, nombre, precio, categoria, imagen, stock 
        FROM productos
        WHERE activo = 1";
$res = $conexion->query($sql);

$data = [];
while ($row = $res->fetch_assoc()){
    $data[] = $row;
}

echo json_encode(["success"=>true, "productos"=>$data]);
?>