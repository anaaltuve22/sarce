<?php
require_once '../../includes/auth.php';

// 1. CARGAR DATOS
if (isset($_GET['cedula'])) {
    $pacienteCtrl = new PacienteController($conexion);
    $p = $pacienteCtrl->obtenerPorCedula($_GET['cedula']);

    if (!$p) { 
        $_SESSION['error_message'] = "Paciente no encontrado.";
        header("Location: " . MOD_PACIENTES . "lista_de_pacientes.php");
        exit(); 
    }
}

$pageTitle = "Editar Paciente | SARCE";
include '../../includes/layout_header.php';
?>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php

if (isset($_POST['actualizar'])) {
    $pacienteCtrl = new PacienteController($conexion);
    $resultado = $pacienteCtrl->actualizar($_POST, $_POST['cedula_vieja']);

    echo "<script>
        Swal.fire({
            icon: '{$resultado['status']}',
            title: '" . ($resultado['status'] == 'success' ? '¡Actualizado!' : 'Aviso') . "',
            text: '{$resultado['msg']}',
            confirmButtonColor: '#28a745'
        }).then(() => {
            " . ($resultado['status'] == 'success' ? "window.location='" . MOD_PACIENTES . "lista_de_pacientes.php';" : "window.history.back();") . "
        });
    </script>";
    exit();
}
?>
<div class="box">
    <h2><i class="fas fa-user-edit"></i> Editar Paciente</h2>
    
    <form action="" method="POST" id="formEditar">
        <input type="hidden" name="cedula_vieja" value="<?php echo $p['cedula']; ?>">

        <label>Cédula:</label>
        <input type="text" name="cedula" value="<?php echo $p['cedula']; ?>" minlength="7" maxlength="8" onkeypress="return soloNumeros(event)" required>

        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?php echo $p['nombre']; ?>" maxlength="25" onkeypress="return soloLetras(event)" required>

        <label>Apellido:</label>
        <input type="text" name="apellido" value="<?php echo $p['apellido']; ?>" maxlength="25" onkeypress="return soloLetras(event)" required>

        <label>Fecha de Nacimiento:</label>
        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" value="<?php echo $p['fecha_nacimiento']; ?>" onchange="calcularEdad()" required>

        <label>Edad:</label>
        <input type="number" name="edad" id="edad" value="<?php echo $p['edad']; ?>" readonly>

        <label>Género:</label>
        <select name="genero" required>
            <option value="Masculino" <?php if($p['genero'] == 'Masculino') echo 'selected'; ?>>Masculino</option>
            <option value="Femenino" <?php if($p['genero'] == 'Femenino') echo 'selected'; ?>>Femenino</option>
        </select>

        <label>Teléfono:</label>
        <input type="tel" name="telefono" value="<?php echo $p['telefono']; ?>" onkeypress="return soloNumeros(event)" maxlength="15" required>

        <label>Dirección:</label>
        <input type="text" name="direccion" value="<?php echo $p['direccion']; ?>" required>

        <div class="btn-container-sarce">
            
            <button type="submit" name="actualizar" class="btn-sarce btn-sarce-success">
                <i class="fas fa-save"></i> ACTUALIZAR
            </button>
        </div>
    </form>
</div>

<?php include '../../includes/layout_footer.php'; ?>