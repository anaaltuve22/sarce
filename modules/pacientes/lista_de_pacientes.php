<?php
// 1. CONFIGURACIÓN E INICIO DE SESIÓN
require_once '../../includes/auth.php';

$pacienteCtrl = new PacienteController($conexion);
$resultado = $pacienteCtrl->listar();

$pageTitle = "Listado de Pacientes | SARCE";
include '../../includes/layout_header.php';
?>
    <div class="header-tablero">
        <h2><i class="fas fa-users"></i> Listado General de Pacientes</h2>
        <a href="<?php echo MOD_PACIENTES; ?>registrar_paciente.php" class="btn-original btn-atender">
            <i class="fas fa-plus"></i> REGISTRAR PACIENTE
        </a>
    </div>

    <div class="container-tabla">
        <div class="search-box-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="buscador" class="search-box" placeholder="Escriba cédula, nombre o apellido para filtrar...">
        </div>

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

<?php include '../../includes/layout_footer.php'; ?>