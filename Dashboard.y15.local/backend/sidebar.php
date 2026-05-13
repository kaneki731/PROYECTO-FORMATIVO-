<!-- SIDEBAR -->

<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar" id="sidebar">

<h2 class="logo">
  <img src="img/logos/pastilla.png" class="logo-icon">
  <span class="logo-text">Monitoreo IoT</span>
</h2>

<a href="dashboard.php" class="menu-item <?php if($current_page == 'dashboard.php') echo 'active'; ?>">
  <img src="img/logos/estadisticas.png" class="icono">
  <span class="texto"><strong>Gráficos</strong></span>
</a>

<br>
<a href="base_datos.php" class="menu-item <?php if($current_page == 'base_datos.php') echo 'active'; ?>">
  <img src="img/logos/basedt.png" class="icono">
  <span class="texto"><strong>Base de Datos</strong></span>
</a>

<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if($_SESSION["rol"] == "admin"){ ?>


<br>

<a href="configuracion.php" class="menu-item <?php if($current_page == 'configuracion.php') echo 'active'; ?>">
  <img src="img/logos/ajustes.png" class="icono">
  <span class="texto"><strong>Configuración</strong></span>
</a>

<?php } ?>

<a href="backend/logout.php" class="menu-item logout">
  <img src="img/logos/salida.png" class="icono">
  <span class="texto"><strong>Cerrar Sesión</strong></span>
</a>

</aside>
