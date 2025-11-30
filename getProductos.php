<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

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
