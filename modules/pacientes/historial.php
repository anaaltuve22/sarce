<?php
require_once '../../includes/auth.php';

if(!isset($_GET['cedula'])){
    echo "No se especificó un paciente.";
    exit();
}

// 1. Datos del paciente
$pacienteCtrl = new PacienteController($conexion);
$paciente = $pacienteCtrl->obtenerPorCedula($_GET['cedula']);

if (!$paciente) { die("Paciente no encontrado."); }

// 2. Consultas
$consultaCtrl = new ConsultaController($conexion);
$resultado_c = $consultaCtrl->obtenerHistorial($_GET['cedula']);

$pageTitle = "Historial Médico | SARCE";
include '../../includes/layout_header.php';
?>

<div class="ficha">
    <div class="encabezado-ficha">
        <div>
            <h2><i class="fas fa-file-medical"></i> HISTORIAL CLÍNICO</h2>
            <small>Ambulatorio Rural II de Jají</small>
        </div>
        <button onclick="window.print()" class="btn-sarce btn-sarce-success no-print"><i class="fas fa-print"></i> Imprimir Todo</button>
    </div>
    
    <div class="datos-paciente-historial">
        <div><strong>Paciente:</strong> <?php echo strtoupper($paciente['nombre'] . " " . $paciente['apellido']); ?></div>
        <div><strong>Cédula:</strong> V-<?php echo $paciente['cedula']; ?></div>
        <div><strong>Género:</strong> <?php echo $paciente['genero']; ?></div>
        <div><strong>Edad:</strong> <?php echo $paciente['edad']; ?> años</div>
    </div>

    <h3><i class="fas fa-history"></i> Visitas Médicas</h3>
    <br>

    <?php if(mysqli_num_rows($resultado_c) > 0): ?>
        <?php while($c = mysqli_fetch_assoc($resultado_c)): ?>
            <div class="card-consulta">
                <div class="card-header-consulta">
                    <span style="font-weight:bold; color:#002347;"><i class="far fa-calendar-alt"></i> Fecha: <?php echo date("d/m/Y", strtotime($c['fecha'])); ?></span>
                    <span style="color: #666; font-size: 13px;"><b>Signos:</b> <?php echo $c['tension']; ?> mmHg | <?php echo $c['peso']; ?> kg | <?php echo $c['temperatura']; ?> °C</span>
                </div>
                
                <p style="margin: 5px 0;"><b>Motivo:</b> <?php echo $c['motivo']; ?></p>
                <p style="margin: 5px 0;"><b>Diagnóstico:</b> <?php echo !empty($c['nombre_patologia']) ? strtoupper($c['nombre_patologia']) : 'No especificado'; ?></p>
                <p style="margin: 5px 0;"><b>Tratamiento:</b> <?php echo nl2br($c['tratamiento']); ?></p>
                
                <div style="background:#f0fdf4; padding:10px; border-radius:8px; margin-top:10px; border: 1px solid #dcfce7;">
                    <b style="color:#166534;"><i class="fas fa-pills"></i> Medicación Entregada:</b><br>
                    <?php 
                    if (!empty($c['medicamentos_entregados'])) {
                        echo "<span style='color:#166534;'>- " . strtoupper($c['medicamentos_entregados']) . "</span>";
                    } else {
                        echo "<span style='color:gray; font-size:12px;'>No se suministraron medicamentos en esta visita.</span>";
                    }
                    ?>
                </div>

                <div class="no-print" style="margin-top:15px; text-align:right;">
                    <a href="<?php echo MOD_CONSULTAS; ?>imprimir_consulta.php?id=<?php echo $c['id_consulta']; ?>" class="btn-sarce btn-sarce-primary">
                        <i class="fas fa-file-invoice"></i> Reimprimir Ticket
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div style="text-align:center; padding: 40px; color: #718096; background: #f8fafc; border-radius: 10px;">
            <i class="fas fa-folder-open fa-3x"></i><br><br>
            Este paciente aún no registra consultas médicas.
        </div>
    <?php endif; ?>

    <div class="no-print" style="margin-top: 30px; text-align: center; border-top: 1px solid #eee; padding-top: 20px;">
        <a href="<?php echo MOD_PACIENTES; ?>lista_de_pacientes.php" class="btn-sarce btn-sarce-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
        <a href="<?php echo BASE_URL; ?>inicio.php" class="btn-sarce btn-sarce-primary">
            <i class="fas fa-home"></i> Inicio
        </a>
    </div>
</div>
<?php include '../../includes/layout_footer.php'; ?>
