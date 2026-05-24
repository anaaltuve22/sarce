<?php
require_once '../../includes/auth.php';

// Solo el admin puede ver la bitácora
if (!isset($_SESSION['admin']) || $_SESSION['rol'] !== 'admin') { header("Location: " . BASE_URL . "inicio.php"); exit(); }

// Control de inactividad (10 minutos = 600 segundos)
if (isset($_SESSION['ultimo_acceso'])) {
    if ((time() - $_SESSION['ultimo_acceso']) > 600) {
        session_unset();
        session_destroy();
        header("Location: " . BASE_URL . "login.php");
        exit();
    }
}
$_SESSION['ultimo_acceso'] = time();

$usuarioCtrl = new UsuarioController($conexion);
$resultado = $usuarioCtrl->obtenerBitacora();

$pageTitle = "Bitácora de Seguridad | SARCE";
include '../../includes/layout_header.php';
?>
    <div class="contenedor">
        <div class="header-bitacora">
            <h2><i class="fas fa-clipboard-list"></i> Bitácora de Movimientos</h2>
        </div>
        
        <p style="color: #666; margin-bottom: 25px;">Registro detallado de acciones realizadas por el personal en el sistema <strong>SARCE</strong>.</p>
        
        <div class="search-box-container" style="position: relative; margin-bottom: 20px;">
            <i class="fas fa-search search-icon" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #cbd5e0;"></i>
            <input type="text" id="buscador" class="search-box" placeholder="Buscar por usuario o acción..." 
                   style="width: 100%; padding: 12px 12px 12px 45px; border: 2px solid #edf2f7; border-radius: 10px; font-size: 14px; outline: none;">
        </div>

        <table>
            <thead>
                <tr>
                    <th><i class="far fa-clock"></i> Fecha y Hora</th>
                    <th><i class="far fa-user"></i> Usuario</th>
                    <th><i class="fas fa-tasks"></i> Acción Realizada</th>
                </tr>
            </thead>
            <tbody id="cuerpoTabla">
                <?php while($row = mysqli_fetch_assoc($resultado)) { ?>
                <tr>
                    <td class="fecha-txt">
                        <i class="far fa-calendar-alt" style="margin-right: 5px;"></i>
                        <?php echo date("d/m/Y", strtotime($row['fecha_hora'])); ?>
                        <small style="display:block;"><?php echo date("H:i:s", strtotime($row['fecha_hora'])); ?></small>
                    </td>
                    <td>
                        <span class="user-badge">
                            <?php echo strtoupper($row['usuario']); ?>
                        </span>
                    </td>
                    <td style="font-weight: 500;">
                        <?php echo $row['accion']; ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php include '../../includes/layout_footer.php'; ?>