<?php ini_set('display_errors', 1);
error_reporting(E_ALL);?>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$rol = $_SESSION['rol'] ?? 'invitado';
include("backend/conexion.php");
include("backend/auth.php");

/* VERIFICAR LOGIN */
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

/* CONSULTAR DATOS */

$buscar = $_GET['buscar'] ?? '';
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';

$sql = "SELECT * FROM datos WHERE 1=1";

if($buscar != ''){
$sql .= " AND (
id LIKE '%$buscar%' OR
fecha LIKE '%$buscar%' OR
valor LIKE '%$buscar%' OR
sensor LIKE '%$buscar%'
)";
}


/* Botones de sig pag BD */

$limite = 10;

$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

$inicio = ($pagina - 1) * $limite;

/*Filtros RH*/

$tipo_consulta = $_GET['tipo_consulta'] ?? '';

if($tipo_consulta == 'fecha' && !empty($_GET['fecha_unica'])){

    $fecha = $_GET['fecha_unica'];

    $sql_reporte = "
    SELECT *
    FROM datos
    WHERE DATE(fecha) = '$fecha'
    ORDER BY fecha DESC
    ";

    $stmt = $conexion->prepare($sql_reporte);
    $stmt->execute();
    $datos_reporte = $stmt->fetchAll(PDO::FETCH_ASSOC);

}elseif($tipo_consulta == 'rango' && $fecha_inicio != '' && $fecha_fin != ''){

if (!empty($fecha_inicio) && !empty($fecha_fin)){

    $sql_reporte = "
    SELECT *
    FROM datos
    WHERE DATE(fecha) BETWEEN '$fecha_inicio' AND '$fecha_fin'
    ORDER BY fecha DESC
    ";
}

    $stmt = $conexion->prepare($sql_reporte);
    $stmt->execute();
    $datos_reporte = $stmt->fetchAll(PDO::FETCH_ASSOC);
}


$sql .= " ORDER BY fecha DESC LIMIT $inicio, $limite";

$total_registros = $conexion->query("SELECT COUNT(*) FROM datos")->fetchColumn();

$total_paginas = ceil($total_registros / $limite);

$stmt = $conexion->prepare($sql);
$stmt->execute();
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = $conexion->query("SELECT COUNT(*) FROM datos")->fetchColumn();

