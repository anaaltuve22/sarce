<?php 
// Activar la visualización de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/auth.php';

// Lógica para habilitar paciente si se solicita vía GET
if (isset($_GET['habilitar']) && isset($_GET['cedula'])) {
    $pacienteCtrl = new PacienteController($conexion);
    $pacienteCtrl->habilitar($_GET['cedula']);
    $_SESSION['success_message'] = "Paciente habilitado correctamente.";
    header("Location: atender.php?cedula=" . $_GET['cedula']);
    exit();
}

if (isset($_GET['cedula'])) { 
    // Usamos el controlador para obtener los datos del paciente
    $pacienteCtrl = new PacienteController($conexion);
    $paciente = $pacienteCtrl->obtenerPorCedula($_GET['cedula']);

    if (!$paciente) {
        $_SESSION['error_message'] = "Error: Paciente no encontrado.";
        header("Location: " . MOD_PACIENTES . "lista_de_pacientes.php");
        exit();
    }

    // Obtener personal médico (solo activos)
    $personalCtrl = new PersonalController($conexion);
    $res_personal = $personalCtrl->listar();

    // Obtener patologías
    $consultaCtrl = new ConsultaController($conexion);
    $res_pat = $consultaCtrl->listarPatologias();

} else {
    $_SESSION['error_message'] = "Error: Cédula del paciente no especificada.";
    header("Location: " . MOD_PACIENTES . "lista_de_pacientes.php");
    exit();
}

// Pre-seleccionar el profesional si está logueado y es parte del personal
$cedula_profesional_logueado = null;
if (isset($_SESSION['id_usuario_rel'])) {
    $personalCtrl = new PersonalController($conexion);
    $cedula_profesional_logueado = $personalCtrl->obtenerCedulaPorUsuario($_SESSION['id_usuario_rel']);
}

$pageTitle = "Consulta Médica | SARCE";
include '../../includes/layout_header.php';
?>
<?php

