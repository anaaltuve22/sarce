<?php
require_once '../../includes/auth.php';

// Seguridad: Solo administradores pueden registrar otros usuarios
if ($_SESSION['rol'] !== 'admin') {
    header("Location: " . BASE_URL . "login.php");
    exit();
}

$pageTitle = "Registro de Personal | SARCE";
include '../../includes/layout_header.php';
?>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php

if (isset($_POST['registrar_personal'])) {
    $userCtrl = new UsuarioController($conexion);
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
    <h2 style="color: #002347; text-align: center; margin-bottom: 5px;">
        <i class="fas fa-user-plus"></i> Nuevo Usuario
    </h2>
    <p style="text-align: center; color: #888; font-size: 12px; margin-bottom: 20px;">Personal del Ambulatorio Rural II Jají</p>
    
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
            <label><i class="fas fa-shield-alt"></i> Pregunta de Seguridad 1:</label>
            <select name="p1" required>
                <option value="">Seleccione...</option>
                <option value="¿Cuál es el nombre de tu primera mascota?">¿Cuál es el nombre de tu primera mascota?</option>
                <option value="¿En qué ciudad nació tu madre?">¿En qué ciudad nació tu madre?</option>
            </select>
            <input type="text" name="r1" placeholder="Respuesta 1" required style="margin-top:8px;">
        </div>

        <div class="input-box">
            <label><i class="fas fa-shield-alt"></i> Pregunta de Seguridad 2:</label>
            <select name="p2" required>
                <option value="">Seleccione...</option>
                <option value="¿Cuál era el nombre de tu escuela primaria?">¿Cuál era el nombre de tu escuela primaria?</option>
                <option value="¿Cuál es tu color favorito?">¿Cuál es tu color favorito?</option>
            </select>
            <input type="text" name="r2" placeholder="Respuesta 2" required style="margin-top:8px;">
        </div>

        <div class="input-box">
            <label><i class="fas fa-shield-alt"></i> Pregunta de Seguridad 3:</label>
            <select name="p3" required>
                <option value="">Seleccione...</option>
                <option value="¿Cuál es el nombre de tu mejor amigo de la infancia?">¿Cuál es el nombre de tu mejor amigo de la infancia?</option>
                <option value="¿Cuál fue tu primer trabajo?">¿Cuál fue tu primer trabajo?</option>
            </select>
            <input type="text" name="r3" placeholder="Respuesta 3" required style="margin-top:8px;">
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

<script>
function togglePassword(inputId, icon) {
    const input = document.getElementById(inputId);
    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = "password";
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>

<?php include '../../includes/layout_footer.php'; ?>