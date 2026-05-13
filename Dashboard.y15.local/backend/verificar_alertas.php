<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

include("conexion.php");
include("telegram.php");

// 🔹 Obtener configuración
$config = $conexion->query("SELECT * FROM configuracion LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if (!$config) {
    echo json_encode([]);
    exit();
}

$id_config = $config['id'];
$temp_max = $config['temp_max'];
$temp_min = $config['temp_min'];
$hum_max  = $config['hum_max'];
$hum_min  = $config['hum_min'];

// 🔹 Obtener datos actuales

// 🌡️ Temperatura ambiente
$temp = $conexion->query("
    SELECT valor FROM datos 
    WHERE tipo='temperatura' AND sensor=2
    ORDER BY fecha DESC 
    LIMIT 1
")->fetchColumn();

// 💧 Humedad
$hum = $conexion->query("
    SELECT valor FROM datos 
    WHERE tipo='humedad' AND sensor=1
    ORDER BY fecha DESC 
    LIMIT 1
")->fetchColumn();

// 🌊 Temperatura lago
$temp_lago = $conexion->query("
    SELECT valor FROM datos
    WHERE sensor=3
    ORDER BY fecha DESC
    LIMIT 1
")->fetchColumn();

// 🔥 Variables de mensajes
$mensaje_ambiente = "";
$mensaje_lago = "";
$nuevo_estado = "normal";

// =========================
// 🔍 VALIDAR AMBIENTE
// =========================

if ($temp !== false) {
    if ($temp > $temp_max) {
        $mensaje_ambiente .= "🔥 Temp AMBIENTE ALTA: $temp °C\n";
        $nuevo_estado = "alerta";
    }
    if ($temp < $temp_min) {
        $mensaje_ambiente .= "❄️ Temp AMBIENTE BAJA: $temp °C\n";
        $nuevo_estado = "alerta";
    }
}

if ($hum !== false) {
    if ($hum > $hum_max) {
        $mensaje_ambiente .= "💧 Humedad ALTA: $hum%\n";
        $nuevo_estado = "alerta";
    }
    if ($hum < $hum_min) {
        $mensaje_ambiente .= "🏜️ Humedad BAJA: $hum%\n";
        $nuevo_estado = "alerta";
    }
}

// =========================
// 🔍 VALIDAR LAGO
// =========================

if ($temp_lago !== false) {
    if ($temp_lago > $temp_max) {
        $mensaje_lago .= "🌊 Temp LAGO ALTA: $temp_lago °C\n";
        $nuevo_estado = "alerta";
    }
    if ($temp_lago < $temp_min) {
        $mensaje_lago .= "❄️ Temp LAGO BAJA: $temp_lago °C\n";
        $nuevo_estado = "alerta";
    }
}

// =========================
// 🚨 ENVÍO DE ALERTAS
// =========================

// 🌡️ Ambiente
if ($mensaje_ambiente != "") {

    $mensajeCompleto = "🚨 ALERTA AMBIENTE\n" . $mensaje_ambiente;

    $stmt = $conexion->prepare("INSERT INTO alertas (mensaje) VALUES (?)");
    $stmt->execute([$mensajeCompleto]);

    enviarTelegram($mensajeCompleto);
}

// 🌊 Lago
if ($mensaje_lago != "") {

    $mensajeCompleto = "🚨 ALERTA LAGO\n" . $mensaje_lago;

    $stmt = $conexion->prepare("INSERT INTO alertas (mensaje) VALUES (?)");
    $stmt->execute([$mensajeCompleto]);

    enviarTelegram($mensajeCompleto);
}

// 🔥 Actualizar estado general
$stmt = $conexion->prepare("UPDATE configuracion SET estado_actual=?, fecha=NOW() WHERE id=?");
$stmt->execute([$nuevo_estado, $id_config]);

// 🔹 devolver última alerta
$ultima = $conexion->query("SELECT * FROM alertas ORDER BY id DESC LIMIT 1")
                   ->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($ultima);