$hoy = $conexion->query("
SELECT COUNT(*) 
FROM datos 
WHERE DATE(fecha) = CURDATE()
")->fetchColumn();

$sensores = $conexion->query("
SELECT COUNT(DISTINCT sensor) 
FROM datos
")->fetchColumn();

$espacio = $conexion->query("
SELECT ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'login'
AND TABLE_NAME = 'datos'
")->fetchColumn();

/*TEMPERATURA*/

$stats_temp = $conexion->query("
SELECT 
MAX(valor) as max_temp,
MIN(valor) as min_temp,
AVG(valor) as avg_temp,
COUNT(*) as total
FROM datos
WHERE tipo = 'temperatura'
AND DATE(fecha) = CURDATE()
")->fetch(PDO::FETCH_ASSOC);
echo $stats_temp['total'] ?? 0;

/*HUMEDAD*/

$stats_hum = $conexion->query("
SELECT 
MAX(valor) as max_hum,
MIN(valor) as min_hum,
AVG(valor) as avg_hum,
COUNT(*) as total
FROM datos
WHERE tipo = 'humedad'
AND DATE(fecha) = CURDATE()
")->fetch(PDO::FETCH_ASSOC);
echo $stats_hum['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Base de Datos</title>
<link rel="icon" href="img/logos/logopre.png?v=1" type="image/png">
<link rel="stylesheet" href="css/dashboard.css">
<link rel="stylesheet" href="css/base_datos.css">
<meta name="viewport" content="width=1450">
<link rel="stylesheet" href="css/layout.css">
</head>

<body>

<?php include("backend/sidebar.php"); ?>

<main class="contenido">

<h1>Base de Datos y Reportes Históricos</h1>
<p>Gestión completa de datos almacenados</p>


<div class="tabs-container">

<button type="button" class="tab-btn active" onclick="mostrarTab('baseDatos', this)">
Base de Datos
</button>

<button type="button" class="tab-btn" onclick="mostrarTab('reportes', this)">
Reportes Históricos
</button>
</div>

<div class="cards-container">

  <div class="card card-blue">
    <p class="card-title">Total de Registros</p>
    <h2><?php echo $total; ?></h2>
    <span class="card-sub">Todas las lecturas</span>
  </div>

  <div class="card card-purple">
    <p class="card-title">Registros Hoy</p>
    <h2><?php echo $hoy;?></h2>
    <span class="card-sub">Últimas 24 horas</span>
  </div>

  <div class="card card-green">
    <p class="card-title">Sensores Activos</p>
    <h2><?php echo $sensores; ?></h2>
    <span class="card-sub">En operación</span>
  </div>

  <div class="card card-orange">
    <p class="card-title">Espacio en DB</p>
    <h2><?php echo $espacio; ?> MB</h2>
    <span class="card-sub">De 500 MB disponibles</span>
  </div>

</div>

<form method="GET" class="filtros-card">

<input type="hidden" name="tab" value="baseDatos">

<div id="baseDatos" class="tab-content active">

<h3>Búsqueda y Filtros</h3>

<div class="filtros-grid">

<div class="campo">
<label>Buscar</label>
<input type="text" name="buscar" placeholder="Buscar por sensor, fecha...">
</div>

<div class="campo">
<label>Fecha Inicio</label>
<input type="date" name="fecha_inicio">
</div>

<div class="campo"> 
<label>Fecha Fin</label>
<input type="date" name="fecha_fin">
</div>

<div class="campo boton">
<label>Acción</label>
<button class="btn-buscar">Buscar</button>
</div>

</div>
<br><br>
</form>

<table class="tabla-datos">

<thead>
<tr>
<th>ID</th>
<th>Fecha</th>
<th>Hora</th>
<th>Temperatura</th>
<th>Humedad</th>
<th>Sensor</th>
<th>Ubicación</th>
</tr>
</thead>

<tbody>

<?php if(count($datos) > 0): ?>
<?php foreach($datos as $fila): ?>

<tr>

<td><?php echo $fila['id']; ?></td>

<td><?php echo date("Y-m-d", strtotime($fila['fecha'])); ?></td>

<td><?php echo date("H:i:s", strtotime($fila['fecha'])); ?></td>

<td><?php echo $fila['tipo']=="temperatura" ? $fila['valor']."°C" : "-"; ?></td>

<td><?php echo $fila['tipo']=="humedad" ? $fila['valor']."%" : "-"; ?></td>

<td><?php switch($fila['sensor']){
    case 1: echo "Sensor 1 - Humedad"; break;
    case 2: echo "Sensor 2 - Temp. Ambiente"; break;
    case 3: echo "Sensor 3 - Temp. Lago"; break;
    default: echo "Desconocido";
}
?></td>

<td>Lago</td>

</tr>

<?php endforeach; ?>
<?php else: ?>

<tr>
<td colspan="7" style="text-align:center; padding:20px;">
No se encontraron datos
</td>
</tr>

<?php endif; ?>

</tbody>

</table>

<div class="paginacion">

<?php
$max_paginas = 5;

$inicio = max(1, $pagina - 2);
$fin = min($total_paginas, $inicio + $max_paginas - 1);

if ($inicio > 1) {
    $inicio = max(1, $fin - $max_paginas + 1);
}
?>

<?php if($pagina > 1): ?>
<a class="btn" href="?pagina=<?php echo $pagina-1; ?>">Anterior</a>
<?php endif; ?>


<?php for($i = $inicio; $i <= $fin; $i++): ?>

<a class="<?php echo ($i==$pagina)?'activo':'';?>"
href="?pagina=<?php echo $i; ?>">
<?php echo $i; ?>
</a>

<?php endfor; ?>


<?php if($pagina < $total_paginas): ?>
<a class="btn" href="?pagina=<?php echo $pagina+1; ?>">Siguiente</a>
<?php endif; ?>

</div>
</div>

<script src="js/main.js"></script>
<script src="js/sidebar.js"></script>

<!-- Reportes Historicos-->

<div id="reportes" class="tab-content">

<!-- FILTROS -->

<h3>Filtros de Consulta</h3>

<div class="filtros-box">

  <form method="GET">

<input type="hidden" name="tab" value="reportes">

    <div class="tipo-consulta">
      <label>Tipo de Consulta</label>

      <label>
        <input type="radio" name="tipo_consulta" value="fecha"
         <?php echo ($tipo_consulta == 'fecha') ? 'checked' : ''; ?>>
        Fecha Específica
      </label>

      <label>
        <input type="radio" name="tipo_consulta" value="rango"
         <?php echo ($tipo_consulta == 'rango' || $tipo_consulta == '') ? 'checked' : ''; ?>>
        Rango de Fechas
      </label>
    </div>

    <div class="filtros-grid-reportes">

      <!-- FECHA ÚNICA -->
      <div class="campo" id="campoUnico">
        <label>Fecha</label>
        <input type="date" name="fecha_unica">
      </div>

    <!-- RANGO -->
      <div class="campo" id="campoRango">
        <label>Fecha Inicio</label>
        <input type="date" name="fecha_inicio">
      </div>

      <div class="campo" id="campoFin">
        <label>Fecha Fin</label>
        <input type="date" name="fecha_fin">
      </div>

      <div class="campo boton">
        <label>Acción</label>
        <button type="submit" class="btn-consultar">
          <i class="fa fa-file"></i> Consultar Datos
        </button>
      </div>

    </div>

  </form>

</div>


 <!--Cuadros Promedio-->

<div class="resumen">

  <div class="resumen-card temperatura">
    <h3>Resumen Estadístico - Temperatura</h3>
    <div class="fila">
      <span>Valor Máximo</span>
      <strong><?php echo round($stats_temp['max_temp'] ?? 0,2); ?>°C</strong>
    </div>
    <div class="linea"></div>
    <div class="fila">
      <span>Valor Mínimo</span>
      <strong><?php echo round($stats_temp['min_temp'] ?? 0,2); ?>°C</strong>
    </div>
    <div class="linea"></div>
    <div class="fila">
      <span>Promedio</span>
      <strong><?php echo round($stats_temp['avg_temp'] ?? 0,2); ?>°C</strong>
    </div>
    <div class="linea"></div>
    <p class="registros">
      Basado en <?php echo $stats_temp['total']; ?> registros
    </p>
  </div>

  <div class="resumen-card humedad">
    <h3>Resumen Estadístico - Humedad</h3>
    <div class="fila">
      <span>Valor Máximo</span>
      <strong><?php echo round($stats_hum['max_hum'] ?? 0,2); ?>%</strong>
    </div>
    <div class="linea"></div>
    <div class="fila">
      <span>Valor Mínimo</span>
      <strong><?php echo round($stats_hum['min_hum'] ?? 0,2); ?>%</strong>
    </div>
    <div class="linea"></div>
    <div class="fila">
      <span>Promedio</span>
      <strong><?php echo round($stats_hum['avg_hum'] ?? 0,2); ?>%</strong>
    </div>
    <div class="linea"></div>
    <p class="registros">
      Basado en <?php echo $stats_hum['total']; ?> registros
    </p>
  </div>

</div>

<?php

$datos_reporte = [];

$pagina2 = isset($_GET['pagina2']) ? max(1, (int)$_GET['pagina2']) : 1;
$total_paginas2 = 0;
$inicio2_rango = 1;
$fin2_rango = 1;

if($tipo_consulta == 'fecha' && !empty($_GET['fecha_unica'])){

    $fecha = $_GET['fecha_unica'];

    $limite2 = 10;
    $inicio2 = max(0, ($pagina2 - 1) * $limite2);

    $total_registros2 = $conexion->query("
    SELECT COUNT(*) FROM datos
    WHERE DATE(fecha) = '$fecha'
    ")->fetchColumn();

    $total_paginas2 = ceil($total_registros2 / $limite2);

    $max_paginas2 = 5;

    $inicio2_rango = max(1, $pagina2 - 2);
    $fin2_rango = min($total_paginas2, $inicio2_rango + $max_paginas2 - 1);

    if ($inicio2_rango > 1) {
        $inicio2_rango = max(1, $fin2_rango - $max_paginas2 + 1);
    }

    $sql_reporte = "
    SELECT * FROM datos
    WHERE DATE(fecha) = '$fecha'
    ORDER BY fecha DESC
    LIMIT $inicio2, $limite2
    ";

    $stmt = $conexion->prepare($sql_reporte);
    $stmt->execute();
    $datos_reporte = $stmt->fetchAll(PDO::FETCH_ASSOC);

}

elseif($tipo_consulta == 'rango' && !empty($fecha_inicio) && !empty($fecha_fin)){

    $limite2 = 10;
    $inicio2 = max(0, ($pagina2 - 1) * $limite2);

    // TOTAL DE REGISTROS
    $total_registros2 = $conexion->query("
    SELECT COUNT(*) FROM datos
    WHERE DATE(fecha) BETWEEN '$fecha_inicio' AND '$fecha_fin'
    ")->fetchColumn();

    $total_paginas2 = ceil($total_registros2 / $limite2);

    $max_paginas2 = 5;

    $inicio2_rango = max(1, $pagina2 - 2);
    $fin2_rango = min($total_paginas2, $inicio2_rango + $max_paginas2 - 1);

if ($inicio2_rango > 1) {
    $inicio2_rango = max(1, $fin2_rango - $max_paginas2 + 1);
}

    // CONSULTA CON PAGINACIÓN
    $sql_reporte = "
    SELECT * FROM datos
    WHERE DATE(fecha) BETWEEN '$fecha_inicio' AND '$fecha_fin'
    ORDER BY fecha DESC
    LIMIT $inicio2, $limite2
    ";

    $stmt = $conexion->prepare($sql_reporte);
    $stmt->execute();
    $datos_reporte = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>

<?php if(!empty($datos_reporte)): ?>

<div class="tabla-reporte">

<h3>Resultados del Reporte</h3>

<table>

<thead>
<tr>
<th>ID</th>
<th>Fecha</th>
<th>Hora</th>
<th>Tipo</th>
<th>Valor</th>
<th>Sensor</th>
<th>Ubicación</th>
</tr>
</thead>

<tbody>

<?php foreach($datos_reporte as $fila): ?>

<tr>
<td><?php echo $fila['id']; ?></td>
<td><?php echo date("Y-m-d", strtotime($fila['fecha'])); ?></td>
<td><?php echo date("H:i:s", strtotime($fila['fecha'])); ?></td>
<td><?php echo $fila['tipo']; ?></td>
<td>
<?php 
if($fila['tipo']=="temperatura"){
echo $fila['valor']."°C";
}else{
echo $fila['valor']."%";
}
?>
</td>

<td><?php switch($fila['sensor']){
    case 1: echo "Sensor 1 - Humedad"; break;
    case 2: echo "Sensor 2 - Temp. Ambiente"; break;
    case 3: echo "Sensor 3 - Temp. Lago"; break;
    default: echo "Desconocido";
}
?></td>
<td>Lago</td>
</tr>

<?php endforeach; ?>

</tbody>
</table>

</div>

<?php else: ?>

<p style="text-align:center; margin-top:20px;">
No hay resultados para esta consulta
</p>

<?php endif; ?>

<?php if(!empty($datos_reporte)): ?>

<div class="paginacion2">

<?php if($pagina2 > 1): ?>
<a class="btn" href="?pagina2=<?php echo $pagina2-1; ?>&tab=reportes">Anterior</a>
<?php endif; ?>

<?php for($i = $inicio2_rango; $i <= $fin2_rango; $i++): ?>

<a class="<?php echo ($i==$pagina2)?'activo':'';?>"
href="?pagina2=<?php echo $i; ?>&tab=reportes">
<?php echo $i; ?>
</a>

<?php endfor; ?>

<?php if($pagina2 < $total_paginas2): ?>
<a class="btn" href="?pagina2=<?php echo $pagina2+1; ?>&tab=reportes">Siguiente</a>
<?php endif; ?>

</div>

<?php endif; ?>

<!-- exportar datos -->

<div class="export-box">

 <h3>Exportar Datos</h3>
  <p class="subtexto">
    Descarga los datos consultados en diferentes formatos
  </p>

<div class="exportar-grid">

<a href="backend/exp_excel.php?fecha_inicio=<?php echo $fecha_inicio; ?>&fecha_fin=<?php echo $fecha_fin; ?>" class="export-btn excel">
<i class="fa-solid fa-file-excel"></i>
<img src="img/logos/archivo-excel.png" alt="logo excel">
<span>Exportar XLSX</span>
</a>

<a href="backend/exp_pdf.php?fecha_inicio=<?php echo $fecha_inicio; ?>&fecha_fin=<?php echo $fecha_fin; ?>" class="export-btn pdf">
<i class="fa-solid fa-file-pdf"></i>
<img src="img/logos/archivo-pdf.png" alt="logo pdf">
<span>Exportar PDF</span>
</a>

</div>

</div>

</div>
</div>

</main>
<script>

document.addEventListener("DOMContentLoaded", function(){

const params = new URLSearchParams(window.location.search);

if(params.get("tab") === "reportes"){
    
    document.getElementById("baseDatos").classList.remove("active");
    document.getElementById("reportes").classList.add("active");

    const botones = document.querySelectorAll(".tab-btn");
    
    botones[0].classList.remove("active");
    botones[1].classList.add("active");
}

});


function cambiarTipoConsulta() {
    const tipo = document.querySelector('input[name="tipo_consulta"]:checked').value;

    const contenedor = document.querySelector(".filtros-grid-reportes");
    const unico = document.getElementById("campoUnico");
    const inicio = document.getElementById("campoRango");
    const fin = document.getElementById("campoFin");

    if (tipo === "fecha") {
        unico.style.display = "block";
        inicio.style.display = "none";
        fin.style.display = "none";

        contenedor.classList.add("modo-unico");

    } else {
        unico.style.display = "none";
        inicio.style.display = "block";
        fin.style.display = "block";

        contenedor.classList.remove("modo-unico");
    }
}

document.addEventListener("DOMContentLoaded", function(){
    cambiarTipoConsulta();

    const radios = document.querySelectorAll('input[name="tipo_consulta"]');
    radios.forEach(r => r.addEventListener("change", cambiarTipoConsulta));
});

</script>
</body>
</html>
