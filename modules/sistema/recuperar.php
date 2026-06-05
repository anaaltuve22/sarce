<?php
require_once '../../includes/bootstrap.php';

$mensaje = "";
$tipo_error = ""; // 'error' o 'exito'
$paso = 1; // 1: Identificar, 2: Pregunta, 3: Nueva Clave
$user_id = "";
$p1_db = ""; $p2_db = ""; $p3_db = "";

$usuarioCtrl = new UsuarioController($conexion);

// PASO 1: Buscar usuario
if (isset($_POST['buscar_usuario'])) {
    $usuario = $_POST['identificador'];
    $resultado = $usuarioCtrl->iniciarRecuperacion($usuario);

    if ($resultado['status'] === 'success') {
        // Verificar si el usuario realmente tiene preguntas configuradas en la base de datos
        if (empty($resultado['p1']) || empty($resultado['p2']) || empty($resultado['p3'])) {
            $mensaje = "Esta cuenta no tiene preguntas de seguridad configuradas. Por favor, contacte al administrador para un restablecimiento manual.";
            $tipo_error = "error";
        } else {
            $paso = 2;
            $user_id = $resultado['user_id'];
            $p1_db = $resultado['p1'];
            $p2_db = $resultado['p2'];
            $p3_db = $resultado['p3'];
        }
    } else {
        $mensaje = $resultado['msg'];
        $tipo_error = "error";
    }
}

// PASO 2: Verificar respuesta
if (isset($_POST['verificar_respuesta'])) {
    $user_id = $_POST['user_id'];
    $r1_u = trim($_POST['r1']);
    $r2_u = trim($_POST['r2']);
    $r3_u = trim($_POST['r3']);
    
    $resultado = $usuarioCtrl->verificarRespuestas($user_id, $r1_u, $r2_u, $r3_u);

    if ($resultado['status'] === 'success') {
        $paso = 3;
    } else {
        $mensaje = $resultado['msg'];
        $tipo_error = "error";
        $paso = 2; // Volver al paso 2 si las respuestas son incorrectas

        // Recargar preguntas para la vista
        $preguntas = $usuarioCtrl->obtenerPreguntasPorId($user_id);
        if ($preguntas) {
            $p1_db = $preguntas['p1'];
            $p2_db = $preguntas['p2'];
            $p3_db = $preguntas['p3'];
        } else {
            $mensaje = "Error crítico al recuperar las preguntas de seguridad.";
            $tipo_error = "error";
            $paso = 1;
        }
    }
}

// PASO 3: Actualizar contraseña
if (isset($_POST['cambiar_clave'])) {
    $user_id = $_POST['user_id'];
    $nueva_clave = $_POST['nueva_clave'];
    $confirmar_clave = $_POST['confirmar_clave'];

    $resultado = $usuarioCtrl->restablecerClave($user_id, $nueva_clave, $confirmar_clave);
    
    if ($resultado['status'] === 'success') {
        $mensaje = $resultado['msg'];
        $tipo_error = "exito";
        $paso = 4; // Finalizado
    } else {
        $mensaje = $resultado['msg'];
        $tipo_error = "error";
        $paso = 3; // Volver al paso 3 si hay error
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña | SARCE</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/estilos_globales.css">
    <script src="../../assets/js/scripts_globales.js" defer></script>
</head>
<body class="body-recuperar">

<div class="recovery-card">
    <i class="fas fa-key main-icon"></i>
    <h2>¿Problemas para entrar?</h2>
    <p class="subtitle">Recuperación por preguntas de seguridad</p>

    <?php if($mensaje): ?>
        <div class="msg <?php echo ($tipo_error == 'exito') ? 'msg-exito' : 'msg-error'; ?>">
            <i class="fas <?php echo ($tipo_error == 'exito') ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i> <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <?php if($paso == 1): ?>
        <form method="POST" autocomplete="off">
            <div class="input-group">
                <label><i class="fas fa-user"></i> Usuario</label>
                <input type="text" name="identificador" placeholder="Ingrese su usuario" maxlength="20" autocomplete="off" required>
            </div>
            <button type="submit" name="buscar_usuario" class="btn-recuperar">Siguiente</button>
        </form>
    <?php elseif($paso == 2): ?>
        <form method="POST">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            
            <div class="input-group">
                <label><i class="fas fa-shield-alt"></i> Pregunta de Seguridad 1:</label>
                <div class="question-display"><?php echo htmlspecialchars($p1_db); ?></div>
                <input type="text" name="r1" placeholder="Respuesta 1" maxlength="30" onkeypress="return soloLetras(event)" required autofocus>
            </div>

            <div class="input-group">
                <label><i class="fas fa-shield-alt"></i> Pregunta de Seguridad 2:</label>
                <div class="question-display"><?php echo htmlspecialchars($p2_db); ?></div>
                <input type="text" name="r2" placeholder="Respuesta 2" maxlength="30" onkeypress="return soloLetras(event)" required>
            </div>

            <div class="input-group">
                <label><i class="fas fa-shield-alt"></i> Pregunta de Seguridad 3:</label>
                <div class="question-display"><?php echo htmlspecialchars($p3_db); ?></div>
                <input type="text" name="r3" placeholder="Respuesta 3" maxlength="30" onkeypress="return soloLetras(event)" required>
            </div>

            <button type="submit" name="verificar_respuesta" class="btn-recuperar">Verificar Todas</button>

            <p style="margin-top: 20px; font-size: 0.8rem; color: #718096; border-top: 1px dashed #edf2f7; padding-top: 15px;">
                <i class="fas fa-info-circle"></i> <b>¿Olvidó sus respuestas?</b><br>
                Por motivos de seguridad, si no recuerda sus respuestas, debe contactar al <b>Administrador del Sistema</b> para un restablecimiento manual.
            </p>
        </form>
    <?php elseif($paso == 3): ?>
        <form method="POST" autocomplete="off">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            <div class="input-group">
                <label><i class="fas fa-lock"></i> Nueva Contraseña</label>
                <div class="password-wrapper">
                    <input type="password" name="nueva_clave" id="nueva_clave" placeholder="Entre 8 y 12 caracteres" maxlength="12" autocomplete="new-password" required autofocus>
                    <i class="fas fa-eye toggle-password" onclick="togglePassword('nueva_clave', this)"></i>
                </div>
            </div>
            <div class="input-group">
                <label><i class="fas fa-check-double"></i> Confirmar Contraseña</label>
                <div class="password-wrapper">
                    <input type="password" name="confirmar_clave" id="confirmar_clave" placeholder="Repita la contraseña" maxlength="12" autocomplete="new-password" required>
                    <i class="fas fa-eye toggle-password" onclick="togglePassword('confirmar_clave', this)"></i>
                </div>
            </div>
            <button type="submit" name="cambiar_clave" class="btn-recuperar">Actualizar Clave</button>
        </form>
    <?php endif; ?>

    <?php if($paso == 4 || $tipo_error == 'exito'): ?>
        <a href="<?php echo BASE_URL; ?>login.php" class="btn-recuperar" style="text-decoration: none;">
            <i class="fas fa-sign-in-alt"></i> IR AL LOGIN AHORA
        </a>
    <?php endif; ?>

    <div class="links">
        <a href="<?php echo BASE_URL; ?>login.php"><i class="fas fa-arrow-left"></i> Volver al Inicio de Sesión</a>
    </div>
</div>

</body>
</html>