<?php
// 1. Configuración de errores (Activados para depuración)
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/auth.php';
?>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php

// VALIDACIÓN 1: Seguridad de acceso
if (!isset($_SESSION['admin'])) {
    header("Location: " . BASE_URL . "login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cedula_paciente'])) {
    $consultaCtrl = new ConsultaController($conexion);
    
    // Mapeo de campos del formulario a los que espera el Modelo/Controlador
    $_POST['procedencia'] = $_POST['consultorio_procedencia'] ?? '';
    $_POST['medicamentos_entregados'] = $_POST['medicamentos_manual'] ?? '';

    $resultado = $consultaCtrl->registrar($_POST);

    if ($resultado['status'] === 'success') {
        $id_consulta_generada = $resultado['id'];
        echo "<html>
        <head>
            <meta charset='UTF-8'>
            <link rel='stylesheet' href='../../assets/css/estilos_globales.css'>
        </head>
        <body>
            <div class='card-procesando'>
                <h2 style='color: #002347;'>Procesando...</h2>
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: '¡Consulta registrada con éxito!',
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        window.location='" . MOD_CONSULTAS . "imprimir_consulta.php?id=$id_consulta_generada';
                    });
                </script>
            </div>
        </body>
        </html>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '" . addslashes($resultado['msg'] ?? 'Error desconocido al registrar la consulta.') . "'
            }).then(() => { window.history.back(); });
        </script>";
    }
} else {
    header("Location: " . MOD_PACIENTES . "lista_de_pacientes.php");
}
?>