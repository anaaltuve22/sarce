<?php
require_once '../../includes/auth.php';

$pageTitle = "Editar Personal | SARCE";
include '../../includes/layout_header.php';
?>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php

if (isset($_GET['cedula'])) {
    $personalCtrl = new PersonalController($conexion);
    $p = $personalCtrl->obtenerPorCedula($_GET['cedula']);

    if (!$p) { die("Personal no encontrado."); }
}

if (isset($_POST['actualizar'])) {
    $personalCtrl = new PersonalController($conexion);
    $resultado = $personalCtrl->actualizar($_POST, $_POST['cedula_vieja']);

    if ($resultado['status'] === 'success') {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: '¡Actualizado!',
                confirmButtonColor: '#28a745'
            }).then(() => {
                window.location='" . MOD_PERSONAL . "personal.php';
            });
        </script>";
        exit();
    }
}
?>
<div class="box">
    <h2><i class="fas fa-user-edit"></i> Editar Personal</h2>
    <form method="POST">
        <input type="hidden" name="cedula_vieja" value="<?php echo $p['cedula']; ?>">
        <label><i class="fas fa-id-card"></i> Cédula:</label>
        <input type="text" 
               name="cedula" 
               value="<?php echo $p['cedula']; ?>" 
               maxlength="8" 
               onkeypress="return soloNumeros(event)" 
               required>
        <label><i class="fas fa-user"></i> Nombre:</label>
        <input type="text" name="nombre" value="<?php echo $p['nombre']; ?>" maxlength="25" onkeypress="return soloLetras(event)" required>
        <label><i class="fas fa-user"></i> Apellido:</label>
        <input type="text" name="apellido" value="<?php echo $p['apellido']; ?>" maxlength="25" onkeypress="return soloLetras(event)" required>
        <label><i class="fas fa-briefcase"></i> Cargo:</label>
        <select name="cargo">
            <option value="Médico General" <?php if($p['cargo'] == 'Médico General') echo 'selected'; ?>>Médico General</option>
            <option value="Médico Especialista" <?php if($p['cargo'] == 'Médico Especialista') echo 'selected'; ?>>Médico Especialista</option>
            <option value="Enfermero(a)" <?php if($p['cargo'] == 'Enfermero(a)') echo 'selected'; ?>>Enfermero(a)</option>
            <option value="Personal Administrativo" <?php if($p['cargo'] == 'Personal Administrativo') echo 'selected'; ?>>Personal Administrativo</option>
        </select>
        <div class="btn-container-sarce">
            <button type="submit" name="actualizar" class="btn-sarce" style="background-color: #28a745;">
                <i class="fas fa-save"></i> ACTUALIZAR
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