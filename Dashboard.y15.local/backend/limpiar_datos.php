<?php
include("conexion.php");

// obtener retención actual
$config = $conexion->query("SELECT retencion FROM configuracion LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$retencion = $config['retencion'] ?? 7;

// eliminar datos viejos dinámicamente
$stmt = $conexion->prepare("
    DELETE FROM datos 
    WHERE fecha < NOW() - INTERVAL ? DAY
");
$stmt->execute([$retencion]);

echo "OK";