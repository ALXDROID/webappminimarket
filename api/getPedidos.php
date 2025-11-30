<?php
header("Content-Type: application/json; charset=UTF-8");
require "conexion.php";

$sql = "SELECT 
            p.id, 
            p.tipo, 
            p.metodo, 
            p.direccion, 
            p.receptor, 
            p.telefono,
            p.total, 
            p.fecha, 
            p.hora_entrega,   -- AQUI!! 🔥🔥🔥
            p.estado,
            u.nombre AS usuario
        FROM pedidos p
        INNER JOIN usuarios u ON u.id = p.id_usuario
        ORDER BY p.fecha DESC";

$res = $conexion->query($sql);

$pedidos = [];

while ($row = $res->fetch_assoc()) {

    // cargar items
    $items = [];
    $q = $conexion->query("SELECT pi.*, pr.nombre
                           FROM pedido_items pi
                           INNER JOIN productos pr ON pr.id = pi.id_producto
                           WHERE pi.id_pedido = ".$row["id"]);

    while ($i = $q->fetch_assoc()) { 
        $items[] = $i; 
    }

    $row["items"] = $items;
    $pedidos[] = $row;
}

echo json_encode(["success"=>true, "pedidos"=>$pedidos]);
