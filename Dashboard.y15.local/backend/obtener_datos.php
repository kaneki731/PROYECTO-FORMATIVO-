<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
include("conexion.php");
$sql_temp = "SELECT fecha, valor FROM datos
             WHERE tipo='temperatura'
             ORDER BY fecha DESC
             LIMIT 20";

$sql_hum = "SELECT fecha, valor FROM datos
            WHERE tipo='humedad'
            ORDER BY fecha DESC
            LIMIT 20";

$temp = $conexion->query($sql_temp)->fetchAll(PDO::FETCH_ASSOC);
$hum  = $conexion->query($sql_hum)->fetchAll(PDO::FETCH_ASSOC);

// 🔵 LAGO
$lago = $conexion->query("
    SELECT valor, fecha
    FROM datos
    ORDER BY fecha ASC
")->fetchAll(PDO::FETCH_ASSOC);
// ✅ RESPUESTA FINAL (UNA SOLA)
header('Content-Type: application/json');

echo json_encode([
    "temperatura" => array_reverse($temp),
    "humedad"     => array_reverse($hum),
    "lago"        => $lago
], JSON_UNESCAPED_UNICODE);

exit;
