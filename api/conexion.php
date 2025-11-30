<?php
    
$host = "localhost";
$user = "root";
$pass = "root";
$db   = "marketdb";

$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    error_log("Error conexión BD: " . $conexion->connect_error);
    http_response_code(500);
    echo "DB_ERROR";  // NO JSON, NO HEADERS
    exit;
}

$conexion->set_charset("utf8mb4");

?>
