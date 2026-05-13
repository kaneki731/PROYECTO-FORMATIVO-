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
?>

<html>
<head>
<title>Reporte</title>

<script>
window.onload = function(){
window.print();
}
</script>

<style>
table{
width:100%;
border-collapse:collapse;
}

th,td{
border:1px solid black;
padding:8px;
text-align:center;
}
</style>

</head>

<body>

<h2>Reporte de Sensores</h2>

<table>

<tr>
<th>ID</th>
<th>Fecha</th>
<th>Tipo</th>
<th>Valor</th>
</tr>

<?php while($fila = $stmt->fetch(PDO::FETCH_ASSOC)): ?>

<tr>
<td><?php echo $fila['id']; ?></td>
<td><?php echo $fila['fecha']; ?></td>
<td><?php echo $fila['tipo']; ?></td>
<td><?php echo $fila['valor']; ?></td>
</tr>

<?php endwhile; ?>

</table>

</body>
</html>