<?php
// 1. CONFIGURACIÓN E INICIO DE SESIÓN
require_once '../../includes/auth.php';

$pacienteCtrl = new PacienteController($conexion);
$busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : null;
$resultado = $pacienteCtrl->listar($busqueda);

$pageTitle = "Listado de Pacientes | SARCE";
include '../../includes/layout_header.php';
?>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="container-tabla">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h2 style="margin: 0;"><i class="fas fa-users"></i> Listado General de Pacientes</h2>
            <a href="<?php echo MOD_PACIENTES; ?>registrar_paciente.php" class="btn-original btn-atender" style="padding: 12px 20px; font-size: 14px; text-decoration: none;">
                <i class="fas fa-plus"></i> REGISTRAR NUEVO PACIENTE
            </a>
        </div>

        <form action="" method="GET" class="search-box-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" name="buscar" id="buscador" class="search-box" 
                   placeholder="Escriba cédula, nombre o apellido para filtrar..."
                   value="<?php echo htmlspecialchars($busqueda ?? ''); ?>">
        </form>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%;">Cédula</th>
                        <th style="width: 35%;">Apellidos y Nombres</th>
                        <th style="width: 10%; text-align: center;">Género</th>
                        <th style="width: 40%; text-align: center;">Acciones de Control</th>
                    </tr>
                </thead>
                <tbody id="cuerpoTabla">
                    <?php while($f = mysqli_fetch_assoc($resultado)): ?>
                    <tr>
                        <td><b style="color: #002347;"><?php echo $f['cedula']; ?></b></td>
                        <td style="text-transform: uppercase; font-weight: 500;">
                            <?php echo $f['apellido'] . ", " . $f['nombre']; ?>
                        </td>
                        <td style="text-align: center;">
                            <?php if($f['genero'] == 'Masculino'): ?>
                                <i class="fas fa-mars" style="color: #007bff;" title="Masculino"></i>
                            <?php elseif($f['genero'] == 'Femenino'): ?>
                                <i class="fas fa-venus" style="color: #e83e8c;" title="Femenino"></i>
                            <?php else: ?>
                                <i class="fas fa-user-tag" title="Otro"></i>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="acciones-flex">
                                <a href="<?php echo MOD_PACIENTES; ?>editar_paciente.php?cedula=<?php echo $f['cedula']; ?>" class="btn-original btn-editar">
                                    <i class="fas fa-edit"></i> EDITAR
                                </a>

                                <a href="<?php echo MOD_CONSULTAS; ?>atender.php?cedula=<?php echo $f['cedula']; ?>" class="btn-original btn-atender">
                                    <i class="fas fa-stethoscope"></i> ATENDER
                                </a>

                                <a href="<?php echo MOD_PACIENTES; ?>historial.php?cedula=<?php echo $f['cedula']; ?>" class="btn-original btn-historial">
                                    <i class="fas fa-history"></i> HISTORIAL
                                </a>

                                <?php if ($_SESSION['rol'] === 'admin'): ?>
                                <a href="javascript:void(0);" 
                                   class="btn-original btn-inhabilitar"
                                   onclick="confirmarInhabilitacion('<?php echo $f['cedula']; ?>')">
                                    <i class="fas fa-user-slash"></i>INHABILITAR
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
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

<script>
/**
 * Confirmación para inhabilitar paciente
 */
function confirmarInhabilitacion(cedula) {
    Swal.fire({
        title: '¿Está seguro?',
        text: "El paciente dejará de aparecer en las listas de atención.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, inhabilitar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'inhabilitar.php?cedula=' + cedula;
        }
    });
}

/**
 * Filtro visual rápido (opcional, mejora la experiencia al escribir)
 */
document.getElementById('buscador').addEventListener('keyup', function() {
    let filtro = this.value.toLowerCase();
    let filas = document.querySelectorAll("#cuerpoTabla tr");
    filas.forEach(fila => {
        fila.style.display = fila.textContent.toLowerCase().includes(filtro) ? "" : "none";
    });
});
</script>

<?php include '../../includes/layout_footer.php'; ?>