<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
session_start();

// 🔒 Solo admin
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] != "admin") {
    die("Acceso no autorizado");
}

// conexión
$conn = new mysqli("localhost", "admin", "IOT*2026", "login");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// verificar acción
if (isset($_POST['accion'])) {

    $accion = $_POST['accion'];

    switch ($accion) {

        case 'reiniciar':
            $conn->query("UPDATE datos SET valor = 0");
            header("Location: ../configuracion.php?msg=reiniciado");
            exit();
        break;

        case 'limpiar':
            $conn->query("TRUNCATE TABLE datos");
            $conn->query("TRUNCATE TABLE tiempo");
            header("Location: ../configuracion.php?msg=limpiado");
            exit();
        break;

       case 'exportar':

    $sql = "SELECT * FROM configuracion WHERE id=1";
    $resultado = $conn->query($sql);

    $config = $resultado->fetch_assoc();

    // Forzar descarga
    header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="configuracion.txt"');

echo "Temperatura Min: " . $config['temp_min'] . "\n";
echo "Temperatura Max: " . $config['temp_max'] . "\n";
echo "Humedad Min: " . $config['hum_min'] . "\n";
echo "Humedad Max: " . $config['hum_max'] . "\n";
    exit();

break;
    }
}

$conn->close();
?>
