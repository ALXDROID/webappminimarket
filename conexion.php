<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json");
$host = "sql10.freesqldatabase.com";
$user = "sql10809720";
$pass = "V7Lizqjv4n";
$db   = "sql10809720";

$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    http_response_code(500);
    die(json_encode(["success" => false, "error" => "Error de conexión BD"]));
}

$conexion->set_charset("utf8mb4");
?>
