<?php
require_once '../../includes/auth.php';

$sistemaCtrl = new SistemaController($conexion);
$resultado = $sistemaCtrl->descargarManual();

// Si llegamos aquí es porque hubo un error (el archivo no existe)
if (is_array($resultado) && $resultado['status'] === 'error') {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        window.onload = function() {
            Swal.fire({ icon: 'error', title: 'Error', text: '{$resultado['msg']}' }).then(() => { window.history.back(); });
        };
    </script>";
}
?>