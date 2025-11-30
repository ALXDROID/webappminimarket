<?php
require "conexion.php";

$id = intval($_GET["id"] ?? 0);

$r = $conexion->query("SELECT * FROM productos WHERE id=$id");
echo json_encode($r->fetch_assoc());
?>
