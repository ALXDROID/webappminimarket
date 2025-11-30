<?php
header("Content-Type: application/json");

// Carpeta donde se guardarán
$uploadDir = __DIR__ . "/../img/";

// Si no existe, crearla
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// VALIDAR ARCHIVO
if (!isset($_FILES["avatar"])) {
    echo json_encode(["success" => false, "error" => "No se recibió archivo"]);
    exit;
}

$img = $_FILES["avatar"];

// Validar errores de subida
if ($img["error"] !== UPLOAD_ERR_OK) {
    echo json_encode([
        "success" => false,
        "error" => "Error en upload: " . $img["error"]
    ]);
    exit;
}

// Extensión permitida
$ext = strtolower(pathinfo($img["name"], PATHINFO_EXTENSION));
if (!in_array($ext, ["jpg","jpeg","png","gif"])) {
    $ext = "png"; // forzar png si no viene extensión
}

// Nombre final
$filename = "avatar_" . uniqid() . "." . $ext;
$target = $uploadDir . $filename;

// Mover archivo
if (!move_uploaded_file($img["tmp_name"], $target)) {
    echo json_encode([
        "success" => false,
        "error" => "No se pudo mover el archivo"
    ]);
    exit;
}

// URL pública en localhost
$url = "http://localhost/MiniMarket/img/" . $filename;

echo json_encode([
    "success" => true,
    "url" => $url
]);



// <!-- <?php
// header("Content-Type: application/json; charset=UTF-8");

// // Carpeta donde se guardarán los avatares
// $targetDir = "../img/";

// // Crear carpeta si no existe
// if (!file_exists($targetDir)) {
//     mkdir($targetDir, 0777, true);
// }

// // Validar archivo
// if (!isset($_FILES["avatar"])) {
//     echo json_encode([
//         "success" => false,
//         "error" => "No se recibió ninguna imagen"
//     ]);
//     exit;
// }

// // Nombre único
// $filename = "avatar_" . uniqid() . ".png";
// $filepath = $targetDir . $filename;

// // Mover archivo subido
// if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $filepath)) {

//     echo json_encode([
//         "success" => true,
//         "url" => "http://localhost/MiniMarket/img/" . $filename
//     ]);
//     exit;

// } else {

//     echo json_encode([
//         "success" => false,
//         "error" => "Error al guardar la imagen"
//     ]);
//     exit;
// }
// 
//  