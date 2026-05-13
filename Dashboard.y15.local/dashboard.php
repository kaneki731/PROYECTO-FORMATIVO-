<?php
$periodo = isset($_GET['periodo']) ? intval($_GET['periodo']) : 24;
$sensor = isset($_GET['sensor']) ? $_GET['sensor'] : 'todos';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$rol = $_SESSION['rol'] ?? 'invitado';
include("backend/conexion.php");
include("backend/auth.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$sqlSemana = "
SELECT 
DATE(fecha) as dia,

MAX(CASE WHEN tipo='temperatura' AND valor > 0 THEN valor END) as temp_max,
MIN(CASE WHEN tipo='temperatura' AND valor > 0 THEN valor END) as temp_min,

MAX(CASE WHEN tipo='humedad' AND valor > 0 THEN valor END) as hum_max,
MIN(CASE WHEN tipo='humedad' AND valor > 0 THEN valor END) as hum_min

FROM datos

WHERE fecha >= DATE_SUB(NOW(), INTERVAL 7 DAY)

GROUP BY DATE(fecha)

ORDER BY dia ASC
";

$stmtSemana = $conexion->prepare($sqlSemana);
$stmtSemana->execute();

$labelsSemana = [];
$tempMaxSemana = [];
$tempMinSemana = [];
$humMaxSemana = [];
$humMinSemana = [];

while($fila = $stmtSemana->fetch(PDO::FETCH_ASSOC)){

$labelsSemana[] = $fila['dia'];
$tempMaxSemana[] = $fila['temp_max'];
$tempMinSemana[] = $fila['temp_min'];
$humMaxSemana[] = $fila['hum_max'];
$humMinSemana[] = $fila['hum_min'];
}
$fecha_limite = date("Y-m-d H:i:s", strtotime("-$periodo hours"));

$queryTiempo = "SELECT ultima_actualizacion FROM tiempo WHERE id = 1";
$stmt = $conexion->prepare($queryTiempo);
$stmt->execute();
$filaTiempo = $stmt->fetch(PDO::FETCH_ASSOC);

// Validar si existe registro
if ($filaTiempo && $filaTiempo['ultima_actualizacion']) {
    $ultima_fecha = $filaTiempo['ultima_actualizacion'];
} else {
    $ultima_fecha = null;
}
if ($ultima_fecha === null) {
    $estado = "offline";
} else {
    $ahora = date("Y-m-d H:i:s");
    $diferencia = strtotime($ahora) - strtotime($ultima_fecha);
    $estado = ($diferencia > 30) ? "offline" : "online";
}
// Definir $ahora
$ahora = date("Y-m-d H:i:s");

// Calcular diferencia
$diferencia = strtotime($ahora) - strtotime($ultima_fecha);
$estado = ($diferencia > 30) ? "offline" : "online";
$diferencia = strtotime($ahora) - strtotime($ultima_fecha);
$estado = ($diferencia > 30) ? "offline" : "online";

$sql_temp = "SELECT fecha, valor 
             FROM datos 
             WHERE tipo = 'temperatura'
             AND fecha >= :fecha_limite
             ORDER BY fecha ASC";

$stmtTemp = $conexion->prepare($sql_temp);
$stmtTemp->bindParam(':fecha_limite', $fecha_limite);
$stmtTemp->execute();
$temp_data = $stmtTemp->fetchAll(PDO::FETCH_ASSOC);

$sql_hum = "SELECT fecha, valor 
            FROM datos 
            WHERE tipo = 'humedad'
            AND fecha >= :fecha_limite
            ORDER BY fecha ASC";

$stmtHum = $conexion->prepare($sql_hum);
$stmtHum->bindParam(':fecha_limite', $fecha_limite);
$stmtHum->execute();
$hum_data = $stmtHum->fetchAll(PDO::FETCH_ASSOC);

$sqlUltTemp = "SELECT valor FROM datos 
               WHERE tipo='temperatura'
               ORDER BY fecha DESC
               LIMIT 1";

$stmtUltTemp = $conexion->prepare($sqlUltTemp);
$stmtUltTemp->execute();
$temp_actual = $stmtUltTemp->fetchColumn() ?? 0;

$sqlUltHum = "SELECT valor FROM datos 
              WHERE tipo='humedad'
              ORDER BY fecha DESC
              LIMIT 1";

$stmtUltHum = $conexion->prepare($sqlUltHum);
$stmtUltHum->execute();
$hum_actual = $stmtUltHum->fetchColumn()?? 0;

$sqlPromTemp = "SELECT AVG(valor) FROM datos WHERE tipo='temperatura'";
$stmtPromTemp = $conexion->prepare($sqlPromTemp);
$stmtPromTemp->execute();
$prom_temp = round($stmtPromTemp->fetchColumn(),1);

$sqlPromHum = "SELECT AVG(valor) FROM datos WHERE tipo='humedad'";
$stmtPromHum = $conexion->prepare($sqlPromHum);
$stmtPromHum->execute();
$prom_hum = round($stmtPromHum->fetchColumn(),1);


?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>IoT</title>
<link rel="icon" href="img/logos/logopre.png" type="image/png">
<link rel="stylesheet" href="css/dashboard.css">
<meta name="viewport" content="width=1450">
</head>
<div class="layout">
    <div class="sidebar"></div>
    <div class="contenido"></div>
<body>
<?php include("backend/sidebar.php"); ?>
<div class="perfil">
        <span><?php echo ($rol == 'admin') ? 'Administrador' : 'Invitado'; ?></span>
    </p>
</div>
<main class="contenido">
<h1>Monitoreo de Sensores</h1>
<h3>Temperatura y Humedad en Tiempo Real</h3>
<div class="barra-estado">

<div class="estado-izq">
<span id="circuloEstado" class="circulo <?php echo $estado; ?>"></span>

<span id="textoEstado" class="texto-estado">
<?php echo ($estado == "online") ? "Sistema de Monitoreo Activo" : "Sistema de Monitoreo Inactivo"; ?>
</span>
</div>

<div class="estado-der">
Última actualización:
<span id="horaActualizacion">
<?php echo date("H:i:s", strtotime($ultima_fecha)); ?>
</span>
</div>

</div>

<form method="GET" class="filtros">

<div>
<label><strong>Tipo de Sensor</strong></label>
<select name="sensor">
<option value="todos"><strong>Todos los sensores</strong></option>
<option value="temperatura"><strong>Temperatura</strong></option>
<option value="humedad"><strong>Humedad</strong></option>
</select>
</div>

<div>
<label><strong>Período de Tiempo</strong></label>
<select name="periodo">
<option value="1">Última hora</option>
<option value="6">Últimas 6 horas</option>
<option value="24">Últimas 24 horas</option>
</select>
</div>

<div>
<label><strong>Acción</strong></label>
<button type="submit">Aplicar Filtro</button>
</div>

</form>

<div class="cards-container">

<div class="card temperatura">

<div class="card-header">
<span>🌡 Temperatura</span>
<span class="estado normal">Normal</span>
</div>

<h1 class="valor"><?php echo $temp_actual; ?>°C</h1>

<hr>

<div class="card-stats">

<div>
<span class="label">Actual</span>
<p><?php echo $temp_actual; ?>°C</p>
</div>

<div>
<span class="label">Promedio</span>
<p><?php echo $prom_temp; ?>°C</p>
</div>

</div>

</div>

<div class="card humedad">

<div class="card-header">
<span>💧 Humedad</span>
<span class="estado optimo">Óptimo</span>
</div>

<h1 class="valor"><?php echo $hum_actual; ?>%</h1>

<hr>

<div class="card-stats">

<div>
<span class="label">Actual</span>
<p><?php echo $hum_actual; ?>%</p>
</div>

<div>
<span class="label">Promedio</span>
<p><?php echo $prom_hum; ?>%</p>
</div>

</div>

</div>

</div>

<script>
let datosTemp = <?php echo json_encode($temp_data); ?>;
let datosHum = <?php echo json_encode($hum_data); ?>;
</script>



<div class="contenedor-graficos">

<?php if($sensor == 'todos' || $sensor == 'temperatura'): ?>
<div class="grafico-card">
<canvas id="graficoTemp"></canvas>
</div>
<?php endif; ?>

<?php if($sensor == 'todos' || $sensor == 'humedad'): ?>
<div class="grafico-card">
<canvas id="graficoHum"></canvas>
</div>
<?php endif; ?>

    <div class="grafico-card">
        <canvas id="graficoSemanal"></canvas>
    </div>

    <div class="grafico-card">
        <canvas id="graficaLago"></canvas>
    </div>

</div>

</main>
<script src="js/CHART.js"></script>
<script src="chart.umd.min.js"></script>
<script src="js/sidebar.js"></script>
<script>
let intervaloID = null;
let ultimaAlertaID = localStorage.getItem("ultimaAlertaID") || 0;
// 🔥 ALERTA VISUAL
function mostrarAlerta(msg){
    let div = document.createElement("div");
    div.className = "toast-alerta";
    div.innerText = msg;

    document.body.appendChild(div);

    setTimeout(() => {
        div.remove();
    }, 3000);
}



// 🔥 VERIFICAR ALERTAS
function verificarAlerta(){

    fetch("backend/verificar_alertas.php")
    .then(res => res.json())
    .then(data => {

        console.log("ALERTA:", data);

        if (!data || !data.id) return;

        if (data.id != ultimaAlertaID) {
            mostrarAlerta(data.mensaje);

            ultimaAlertaID = data.id;
            localStorage.setItem("ultimaAlertaID", data.id);
        }

    })
    .catch(error => console.error("Error alerta:", error));

}
// 🔥 CARGAR DATOS (SENSORES + ALERTAS)
function cargarDatos(){

    // datos sensores
    fetch("backend/obtener_datos.php")
    .then(res => res.json())
    .then(data => {

        console.log("DATOS:", data);

        // ⚠️ asegúrate que estos IDs existan en tu HTML
        let temp = document.getElementById("temp");
        let hum = document.getElementById("hum");

        if(temp) temp.innerText = data.temperatura + "°C";
        if(hum) hum.innerText = data.humedad + "%";

    });

}
</script>
<script id="m2n4jc">
// 🔥 INICIAR SISTEMA CON INTERVALO DINÁMICO
function iniciarSistema(){

    fetch("backend/obtener_configuracion.php")
    .then(res => res.json())
    .then(config => {

        let tiempo = (config.intervalo || 1) * 1000;

        console.log("Intervalo activo:", tiempo);

        if(intervaloID) clearInterval(intervaloID);

        cargarDatos(); // ejecutar una vez
	verificarAlerta();
        intervaloID = setInterval(() => {
            cargarDatos();
        }, tiempo);

    });

}

// 🚀 INICIAR TODO
document.addEventListener("DOMContentLoaded", () => {
    iniciarSistema();
});
function iniciarRecarga(){

    fetch("backend/obtener_configuracion.php")
    .then(res => res.json())
    .then(config => {

        let tiempo = (config.intervalo || 5) * 1000;

        console.log("Recargando cada:", tiempo);

        setInterval(() => {
            location.reload();
        }, tiempo);

    });

}

document.addEventListener("DOMContentLoaded", () => {
    iniciarRecarga();
});
</script>
</body>
</div>
</html>


