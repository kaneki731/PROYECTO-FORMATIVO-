<?php
include("conexion.php");

$nombre = $_POST['nombre'];
$usuario = $_POST['usuario'];
$password = $_POST['password'];

$sql = "INSERT INTO usuarios (nombre, usuario, password)
        VALUES (:nombre, :usuario, :password)";

$stmt = $conexion->prepare($sql);

$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':usuario', $usuario);
$stmt->bindParam(':password', $password);

if ($stmt->execute()) {
    header("Location: ../index.php?registro=ok");
    exit();
} else {
    header("Location: ../index.php?registro=error");
    exit();
}
?>