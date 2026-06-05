<?php
require_once '../../includes/auth.php';

// Seguridad: Solo administradores pueden registrar otros usuarios
if (strtolower($_SESSION['rol'] ?? '') !== 'admin') {
    header("Location: " . BASE_URL . "login.php");
    exit();
}

$pageTitle = "Registrar Personal | SARCE";
include '../../includes/layout_header.php';

$userCtrl = new UsuarioController($conexion);
$preguntas = $userCtrl->obtenerOpcionesPreguntas();

if (isset($_POST['registrar_personal'])) {
    $resultado = $userCtrl->registrar($_POST);

    echo "<script>
        Swal.fire({
            icon: '{$resultado['status']}',
            title: '" . ($resultado['status'] == 'success' ? '¡Éxito!' : 'Error') . "',
            text: '{$resultado['msg']}',
            confirmButtonColor: '#28a745'
        }).then(() => {
            " . ($resultado['status'] == 'success' ? "window.location='" . BASE_URL . "inicio.php';" : "window.history.back();") . "
        });
    </script>";
    exit();
}
?>
<div class="form-container">
    <h2 class="form-header"><i class="fas fa-user-plus"></i> Registro de Personal</h2>
    <p style="text-align: center; color: #888; font-size: 12px; margin-bottom: 20px;">Creación de nueva cuenta de acceso al sistema</p>
    
    <form method="POST" autocomplete="off">
        <div class="form-grid">
            <div class="input-box">
                <label><i class="fas fa-user"></i> Nombre(s):</label>
                <input type="text" name="nombre" maxlength="25" placeholder="Ej: María" onkeypress="return soloLetras(event)" required>
            </div>
            <div class="input-box">
                <label><i class="fas fa-user"></i> Apellido(s):</label>
                <input type="text" name="apellido" maxlength="25" placeholder="Ej: Pérez" onkeypress="return soloLetras(event)" required>
            </div>
        </div>

        <div class="input-box">
            <label><i class="fas fa-envelope"></i> Correo Institucional/Personal:</label>
            <input type="email" name="correo" placeholder="correo@gmail.com" autocomplete="off" required>
        </div>

        <div class="input-box">
            <label><i class="fas fa-user-tag"></i> Nombre de Usuario:</label>
            <input type="text" name="usuario" placeholder="Ej: licenciada_jaji" autocomplete="off" onkeypress="return soloLetrasSinNumeros(event)" required>
        </div>

        <div class="input-box">
            <label><i class="fas fa-lock"></i> Contraseña:</label>
            <div class="password-wrapper">
                <input type="password" name="clave" id="clave" placeholder="Cree una clave segura" autocomplete="new-password" required>
                <i class="fas fa-eye toggle-password" onclick="togglePassword('clave', this)"></i>
            </div>
        </div>

        <div class="input-box">
            <label><i class="fas fa-check-double"></i> Confirmar Contraseña:</label>
            <div class="password-wrapper">
                <input type="password" name="confirmar_clave" id="confirmar_clave" placeholder="Repita la contraseña" autocomplete="new-password" required>
                <i class="fas fa-eye toggle-password" onclick="togglePassword('confirmar_clave', this)"></i>
            </div>
        </div>

        <div class="input-box">
            <label><i class="fas fa-shield-alt"></i> Pregunta de Seguridad 1:</label>
            <select name="p1" required onchange="validarPreguntasUnicas()">
                <option value="" disabled selected>Seleccione una pregunta...</option>
                <?php foreach($preguntas as $p): ?>
                    <option value="<?php echo $p; ?>"><?php echo $p; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="r1" placeholder="Respuesta 1" maxlength="30" onkeypress="return soloLetras(event)" required style="margin-top:8px;">
        </div>

        <div class="input-box">
            <label><i class="fas fa-shield-alt"></i> Pregunta de Seguridad 2:</label>
            <select name="p2" required onchange="validarPreguntasUnicas()">
                <option value="" disabled selected>Seleccione una pregunta...</option>
                <?php foreach($preguntas as $p): ?>
                    <option value="<?php echo $p; ?>"><?php echo $p; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="r2" placeholder="Respuesta 2" maxlength="30" onkeypress="return soloLetras(event)" required style="margin-top:8px;">
        </div>

        <div class="input-box">
            <label><i class="fas fa-shield-alt"></i> Pregunta de Seguridad 3:</label>
            <select name="p3" required onchange="validarPreguntasUnicas()">
                <option value="" disabled selected>Seleccione una pregunta...</option>
                <?php foreach($preguntas as $p): ?>
                    <option value="<?php echo $p; ?>"><?php echo $p; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="r3" placeholder="Respuesta 3" maxlength="30" onkeypress="return soloLetras(event)" required style="margin-top:8px;">
        </div>

        <div class="input-box">
            <label><i class="fas fa-user-shield"></i> Rol de Usuario:</label>
            <select name="rol">
                <option value="secretaria">Secretaria (Acceso limitado)</option>
                <option value="admin">Administrador (Acceso total)</option>
            </select>
        </div>
        
        <div class="btn-container-sarce">
            <button type="submit" name="registrar_personal" class="btn-sarce" style="background-color: #28a745;">
                <i class="fas fa-user-plus"></i> GUARDAR
            </button>
        </div>
    </form>
</div>

<?php include '../../includes/layout_footer.php'; ?>