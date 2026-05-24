<?php
require_once '../../includes/auth.php';

// Habilitar personal si se solicita desde la alerta
if (isset($_GET['habilitar']) && isset($_GET['cedula'])) {
    $ced_hab = mysqli_real_escape_string($conexion, $_GET['cedula']);
    mysqli_query($conexion, "UPDATE personal SET estado = 1 WHERE cedula = '$ced_hab'");
    
    // Registro en Bitácora: Rehabilitación de Personal
    $id_usu_rel = $_SESSION['id_usuario_rel'] ?? null;
    $accion_log = "Personal médico rehabilitado en el sistema (C.I: $ced_hab)";
    mysqli_query($conexion, "INSERT INTO bitacora (usuario, id_usuario_rel, accion, fecha_hora) VALUES ('{$_SESSION['admin']}', '$id_usu_rel', '$accion_log', NOW())");

    header("Location: " . MOD_PERSONAL . "personal.php");
    exit();
}

// Manejo de validación en tiempo real (AJAX) integrada
if (isset($_GET['verificar_ajax'])) {
    $cedula = mysqli_real_escape_string($conexion, $_GET['cedula']);
    $stmt = mysqli_prepare($conexion, "SELECT estado FROM personal WHERE cedula = ?");
    mysqli_stmt_bind_param($stmt, "s", $cedula);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $estado);
    $found = mysqli_stmt_fetch($stmt);
    
    header('Content-Type: application/json');
    echo json_encode(['existe' => ($found !== null), 'estado' => $estado]);
    mysqli_stmt_close($stmt);
    exit; // Detenemos la ejecución para devolver solo el JSON
}

$pageTitle = "Registrar Personal | SARCE";
include '../../includes/layout_header.php';
?>

<?php
if (isset($_POST['registrar_personal'])) {
    $personalCtrl = new PersonalController($conexion);
    $resultado = $personalCtrl->registrar($_POST);

    echo "<script>
        Swal.fire({
            icon: '{$resultado['status']}',
            title: '" . ($resultado['status'] == 'success' ? '¡Éxito!' : 'Error') . "',
            text: '{$resultado['msg']}',
            confirmButtonColor: '#28a745'
        }).then(() => {
            " . ($resultado['status'] == 'success' ? "window.location='" . MOD_PERSONAL . "personal.php';" : "window.history.back();") . "
        });
    </script>";
}
?>
    <div class="form-container">
            <h2 style="color: #002347; text-align: center; margin-bottom: 30px;">
                <i class="fas fa-user-md"></i> Registro de Personal
            </h2>
            <form method="POST">
                <div class="form-group">
                    <label>Cédula de Identidad</label>
                    <input type="text" 
                           name="cedula"
                           id="cedula_input" 
                           onkeypress="return soloNumeros(event)" 
                           oninput="verificarCedulaPersonalRealTime(this.value)"
                           maxlength="8" 
                           placeholder="Ej: 12345678" 
                           required>
                </div>

            
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Nombres:</label>
                        <input type="text" name="nombre" maxlength="25" onkeypress="return soloLetras(event)" placeholder="Ej: Juan" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Apellidos:</label>
                        <input type="text" name="apellido" maxlength="25" onkeypress="return soloLetras(event)" placeholder="Ej: Rojas" required>
                    </div>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-briefcase"></i> Cargo o Especialidad:</label>
                    <select name="cargo">
                        <option value="Médico General">Médico General</option>
                        <option value="Médico Especialista">Médico Especialista</option>
                        <option value="Enfermero(a)">Enfermero(a)</option>
                        <option value="Personal Administrativo">Personal Administrativo</option>
                        <option value="Camillero / Servicios">Camillero / Servicios</option>
                    </select>
                </div>
                <div class="btn-container-sarce">
                
                    <button type="submit" name="registrar_personal" class="btn-sarce" style="background-color: #28a745;">
                        <i class="fas fa-save"></i> GUARDAR
                    </button>
                </div>
            </form>
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