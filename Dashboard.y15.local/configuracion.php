<?php
include("backend/conexion.php");
include("backend/auth.php");


// Asegúrate de que la sesión esté activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirigir si el usuario no es admin
if ($_SESSION["rol"] != "admin") {
    header("Location: dashboard.php");
    exit();
}

// Obtener los valores guardados en la base de datos
$sql = "SELECT * FROM configuracion LIMIT 1";
$result = $conexion->query($sql);
$config = $result->fetch(PDO::FETCH_ASSOC);

// Valores predeterminados si no existen en la base de datos
$temp_min = $config['temp_min'] ?? 18;
$temp_max = $config['temp_max'] ?? 28;
$hum_min = $config['hum_min'] ?? 40;
$hum_max = $config['hum_max'] ?? 70;
$intervalo = $config['intervalo'] ?? 1;
$retencion = $config['retencion'] ?? 7;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración</title>
    <link rel="icon" href="img/logos/logopre.png" type="image/png">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/configuracion.css">
    <link rel="stylesheet" href="css/layout.css">
    <meta name="viewport" content="width=1450">
</head>
<body>

<?php include("backend/sidebar.php"); ?>

<main class="contenido">

<h1>Configuración del Sistema</h1>
<h2 style="color: #535963; font-size: 19px;">Ajusta los parámetros de monitoreo y alertas</h2>

<div class="config-layout">

<!-- COLUMNA IZQUIERDA -->
<div class="config-principal">

<!-- CONFIGURACIÓN TEMPERATURA -->
<div class="config-card">
<h2><img src="img/logos/temperatura-alta.png" class="icono-config">Configuración de Temperatura</h2>
<p>Define los rangos óptimos de temperatura</p>

<div class="inputs-grid">
<div class="input-box">
<label for="temp_min">Temperatura Mínima</label>
<div class="input-unidad">
<input type="number" id="temp_min" value="<?php echo $temp_min; ?>">
<span>°C</span>
</div>
</div>

<div class="input-box">
<label for="temp_max">Temperatura Máxima</label>
<div class="input-unidad">
<input type="number" id="temp_max" value="<?php echo $temp_max; ?>">
<span>°C</span>
</div>
</div>
</div>

<div class="alerta-info">
Rango configurado: <span id="rangoTemp"><?php echo $temp_min; ?>°C - <?php echo $temp_max; ?>°C</span>
</div>
</div>

<!-- CONFIGURACIÓN HUMEDAD -->
<div class="config-card">
<h2><img src="img/logos/gotas-de-lluvia.png" class="icono-config">Configuración de Humedad</h2>
<p>Define los rangos óptimos de humedad relativa</p>

<div class="inputs-grid">
<div class="input-box">
<label for="hum_min">Humedad Mínima</label>
<div class="input-unidad">
<input type="number" id="hum_min" value="<?php echo $hum_min; ?>">
<span>%</span>
</div>
</div>

<div class="input-box">
<label for="hum_max">Humedad Máxima</label>
<div class="input-unidad">
<input type="number" id="hum_max" value="<?php echo $hum_max; ?>">
<span>%</span>
</div>
</div>
</div>

<div class="alerta-info azul">
Rango configurado: <span id="rangoHum"><?php echo $hum_min; ?>% - <?php echo $hum_max; ?>%</span>
</div>
</div>

<!-- CONFIGURACIÓN ALERTAS -->
<div class="config-card">
<h2><img src="img/logos/campana.png" class="icono-config">Alertas y Notificaciones</h2>
<p>Configura cómo recibir las alertas del sistema</p>

<div class="notificacion-item">
<div>
<strong>Notificaciones en Pantalla</strong>
<p>Mostrar alertas en el navegador</p>
</div>
<input type="checkbox" name="notif_pantalla" checked>
</div>

<div class="notificacion-item">
<div>
<strong>Alertas de Temperatura</strong>
<p>Avisar cuando se exceda el rango</p>
</div>
<input type="checkbox" name="alerta_temp" checked>
</div>

<div class="notificacion-item">
<div>
<strong>Alertas de Humedad</strong>
<p>Avisar cuando se exceda el rango</p>
</div>
<input type="checkbox" name="alerta_hum" checked>
</div>

<div class="notificacion-item">
<div>
<strong>Resumen Diario</strong>
<p>Recibir reporte al final del día</p>
</div>
<input type="checkbox" name="resumen">
</div>

</div>
</div>

<!-- COLUMNA DERECHA -->
<div class="config-lateral">
<!-- ACCIONES RÁPIDAS -->
<div class="config-card">
<h2>Acciones Rápidas</h2>
<form action="backend/acciones.php" method="POST">
    <button type="submit" name="accion" value="reiniciar">
        Reiniciar Sensores
    </button>
</form>

<br>

