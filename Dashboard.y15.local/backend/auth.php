<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$rol = $_SESSION['rol'] ?? 'invitado';

// Archivos permitidos para invitados
$permitidos_invitado = ['dashboard.php', 'base_datos.php'];

// Archivo actual
$archivo_actual = basename($_SERVER['PHP_SELF']);

// Si es invitado y no está en la lista → bloquear
if ($rol === 'invitado' && !in_array($archivo_actual, $permitidos_invitado)) {
    header("Location: dashboard.php");
    exit();
}