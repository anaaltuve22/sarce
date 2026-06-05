<?php
require_once '../../includes/auth.php';

$pageTitle = "Registrar Medicamento | SARCE";
include '../../includes/layout_header.php';
?>

<?php
if (isset($_POST['registrar'])) {
    $medCtrl = new MedicamentoController($conexion);
    $resultado = $medCtrl->registrar($_POST);
    
    echo "<script>
        Swal.fire({
            icon: '{$resultado['status']}',
            title: '" . ($resultado['status'] == 'success' ? '¡Éxito!' : 'Error') . "',
            text: '{$resultado['msg']}',
            confirmButtonColor: '#28a745'
        }).then(() => {
            " . ($resultado['status'] == 'success' ? "window.location='medicamentos.php';" : "window.history.back();") . "
        });
    </script>";
}
?>

<div class="form-container">
    <h2 class="titulo-med">
        <i class="fas fa-pills"></i> Nuevo Medicamento
    </h2>
    <form method="POST">
        <div class="form-group">
            <label><i class="fas fa-tag"></i> Nombre del Medicamento:</label>
            <input type="text" name="nombre" maxlength="50" placeholder="Ej: Acetaminofén 500mg" required>
        </div>
        <div class="form-group">
            <label><i class="fas fa-align-left"></i> Descripción / Presentación:</label>
            <textarea name="descripcion" maxlength="50" rows="3" placeholder="Ej: Tabletas - Caja de 20 unidades" required></textarea>
        </div>
        <div class="btn-container-sarce">
            <button type="submit" name="registrar" class="btn-sarce" style="background-color: #28a745;">
                <i class="fas fa-save"></i> GUARDAR
            </button>
        </div>
    </form>
</div>
<?php include '../../includes/layout_footer.php'; ?>