<?php
require "conexion.php";

$id = intval($_GET["id"] ?? 0);

$conexion->query("UPDATE productos SET activo=0 WHERE id=$id");

echo json_encode(["mensaje" => "Producto eliminado"]);
?>
