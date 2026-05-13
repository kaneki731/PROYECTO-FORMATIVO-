<?php
header('Content-Type: application/json');
error_reporting(0); // evitar romper JSON

include("conexion.php");
include("telegram.php");

// 🔹 Obtener últimos datos
$sqlTemp = $conexion->query("SELECT valor FROM datos WHERE tipo='temperatura' ORDER BY fecha DESC LIMIT 1");
$temp_actual = $sqlTemp ? $sqlTemp->fetchColumn() : 0;

$sqlHum = $conexion->query("SELECT valor FROM datos WHERE tipo='humedad' ORDER BY fecha DESC LIMIT 1");
$hum_actual = $sqlHum ? $sqlHum->fetchColumn() : 0;

// 🔹 Obtener configuración
$config = $conexion->query("SELECT * FROM configuracion LIMIT 1")->fetch(PDO::FETCH_ASSOC);

$temp_max = $config['temp_max'] ?? 30;
$temp_min = $config['temp_min'] ?? 0;
$hum_max  = $config['hum_max'] ?? 100;
$hum_min  = $config['hum_min'] ?? 0;

$mensaje = "";

// 🔥 VALIDACIONES
if ($temp_actual > $temp_max) {
    $mensaje .= "🔥 Temp ALTA: $temp_actual °C\n";
}
if ($temp_actual < $temp_min) {
    $mensaje .= "❄️ Temp BAJA: $temp_actual °C\n";
}
if ($hum_actual > $hum_max) {
    $mensaje .= "💧 Humedad ALTA: $hum_actual%\n";
}
if ($hum_actual < $hum_min) {
    $mensaje .= "🏜️ Humedad BAJA: $hum_actual%\n";
}

// 🚨 SI HAY ALERTA
if (!empty($mensaje)) {

    $ultima = $conexion->query("SELECT * FROM alertas ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

    $enviar = true;

    if ($ultima) {
        $timeDiff = time() - strtotime($ultima['fecha']);

        if ($ultima['mensaje'] == $mensaje || $timeDiff <= 60) {
            $enviar = false;
        }
    }

    if ($enviar) {
        $mensajeCompleto = "🚨 ALERTA IOT\n" . $mensaje;

        $stmt = $conexion->prepare("INSERT INTO alertas (mensaje) VALUES (?)");
        $stmt->execute([$mensajeCompleto]);

        enviarTelegram($mensajeCompleto);
    }
}

// 🔹 RESPUESTA SEGURA
$ultima = $conexion->query("SELECT * FROM alertas ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

echo json_encode($ultima ?: []); // 🔥 CLAVE
exit;
?>


