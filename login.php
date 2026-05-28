<?php
require_once 'includes/bootstrap.php';

// Evitar que el navegador guarde la página en caché (Seguridad al cerrar sesión)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Acción de Logout: Si viene ?action=logout, cerramos sesión
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $authCtrl->logout();
    header("Location: login.php");
    exit();
}

$error = ""; // Variable para mostrar errores
$timeout = isset($_GET['timeout']) && $_GET['timeout'] == '1';

if (isset($_POST['login'])) {
    $resultado = $authCtrl->login($_POST['usuario'], $_POST['password']);

    if ($resultado === true) {
        header("Location: inicio.php");
        exit();
    } elseif ($resultado === "inactivo") {
        $error = "Su cuenta se encuentra inhabilitada. Contacte al administrador.";
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso | SARCE</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilos_globales.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="assets/js/scripts_globales.js" defer></script>
</head>
<body class="body-login">

<div class="login-card">
    <img src="<?php echo IMG_URL; ?>logooficial.png" alt="Logo SARCE" class="login-logo">
    <p class="subtitle">Ambulatorio Rural II de Jají</p>

    <?php if($error): ?>
        <div class="error-msg"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
    <?php endif; ?>

    <?php if($timeout): ?>
        <div class="msg msg-error" style="margin-bottom: 15px;">
            <i class="fas fa-clock"></i> Cierre por inactividad
        </div>
    <?php endif; ?>

    <form action="" method="POST" autocomplete="off">
        <div class="input-group">
            <input type="text" name="usuario" placeholder="Usuario" autocomplete="username" required>
        </div>
        
        <div class="input-group">
            <div class="password-wrapper">
                <input type="password" name="password" id="password" placeholder="Contraseña" autocomplete="new-password" value="" required>
                <i class="fas fa-eye toggle-password" onclick="togglePassword('password', this)"></i>
            </div>
        </div>

        <button type="submit" name="login" class="btn-login"><i class="fas fa-sign-in-alt"></i> INICIAR SESIÓN</button>
    </form>

    <div class="links">
        <a href="<?php echo MOD_SISTEMA; ?>recuperar.php">¿Olvidó su contraseña?</a>
    </div>
</div>

</body>
</html>