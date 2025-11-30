<?php
header("Content-Type: application/json");
require "conexion.php";

$input = json_decode(file_get_contents("php://input"), true);

$id        = intval($input["id"]);
$nombre    = $conexion->real_escape_string($input["nombre"]);
$precio    = floatval($input["precio"]);
$categoria = $conexion->real_escape_string($input["categoria"]);
$imagen    = $conexion->real_escape_string($input["imagen"] ?? "");

if ($id == 0) {
    // INSERTAR
    $sql = "INSERT INTO productos (nombre, precio, categoria, imagen, stock, activo)
            VALUES ('$nombre', $precio, '$categoria', '$imagen', 10, 1)";
    $conexion->query($sql);
    echo json_encode(["mensaje" => "Producto agregado"]);
} else {
    // EDITAR
    $setImg = $imagen ? ", imagen='$imagen'" : "";
    $sql = "UPDATE productos SET nombre='$nombre', precio=$precio, categoria='$categoria' $setImg WHERE id=$id";
    $conexion->query($sql);
    echo json_encode(["mensaje" => "Producto actualizado"]);
}
?>
