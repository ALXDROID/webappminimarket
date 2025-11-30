<?php
header("Content-Type: application/json");
require "conexion.php";

$input = json_decode(file_get_contents("php://input"), true);

$id = intval($input["id"] ?? 0);
$nombre = $conexion->real_escape_string($input["nombre"]);

if ($id == 0) {
    // agregar
    $conexion->query("INSERT INTO categorias (nombre) VALUES ('$nombre')");
    echo json_encode(["mensaje" => "Categoría agregada"]);
} else {
    // editar
    $conexion->query("UPDATE categorias SET nombre='$nombre' WHERE id=$id");
    echo json_encode(["mensaje" => "Categoría actualizada"]);
}
?>
