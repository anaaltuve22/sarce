<?php
// 1. INICIO DE SESIÓN Y CONFIGURACIÓN
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/config.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SARCE - JAJÍ</title>
    
    <!-- Iconos y Fuentes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/estilos_globales.css">
</head>
<body>

    <main class="landing-wrapper" style="background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('<?php echo IMG_URL; ?>fotoambulatorio.jpg');">
        
        <nav class="nav-container">
            <?php if (!isset($_SESSION['admin'])): ?>
                <a href="login.php" class="btn-nav btn-login">
                    <i class="fas fa-user-circle"></i> INICIAR SESIÓN
                </a>
            <?php else: ?>
                <a href="inicio.php" class="btn-nav btn-panel">
                    <i class="fas fa-th-large"></i> IR AL PANEL
                </a>
            <?php endif; ?>
        </nav>

        <header class="hero-text">
            <h1>Ambulatorio Rural II <br> de Jají</h1>
            <p>ESTADO MÉRIDA, VENEZUELA</p>
        </header>

        <section class="cards-container">
            <article class="info-card">
                <h2><i class="fas fa-plus-circle" style="color: #28a745;"></i> Misión</h2>
                <p>Prestar servicios de salud integral con calidad y sentido humano a la comunidad de Jají y sus zonas rurales, garantizando el acceso oportuno a la atención médica primaria y promoviendo el bienestar colectivo.</p>
            </article>

            <article class="info-card">
                <h2><i class="fas fa-eye" style="color: #007bff;"></i> Visión</h2>
                <p>Consolidarnos como un centro asistencial modelo en el estado Mérida, reconocido por su eficiencia tecnológica y calidad humana, logrando una atención médica moderna que responda a las necesidades de la población rural.</p>
            </article>
        </section>

    </main> 
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
</body>
</html>