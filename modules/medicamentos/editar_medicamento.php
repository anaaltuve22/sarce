<?php
require_once '../../includes/auth.php';

if (isset($_GET['id'])) {
    $medCtrl = new MedicamentoController($conexion);
    $m = $medCtrl->obtenerPorId($_GET['id']);
}

$pageTitle = "Editar Medicamento | SARCE";
include '../../includes/layout_header.php';
?>

<?php
if (isset($_POST['actualizar'])) {
    $medCtrl = new MedicamentoController($conexion);
    $resultado = $medCtrl->actualizar($_POST);
    
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
    <h2 class="titulo-med"><i class="fas fa-edit"></i> Editar Medicamento</h2>
    <form method="POST">
        <input type="hidden" name="id_med" value="<?php echo $m['id']; ?>">
        <label>Nombre del Medicamento:</label>
        <input type="text" name="nombre" value="<?php echo $m['nombre']; ?>" required>
        <label>Descripción / Presentación:</label>
        <textarea name="descripcion" rows="3" required><?php echo $m['descripcion']; ?></textarea>
        <div class="btn-container-sarce">
            <button type="submit" name="actualizar" class="btn-sarce" style="background-color: #28a745;">
                <i class="fas fa-save"></i> ACTUALIZAR
            </button>
        </div>
    </form>
</div>
<?php include '../../includes/layout_footer.php'; ?>