$direccion_p = !empty($paciente['direccion']) ? $paciente['direccion'] : "No registrada"; 
$edad_p = ($paciente['edad'] > 0) ? $paciente['edad'] : "No registrada"; 
?> 
<div class="card-atencion"> 
    <h2><i class="fas fa-stethoscope"></i> ATENCIÓN MÉDICA</h2>
    
    <div class="form-content"> 
        <div class="patient-badge"> 
            <div class="data-item"> <b>Paciente:</b> <span><?php echo strtoupper($paciente['nombre'] . " " . $paciente['apellido']); ?></span> </div> 
            <div class="data-item"> <b>Cédula:</b> <span>V-<?php echo $paciente['cedula']; ?></span> </div> 
            <div class="data-item"> <b>Edad:</b> <span><?php echo $edad_p; ?> años</span> </div> 
            <div class="data-item"> <b>Dirección:</b> <span><?php echo $direccion_p; ?></span> </div> 
        </div> 

        <form action="procesar_consulta.php" method="POST" id="formAtencion"> 
            <input type="hidden" name="cedula_paciente" value="<?php echo $paciente['cedula']; ?>"> 
            <input type="hidden" name="edad" value="<?php echo $paciente['edad']; ?>"> 
            <input type="hidden" name="direccion" value="<?php echo $direccion_p; ?>">

            <div class="form-grid">
                <div class="input-box">
                    <label><i class="fas fa-user-md"></i> Profesional que Atiende:</label>
                    <select name="cedula_personal" class="form-control" required>
                        <option value="">Seleccione el médico o enfermera...</option>
                        <?php while($p = mysqli_fetch_assoc($res_personal)):
                            $is_selected = ($cedula_profesional_logueado == $p['cedula']) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $p['cedula']; ?>" <?php echo $is_selected; ?>>
                                <?php echo $p['nombre'] . " " . $p['apellido']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="input-box">
                    <label><i class="fas fa-map-marker-alt"></i> Consultorio de Procedencia:</label> 
                    <select name="consultorio_procedencia" required> 
                        <option value="">Seleccione el sector...</option>
                        <option value="Piedra Blanca">Piedra Blanca</option> 
                        <option value="Macho">Macho</option> 
                        <option value="Capaz">Capaz</option> 
                        <option value="Loma del Carmen">Loma del Carmen</option> 
                        <option value="Jají (Principal)">Ambulatorio Jají</option> 
                    </select>
                </div>
            </div>

            <div class="input-box">
                <label><i class="fas fa-comment-medical"></i> Motivo de la Consulta:</label> 
                <textarea name="motivo" rows="2" placeholder="Describa brevemente el malestar..."></textarea> 
            </div>

            <div class="input-box">
                <label><i class="fas fa-disease"></i> Diagnóstico (Patología):</label>
                <select name="id_patologia" id="select_patologia" required onchange="verificarNuevaPatologia()">
                    <option value="">Seleccione una patología...</option>
                    <?php
                    mysqli_data_seek($res_pat, 0);
                    while($pat = mysqli_fetch_assoc($res_pat)) {
                        echo "<option value='".$pat['id']."'>".$pat['nombre_patologia']."</option>";
                    }
                    ?>
                    <option value="nueva" style="font-weight: bold; color: #000;">+ OTRA (AGREGAR NUEVA)</option>
                </select>
            </div>

            <div id="box_nueva_patologia" class="bg-alerta-naranja" style="display: none;">
                <label class="label-naranja"><i class="fas fa-plus-circle"></i> Nombre de la Nueva Patología:</label>
                <input type="text" name="nueva_patologia_nombre" id="input_nueva_patologia" placeholder="Ej: Hipertensión Arterial">
            </div>

            <div class="vitals-grid">
                <div>
                    <label style="margin-top: 0;"><i class="fas fa-heartbeat"></i> T/A:</label>
                    <input type="text" name="tension" placeholder="120/80" required>
                </div>
                <div>
                    <label style="margin-top: 0;"><i class="fas fa-weight"></i> Peso:</label>
                    <input type="text" name="peso" placeholder="kg" required>
                </div>
                <div>
                    <label style="margin-top: 0;"><i class="fas fa-ruler-vertical"></i> Talla:</label>
                    <input type="text" name="talla" placeholder="cm" required>
                </div>
                <div>
                    <label style="margin-top: 0;"><i class="fas fa-thermometer-half"></i> Tem:</label>
                    <input type="text" name="temperatura" placeholder="°C" required>
                </div>
            </div>

            <div class="input-box">
                <label><i class="fas fa-pills"></i> Tratamiento Sugerido:</label> 
                <textarea name="tratamiento" rows="3" placeholder="Indicaciones médicas y dosis..."></textarea> 
            </div>

            <label style="display: flex; align-items: center; gap: 10px; background: #f0fff4; padding: 12px; border-radius: 10px; cursor: pointer; margin-top: 15px; border: 1px solid #c6f6d5;"> 
                <input type="checkbox" name="entrega_medicina" id="entrega_check" style="width: auto;" onchange="toggleMedicamentos()"> 
                <span style="color: #000; font-weight: bold;"><i class="fas fa-hand-holding-medical"></i> Entregar del medicamento </span> 
            </label> 

            <div id="seccion_medicamentos" style="display: none; margin-top: 15px; padding: 20px; background: #f8fafc; border-radius: 12px; border: 2px dashed #cbd5e0;"> 
                <label>Seleccione los  medicamentos:</label> 
                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <input type="text" id="buscador_med" list="lista_meds" placeholder="Buscar medicamento..." style="flex: 1; padding: 12px; border: 2px solid #edf2f7; border-radius: 10px;">
                    <datalist id="lista_meds">
                        <?php 
                        $medCtrl = new MedicamentoController($conexion);
                        $res_meds_list = $medCtrl->listar();
                        while($m = mysqli_fetch_assoc($res_meds_list)): 
                        ?>
                            <option value="<?php echo htmlspecialchars($m['nombre']); ?>">
                        <?php endwhile; ?>
                    </datalist>
                    <button type="button" onclick="agregarMedicamento()" class="btn-sarce" style="background-color: #002347; color: white; border-radius: 10px; padding: 0 20px;">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div id="lista_chips" style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 10px;"></div>
                
                <!-- Input oculto que procesar_consulta.php recibirá -->
                <input type="hidden" name="medicamentos_manual" id="meds_final" value="">
            </div> 

            <div class="btn-container-sarce">
                <button type="submit" class="btn-sarce" style="background-color: #28a745;">
                    <i class="fas fa-check-circle"></i> GUARDAR
                </button>
            </div>
        </form> 
    </div> 
</div> 


<?php if (isset($paciente) && $paciente['estado'] == 0): ?>
<script>
    Swal.fire({
        title: 'Paciente Inhabilitado',
        text: 'Este paciente se encuentra inhabilitado en el sistema. ¿Desea habilitarlo para proceder con la consulta?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, habilitar',
        cancelButtonText: 'No, regresar',
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'atender.php?cedula=<?php echo $paciente['cedula']; ?>&habilitar=1';
        } else {
            window.location.href = '<?php echo MOD_PACIENTES; ?>lista_de_pacientes.php';
        }
    });
</script>
<?php endif; ?>
<?php include '../../includes/layout_footer.php'; ?>
