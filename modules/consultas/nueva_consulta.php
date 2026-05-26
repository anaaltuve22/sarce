<?php
require_once '../../includes/auth.php';

// Si recibimos datos por el formulario
if(isset($_POST['guardar_consulta'])) {
    $consultaCtrl = new ConsultaController($conexion);
    
    // Preparamos los datos para que coincidan con lo que espera el controlador y el modelo
    // Ya que este es un formulario simplificado, asignamos valores por defecto a los campos faltantes
    $_POST['cedula_paciente'] = $_POST['cedula'];
    $_POST['procedencia']     = "Entrada Manual Directa";
    $_POST['id_patologia']    = 1; // ID genérico o por defecto
    $_POST['motivo']          = "Consulta registrada vía formulario rápido";
    $_POST['medicamentos_entregados'] = '';
    $_POST['edad']            = 0; // El modelo espera este entero

    $resultado = $consultaCtrl->registrar($_POST);

    // Usamos SweetAlert2 para mantener la consistencia visual del sistema
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        window.onload = function() {
            Swal.fire({
                icon: '{$resultado['status']}',
                title: '" . ($resultado['status'] == 'success' ? '¡Éxito!' : 'Error') . "',
                text: '" . addslashes($resultado['msg'] ?? 'Consulta procesada') . "'
            }).then(() => {
                window.location = '" . ($resultado['status'] == 'success' ? 'reporte.php' : 'javascript:history.back()') . "';
            });
        };
    </script>";
    exit();
}

$pageTitle = "Nueva Consulta Diario | SARCE";
include '../../includes/layout_header.php';
?>
<div class="form-consulta-manual">
    <h2>Registro Diario de Pacientes</h2>
    <form method="POST">
        <input type="number" name="cedula" value="<?php echo htmlspecialchars($_GET['cedula'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Cédula del Paciente" required>

        <input type="number" name="cedula_personal" placeholder="Cédula del Personal que atiende" required>

        <input type="text" name="direccion" placeholder="Dirección (Sector/Calle)" required>
        
        <div class="fila-vitales-flex">
            <input type="text" name="tension" placeholder="T/A" required>
            <input type="text" name="peso" placeholder="Peso" required>
            <input type="text" name="talla" placeholder="Talla" required>
            <input type="text" name="temperatura" placeholder="Tem" required>
        </div>

        <textarea name="tratamiento" placeholder="Observación / Tratamiento (TTO)" rows="3" required></textarea>
        
        <button type="submit" name="guardar_consulta" class="btn-sarce btn-sarce-success">Guardar en Libro Diario</button>
    </form>
    <br>
    <a href="<?php echo BASE_URL; ?>index.php" class="link-volver-centro">Cancelar y volver</a>
</div>
<?php include '../../includes/layout_footer.php'; ?>
