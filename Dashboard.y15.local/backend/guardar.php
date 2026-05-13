<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
include("conexion.php");

// 🔹 obtener datos
$temp = $_GET['temp'] ?? 0;
$hum  = $_GET['hum'] ?? 0;

try {

    // 🔥 insertar temperatura
    $stmt = $conexion->prepare("INSERT INTO datos (tipo, valor, fecha) VALUES ('temperatura', ?, NOW())");
    $stmt->execute([$temp]);

    // 🔥 insertar humedad
    $stmt = $conexion->prepare("INSERT INTO datos (tipo, valor, fecha) VALUES ('humedad', ?, NOW())");
    $stmt->execute([$hum]);

    // 🔹 actualizar tiempo
    $conexion->exec("UPDATE tiempo SET ultima_actualizacion = NOW() WHERE id = 1");

    echo json_encode(["status" => "ok"]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "mensaje" => $e->getMessage()
    ]);
}
?>
