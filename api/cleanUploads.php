<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

// PROTECCIÓN SIMPLE
if (!isset($_GET["key"]) || $_GET["key"] !== "secret123") {
    die("Acceso denegado.");
}

require "conexion.php";  // <-- Ruta correcta para InfinityFree

// RUTA REAL DE LA CARPETA (relativa al api)
$uploadsDir = realpath(__DIR__ . "/../uploads") . "/";

if (!$uploadsDir) {
    die("No se encontró la carpeta uploads.");
}

// OBTENER ARCHIVOS FÍSICOS
$archivos = array_diff(scandir($uploadsDir), ['.', '..']);

// OBTENER IMÁGENES USADAS EN BD
$usadas = [];
$res = $conexion->query("SELECT imagen FROM productos WHERE imagen IS NOT NULL AND imagen <> ''");

while ($row = $res->fetch_assoc()) {
    $usadas[] = $row["imagen"];
}

$usadas = array_unique($usadas);

// LISTAS PARA RESPUESTA
$eliminadas = [];
$conservadas = [];

foreach ($archivos as $file) {

    // Si el archivo NO está en BD → BORRAR
    if (!in_array($file, $usadas)) {

        if (unlink($uploadsDir . $file)) {
            $eliminadas[] = $file;
        }

    } else {
        $conservadas[] = $file;
    }
}

echo json_encode([
    "success" => true,
    "usadas" => $conservadas,
    "eliminadas" => $eliminadas
]);
