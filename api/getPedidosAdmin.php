<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

ob_start();
header("Content-Type: application/json; charset=UTF-8");

require "conexion.php";
ob_clean();

// =============================================
// OBTENER TODOS LOS PEDIDOS
// =============================================
$sql = "
    SELECT 
        p.id,
        p.id_usuario,
        u.nombre AS usuario,
        u.telefono AS tel_usuario,

        p.tipo,
        p.metodo,
        p.estado,
        p.total,
        p.fecha,
        p.hora_entrega,

        p.direccion,
        p.receptor,
        p.telefono AS tel_delivery

    FROM pedidos p
    LEFT JOIN usuarios u ON u.id = p.id_usuario
    ORDER BY p.id DESC
";

$q = $conexion->query($sql);

if (!$q) {
    echo json_encode([
        "success" => false,
        "error" => "SQL ERROR: " . $conexion->error
    ]);
    exit;
}

$pedidos = [];

while ($row = $q->fetch_assoc()) {

    $pid = intval($row["id"]);

    // =============================================
    // OBTENER LOS ITEMS DEL PEDIDO
    // =============================================
    $q2 = $conexion->query("
        SELECT 
            pi.id_producto,
            pr.nombre,
            pi.cantidad,
            pi.subtotal
        FROM pedido_items pi
        INNER JOIN productos pr ON pr.id = pi.id_producto
        WHERE pi.id_pedido = $pid
    ");

    $items = [];
    while ($it = $q2->fetch_assoc()) {
        $items[] = [
            "id"       => intval($it["id_producto"]),
            "nombre"   => $it["nombre"],
            "cantidad" => intval($it["cantidad"]),
            "subtotal" => floatval($it["subtotal"])
        ];
    }

    // =============================================
    // TELEFONO PARA WHATSAPP
    // =============================================
    $tel = $row["tel_usuario"] ?: $row["tel_delivery"];
    $tel = preg_replace("/[^0-9]/", "", $tel);

    $mensaje = urlencode(
        "Hola ".$row["usuario"].", tu pedido #".$pid." está en proceso 🍀"
    );

    $wa = "https://wa.me/{$tel}?text={$mensaje}";

    // =============================================
    // ARMAR PEDIDO
    // =============================================
    $pedidos[] = [
        "id"           => $pid,
        "usuario"      => $row["usuario"],
        "telefono"     => $row["tel_usuario"],
        "telefono_delivery" => $row["tel_delivery"],

        "tipo"         => $row["tipo"],
        "metodo"       => $row["metodo"],
        "estado"       => $row["estado"],
        "hora_entrega" => $row["hora_entrega"],

        "direccion"    => $row["direccion"],
        "receptor"     => $row["receptor"],

        "total"        => floatval($row["total"]),
        "fecha"        => $row["fecha"],

        "items"        => $items,
        "whatsapp"     => $wa
    ];
}

echo json_encode([
    "success" => true,
    "pedidos" => $pedidos
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