<form action="backend/acciones.php" method="POST">
    <button type="submit" name="accion" value="limpiar">
        Limpiar Base de Datos
    </button>
</form>

<br>

<form action="backend/acciones.php" method="POST">
    <button type="submit" name="accion" value="exportar">
        Exportar Configuración
    </button>
</form>

</div>
</div>
<!-- CONFIGURACIÓN SISTEMA -->
<div class="config-card recoleccion-datos">
<h2><img src="img/logos/gestion-de-bases-de-datos.png" class="icono-config">Recolección de Datos</h2>
<p>Configura la frecuencia y estado del sistema</p>

<div class="input-box">
<label>Intervalo de Lectura (segundos)</label>
<select id="intervalo">
    <option value="1" <?php if ($intervalo == 1) echo "selected"; ?>>Cada 1 segundo</option>
    <option value="10" <?php if ($intervalo == 10) echo "selected"; ?>>Cada 10 segundos</option>
    <option value="60" <?php if ($intervalo == 60) echo "selected"; ?>>Cada 60 segundos</option>
</select>
</div>

<div class="input-box">
<label>Retención de datos (días)</label>
<select id="retencion">
    <option value="7" <?php if ($retencion == 7) echo "selected"; ?>>7 días</option>
    <option value="30" <?php if ($retencion == 30) echo "selected"; ?>>30 días</option>
    <option value="90" <?php if ($retencion == 90) echo "selected"; ?>>90 días</option>
</select>
</div>

</div>
</div>
<div class="guardar-config">
<button class="btn-guardar" id="guardarConfig">
<img src="img/logos/disco.png" class="icono-guardar">
Guardar Configuración
</button>
</div>
</main>

<script src="js/sidebar.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function(){

    const tempMinInput = document.getElementById("temp_min");
    const tempMaxInput = document.getElementById("temp_max");
    const humMinInput = document.getElementById("hum_min");
    const humMaxInput = document.getElementById("hum_max");
    const intervaloSelect = document.getElementById("intervalo");
    const retencion = document.getElementById("retencion");

    const rangoTemp = document.getElementById("rangoTemp");
    const rangoHum = document.getElementById("rangoHum");

    const notifPantalla = document.querySelector('input[name="notif_pantalla"]');
    const alertaTemp = document.querySelector('input[name="alerta_temp"]');
    const alertaHum = document.querySelector('input[name="alerta_hum"]');
    const resumen = document.querySelector('input[name="resumen"]');

    const btnGuardar = document.getElementById("guardarConfig");

    function actualizarRangos(){
        rangoTemp.textContent = `${tempMinInput.value}°C - ${tempMaxInput.value}°C`;
        rangoHum.textContent = `${humMinInput.value}% - ${humMaxInput.value}%`;
    }

    [tempMinInput, tempMaxInput].forEach(i => i.addEventListener("input", actualizarRangos));
    [humMinInput, humMaxInput].forEach(i => i.addEventListener("input", actualizarRangos));

    btnGuardar.addEventListener("click", function(){
        console.log("ENVIANDO DATOS 🔥");
        console.log("CLICK OK 🔥");

        let datos = {
            temp_min: tempMinInput.value,
            temp_max: tempMaxInput.value,
            hum_min: humMinInput.value,
            hum_max: humMaxInput.value,
            notif_pantalla: notifPantalla.checked ? 1 : 0,
            alerta_temp: alertaTemp.checked ? 1 : 0,
            alerta_hum: alertaHum.checked ? 1 : 0,
            resumen: resumen.checked ? 1 : 0,
            intervalo: intervaloSelect.value,
            retencion: retencion.value,
        };
        console.log(datos);
        
        fetch("backend/guardar_configuracion.php", {
            method: "POST",
            headers: {"Content-Type":"application/json"},
            body: JSON.stringify(datos)
        })
        .then(res => res.text())
        .then(text => {

            let data;

            try {
                data = JSON.parse(text);
            } catch(e){
                console.error("❌ Respuesta inválida:", text);
                return;
            }

            if(data && data.status === "ok"){
                alert("✔ Guardado correctamente");
            } else {
                alert("❌ Error al guardar");
            }

        })
        .catch(error => console.error("Error:", error));

    });
    let intervaloID;

function iniciarSistema(){

    fetch("backend/obtener_configuracion.php")
    .then(res => res.json())
    .then(config => {

        console.log("CONFIG:", config);

        let tiempo = (config.intervalo || 1) * 1000; // segundos → ms

        // 🔥 limpiar intervalo anterior
        if(intervaloID) clearInterval(intervaloID);

        // 🔥 iniciar con nuevo valor
       

});

}

// 🔥 iniciar al cargar
iniciarSistema();

setInterval(() => {
    iniciarSistema();
}, 10000); // cada 10s revisa si cambió config
});
</script>

</body>
</html>
