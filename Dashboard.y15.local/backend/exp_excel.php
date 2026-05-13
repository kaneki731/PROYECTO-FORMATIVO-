<?php
include("conexion.php");

$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';

$sql = "SELECT * FROM datos WHERE 1=1";

if($fecha_inicio != ''){
$sql .= " AND DATE(fecha) >= '$fecha_inicio'";
}

if($fecha_fin != ''){
$sql .= " AND DATE(fecha) <= '$fecha_fin'";
}

$sql .= " ORDER BY fecha DESC";

$stmt = $conexion->query($sql);

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=reporte.xls");

echo "ID\tFecha\tTipo\tValor\n";

while($fila = $stmt->fetch(PDO::FETCH_ASSOC)){

echo $fila['id']."\t".
     $fila['fecha']."\t".
     $fila['tipo']."\t".
     $fila['valor']."\n";

}
?>