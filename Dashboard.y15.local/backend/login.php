<?php
session_start();
include("conexion.php");

$usuario = $_POST['usuario'];
$password = $_POST['password'];

$sql = "SELECT * FROM usuarios WHERE usuario = :usuario AND password = :password";
$stmt = $conexion->prepare($sql);
$stmt->bindParam(':usuario', $usuario);
$stmt->bindParam(':password', $password);
$stmt->execute();

$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

if ($resultado) {

    $_SESSION['usuario'] = $resultado['usuario'];
    $_SESSION['rol'] = $resultado['rol']; // GUARDAMOS EL ROL

    header("Location: ../dashboard.php");
    exit();

} else {

    header("Location: ../index.php?error=1");
    exit();

}
?>