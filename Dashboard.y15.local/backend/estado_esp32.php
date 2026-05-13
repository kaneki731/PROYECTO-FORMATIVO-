<?php
header('Content-Type: application/json');
error_reporting(0);

include("conexion.php");

try {

    // 🔹 obtener último dato registrado
    $stmt = $conexion->prepare("SELECT fecha FROM datos ORDER BY id DESC LIMIT 1");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $activo = false;
    $hora = null;

    if($row && !empty($row['fecha'])){

        $ultima = strtotime($row['fecha']);
        $ahora = time();

        // 🔥 si han pasado menos de 30 segundos → activo
        if(($ahora - $ultima) < 30){
            $activo = true;
        }

        $hora = date("H:i:s", $ultima);
    }

    echo json_encode([
        "activo" => $activo,
        "hora" => $hora
    ]);

} catch(Exception $e){

    // 🔥 NUNCA romper JSON
    echo json_encode([
        "activo" => false,
        "hora" => null,
        "error" => "error interno"
    ]);
}
?>