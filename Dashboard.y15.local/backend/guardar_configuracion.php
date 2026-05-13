<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "admin", "IOT*2026", "login");

if ($conn->connect_error) {
    echo json_encode(["status"=>"error", "msg"=>"conexion"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["status"=>"error", "msg"=>"json"]);
    exit();
}

$temp_min = $data['temp_min'];
$temp_max = $data['temp_max'];
$hum_min = $data['hum_min'];
$hum_max = $data['hum_max'];

$notif = $data['notif_pantalla'];
$alerta_temp = $data['alerta_temp'];
$alerta_hum = $data['alerta_hum'];
$resumen = $data['resumen'];

$intervalo = $data['intervalo'];
$retencion = $data['retencion'];

// 🔥 QUERY CORRECTA
$sql = "UPDATE configuracion SET
    temp_min=?,
    temp_max=?,
    hum_min=?,
    hum_max=?,
    notif_pantalla=?,
    alerta_temp=?,
    alerta_hum=?,
    resumen=?,
    intervalo=?,
    retencion=?
    WHERE id=1";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "iiiiiiiiii",
    $temp_min,
    $temp_max,
    $hum_min,
    $hum_max,
    $notif,
    $alerta_temp,
    $alerta_hum,
    $resumen,
    $intervalo,
    $retencion
);

if ($stmt->execute()) {
    echo json_encode(["status"=>"ok"]);
} else {
    echo json_encode(["status"=>"error", "msg"=>$stmt->error]);
}

$stmt->close();
$conn->close();
?>
