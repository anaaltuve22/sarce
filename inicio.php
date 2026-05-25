<?php
require_once 'includes/auth.php'; // Correcto si auth.php está en includes/

$pageTitle = "Inicio | SARCE";

// El autoloader ya cargó los modelos y controladores necesarios
$consultaCtrl = new ConsultaController($conexion);
$stats = $consultaCtrl->obtenerEstadisticas();
$total_p = $stats['pacientes'];
$total_c = $stats['consultas_hoy'];

// Obtener datos del usuario logueado
$usuarioCtrl = new UsuarioController($conexion);
$datos_usuario = $usuarioCtrl->obtenerPorId($_SESSION['id_usuario_rel']);

include 'includes/layout_header.php'; // Correcto si layout_header.php está en includes/
?>

        <div class="welcome-box">
            <i class="fas fa-hospital-user"></i>
            <h1>Bienvenido(a) al Sistema, <?php echo htmlspecialchars(mb_convert_case($datos_usuario['nombre'] . " " . $datos_usuario['apellido'], MB_CASE_TITLE, "UTF-8")); ?></h1>
            <p>Usted se encuentra en el panel de control del Ambulatorio Rural II de Jají. Utilice el menú lateral para gestionar la información.</p>
            
            <div class="quick-stats">
                <div class="mini-card">
                    <h4><i class="fas fa-users"></i> Pacientes Registrados</h4>
                    <span><?php echo $total_p; ?></span>
                </div>
                <div class="mini-card">
                    <h4><i class="fas fa-notes-medical"></i> Consultas de Hoy</h4>
                    <span><?php echo $total_c; ?></span>
                </div>
            </div>
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

<?php include 'includes/layout_footer.php'; // Correcto si layout_footer.php está en includes/ ?>