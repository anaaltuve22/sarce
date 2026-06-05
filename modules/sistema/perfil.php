<?php
require_once '../../includes/auth.php';
$userCtrl = new UsuarioController($conexion);
$esAdmin = (strtolower($_SESSION['rol'] ?? '') === 'admin');
$id_target = ($esAdmin && isset($_GET['id'])) ? $_GET['id'] : $_SESSION['id_usuario_rel'];

if (!$esAdmin && $id_target != $_SESSION['id_usuario_rel']) { header("Location: perfil.php"); exit(); }

if (isset($_POST['guardar'])) {
    $res = $userCtrl->actualizar($id_target, $_POST, $esAdmin);
    $resultado_perfil = $res;
}

$opcionesPreguntas = $userCtrl->obtenerOpcionesPreguntas();
$datos = $userCtrl->obtenerPorId($id_target);
$pageTitle = "Perfil de Usuario | SARCE";
include '../../includes/layout_header.php';
?>
<div class="form-container">
    <h2 class="form-header"><i class="fas fa-user-cog"></i> Perfil de Usuario</h2>

    <?php if (isset($resultado_perfil)): ?>
        <script>
            // Se dispara la alerta usando los datos procesados por PHP
            Swal.fire({
                icon: '<?php echo $resultado_perfil['status']; ?>',
                title: '<?php echo ($resultado_perfil['status'] == 'success' ? '¡Éxito!' : 'Error'); ?>',
                text: '<?php echo $resultado_perfil['msg']; ?>',
                confirmButtonColor: '#28a745'
            });
        </script>
    <?php endif; ?>

    <form method="POST">
        <div class="form-grid">
            <div class="input-box"><label>Nombres:</label><input type="text" name="nombre" value="<?php echo $datos['nombre']; ?>" maxlength="25" onkeypress="return soloLetras(event)" required></div>
            <div class="input-box"><label>Apellidos:</label><input type="text" name="apellido" value="<?php echo $datos['apellido']; ?>" maxlength="25" onkeypress="return soloLetras(event)" required></div>
        </div>
        <div class="input-box"><label>Correo:</label><input type="email" name="correo" value="<?php echo $datos['correo']; ?>" required></div>
        <div class="input-box"><label>Usuario:</label><input type="text" <?php echo $esAdmin ? 'name="usuario" maxlength="20"' : 'class="readonly-input" readonly'; ?> value="<?php echo $datos['usuario']; ?>"></div>
        
        <div class="input-box">
            <label><i class="fas fa-lock"></i> Nueva Contraseña (dejar vacío para no cambiar):</label>
            <div class="password-wrapper">
                <input type="password" name="clave" id="clave" placeholder="********" maxlength="12">
                <i class="fas fa-eye toggle-password" onclick="togglePassword('clave', this)"></i>
            </div>
        </div>

        <div class="input-box">
            <label><i class="fas fa-check-double"></i> Confirmar Contraseña:</label>
            <div class="password-wrapper">
                <input type="password" name="confirmar_clave" id="confirmar_clave" placeholder="Repita la contraseña" maxlength="12">
                <i class="fas fa-eye toggle-password" onclick="togglePassword('confirmar_clave', this)"></i>
            </div>
        </div>

        <div class="input-box">
            <label>Pregunta de Seguridad 1:</label>
            <select name="p1" onchange="validarPreguntasUnicas()" required>
                <option value="<?php echo htmlspecialchars($datos['pregunta_1'] ?? ''); ?>" selected><?php echo htmlspecialchars($datos['pregunta_1'] ?? 'Seleccione...'); ?></option>
                <?php foreach($opcionesPreguntas as $p): ?>
                    <?php if($p !== ($datos['pregunta_1'] ?? '')): ?>
                        <option value="<?php echo $p; ?>"><?php echo $p; ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
            <input type="text" name="r1" placeholder="Respuesta de seguridad" maxlength="30" onkeypress="return soloLetras(event)" required>
        </div>

        <div class="input-box">
            <label>Pregunta de Seguridad 2:</label>
            <select name="p2" onchange="validarPreguntasUnicas()" required>
                <option value="<?php echo htmlspecialchars($datos['pregunta_2'] ?? ''); ?>" selected><?php echo htmlspecialchars($datos['pregunta_2'] ?? 'Seleccione...'); ?></option>
                <?php foreach($opcionesPreguntas as $p): ?>
                    <?php if($p !== ($datos['pregunta_2'] ?? '')): ?>
                        <option value="<?php echo $p; ?>"><?php echo $p; ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
            <input type="text" name="r2" placeholder="Respuesta de seguridad" maxlength="30" onkeypress="return soloLetras(event)" required>
        </div>

        <div class="input-box">
            <label>Pregunta de Seguridad 3:</label>
            <select name="p3" onchange="validarPreguntasUnicas()" required>
                <option value="<?php echo htmlspecialchars($datos['pregunta_3'] ?? ''); ?>" selected><?php echo htmlspecialchars($datos['pregunta_3'] ?? 'Seleccione...'); ?></option>
                <?php foreach($opcionesPreguntas as $p): ?>
                    <?php if($p !== ($datos['pregunta_3'] ?? '')): ?>
                        <option value="<?php echo $p; ?>"><?php echo $p; ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
            <input type="text" name="r3" placeholder="Respuesta de seguridad" maxlength="30" onkeypress="return soloLetras(event)" required>
        </div>

        <?php if($esAdmin): ?>
        <div class="input-box">
            <label>Rol:</label>
            <select name="rol">
                <option value="secretaria" <?php echo $datos['rol']=='secretaria'?'selected':''; ?>>Secretaria</option>
                <option value="admin" <?php echo $datos['rol']=='admin'?'selected':''; ?>>Administrador</option>
            </select>
        </div>
        <?php endif; ?>
        <div class="btn-container-sarce">
            <button type="submit" name="guardar" class="btn-sarce btn-sarce-success">
                <i class="fas fa-save"></i> GUARDAR
            </button>
        </div>
    </form>
</div>
<footer class="footer-sarce-principal">
    <div class="footer-info">
        <p>
            <i class="fas fa-map-marker-alt"></i> <strong>Dirección:</strong> Jají, Municipio Campo Elías, Estado Mérida.
        </p>
        <p>
            <i class="fas fa-phone"></i> <strong>Teléfono de Atención:</strong> 04161537743
        </p>
    </div>
    <p>&copy; <?php echo date("Y"); ?> SARCE - Sistema de Control de Registro.</p>
</footer>
<?php include '../../includes/layout_footer.php'; ?>