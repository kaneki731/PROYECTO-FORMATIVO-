
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login IoT</title>
    <link rel="icon" href="img/logos/logopre.png" type="image/png">
    <link rel="stylesheet" href="css/index.css">
</head>
<body>

    <div class="contenedor">
        <div class="login-box">
            <div class="icono">
                <img src="img/logos/logopre.png" alt="Logo IoT" style="width: 160px; height: auto;">
               <i id="mainIcon" class="fa fa-lock"></i>
            </div>

            <h2>Monitoreo IoT</h2>
            <p class="subtitulo">Ingrese sus credenciales para continuar</p>
           
           <!-- SLIDER -->
<div class="tabs">
    <button type="button" class="tab active" onclick="showLogin()">Iniciar Sesión</button>
    <button type="button" class="tab" onclick="showRegister()">Registrarse</button>
    <div class="slider"></div>
</div>

<!-- LOGIN -->
<form id="loginForm" action="backend/login.php" method="POST">

    <div class="input-group">
        <i class="fa fa-user"></i>
        <label>Usuario</label>
        <img src="img/logos/lapiz-de-usuario.png" alt="" class="icono-input">
        <input type="text" name="usuario" placeholder="Ingrese su usuario" required>
        
    </div>

    <div class="input-group">
        <i class="fa fa-lock"></i>
        <label>Contraseña</label>
        <img src="img/logos/llave.png" alt="" class="icono-input">
        <img src="img/logos/ojo.png" alt="" class="toggle-password">
        <input type="password" name="password" class="password-input" placeholder="Ingrese su contraseña" required>
    </div>

    <button type="submit" class="btn-login">
        Iniciar Sesión
    </button>
    <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
    <div class="error-msg">
        Usuario o contraseña incorrectos
    </div>
<?php endif; ?>
 <?php if (isset($_GET['registro']) && $_GET['registro'] == 'ok'): ?>
    <div class="success-msg">
        Usuario registrado correctamente
    </div>
<?php endif; ?>

<?php if (isset($_GET['registro']) && $_GET['registro'] == 'error'): ?>
    <div class="error-msg">
        Error al registrar usuario
    </div>
<?php endif; ?>
</form>

<!-- REGISTRO -->
<form id="registerForm" action="backend/registro.php" method="POST" class="hidden">

    <div class="input-group">
        <i class="fa fa-user-plus"></i>
        <label>Nombre Completo</label>
        <img src="img/logos/agregar-usuarios.png" alt="" class="icono-input">
        <input type="text" name="nombre" placeholder="Ingrese su nombre completo" required>
    </div>

    <div class="input-group">
        <i class="fa fa-user"></i>
        <label>Usuario</label>
        <img src="img/logos/lapiz-de-usuario.png" alt="" class="icono-input">
        <input type="text" name="usuario" placeholder="Ingrese su usuario" required>
    </div>

    <div class="input-group">
        <i class="fas fa-lock"></i>
        <label>Contraseña</label>
        <img src="img/logos/llave.png" alt="" class="icono-input">
        <img src="img/logos/ojo.png" alt="" class="toggle-password">
        <input type="password" name="password" class="password-input" placeholder="Contraseña" required>
        </span>
    </div>

    <button type="submit" class="btn-login">
        Crear Cuenta
    </button>
   

</form>
<footer class="footer">
  <p class="footer-text"><br>
    © 2026 SENA Tecnólogo en Gestión de Redes de Datos
  </p>
</footer>

        </div>

    </div>

    <script src="js/main.js"></script>

</body>
</html>
