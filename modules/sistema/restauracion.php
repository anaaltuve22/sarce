<?php
require_once '../../includes/auth.php';

// Seguridad: Solo el administrador puede restaurar la base de datos
if (!isset($_SESSION['admin']) || $_SESSION['rol'] !== 'admin') {
    header("Location: " . BASE_URL . "inicio.php");
    exit();
}

$pageTitle = "Restaurar Base de Datos | SARCE";
include '../../includes/layout_header.php';
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-restaurar">
    <div class="icon-warn"><i class="fas fa-exclamation-triangle"></i></div>
    <h2 style="color: #002347;">Restauración del Sistema</h2>
    <p style="color: #4a5568; font-size: 14px;">
        Seleccione el archivo de respaldo (<strong>.sql</strong>) para restaurar la base de datos. 
        <br><br>
        <span style="color: #dc3545;"><strong>Atención:</strong> Esta acción sobrescribirá todos los datos actuales.</span>
    </p>

    <form action="" method="POST" enctype="multipart/form-data" id="formRestaurar">
        <input type="file" name="backup_file" class="file-input" accept=".sql" required>
        
        <div style="display: flex; justify-content: center; gap: 15px;">
            <a href="<?php echo BASE_URL; ?>inicio.php" class="btn-sarce" style="background-color: #6c757d; color: white; text-decoration: none; padding: 12px 20px; border-radius: 10px;">
                CANCELAR
            </a>
            <button type="submit" name="importar" class="btn-restaurar">
                <i class="fas fa-upload"></i> INICIAR RESTAURACIÓN
            </button>
        </div>
    </form>
</div>

<?php
if (isset($_POST['importar'])) {
    $sistemaCtrl = new SistemaController($conexion);
    $resultado = $sistemaCtrl->restaurar($_FILES['backup_file']);

    echo "<script>
        Swal.fire({
            icon: '{$resultado['status']}',
            title: '" . ($resultado['status'] == 'success' ? '¡Restauración Exitosa!' : 'Error en Restauración') . "',
            text: '{$resultado['msg']}',
            confirmButtonColor: '#28a745'
        }).then(() => {
            " . ($resultado['status'] == 'success' ? "window.location.href = '" . BASE_URL . "inicio.php';" : "window.history.back();") . "
        });
    </script>";
}
?>

<footer class="footer-sarce-principal">
    <p>&copy; <?php echo date("Y"); ?> SARCE - Sistema de Control de Registro.</p>
</footer>

<?php include '../../includes/layout_footer.php'; ?>
