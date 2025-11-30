<?php
require "conexion.php";

$res = $conexion->query("SELECT id, nombre, activo FROM categorias");
$cats = [];

while ($c = $res->fetch_assoc()) {
    $cats[] = $c;
}

echo json_encode($cats);
?>

