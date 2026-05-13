<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "admin", "IOT*2026", "login");

if ($conn->connect_error) {
    echo json_encode(["error" => "conexion"]);
    exit();
}

$sql = "SELECT * FROM configuracion WHERE id=1";
$result = $conn->query($sql);

if ($result && $row = $result->fetch_assoc()) {

    echo json_encode([
        "temp_min" => $row['temp_min'],
        "temp_max" => $row['temp_max'],
        "hum_min" => $row['hum_min'],
        "hum_max" => $row['hum_max'],

        // 🔹 AGREGAR ESTO
        "intervalo" => $row['intervalo'],
        "retencion" => $row['retencion'],

        "notif_pantalla" => $row['notif_pantalla'],
        "alerta_temp" => $row['alerta_temp'],
        "alerta_hum" => $row['alerta_hum'],
        "resumen" => $row['resumen']
    ]);

} else {
    echo json_encode([
        "temp_min" => 18,
        "temp_max" => 28,
        "hum_min" => 40,
        "hum_max" => 70,

        // 🔹 VALORES POR DEFECTO
        "intervalo" => 1,
        "retencion" => 7,

        "notif_pantalla" => 1,
        "alerta_temp" => 1,
        "alerta_hum" => 1,
        "resumen" => 0
    ]);
}

$conn->close();
?>
