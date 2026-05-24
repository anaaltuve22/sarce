<?php
// 1. CONFIGURACIÓN E INICIO DE SESIÓN
require_once '../../includes/auth.php';

// Habilitar paciente si se solicita desde la alerta
if (isset($_GET['habilitar']) && isset($_GET['cedula'])) {
    $ced_hab = mysqli_real_escape_string($conexion, $_GET['cedula']);
    mysqli_query($conexion, "UPDATE pacientes SET estado = 1 WHERE cedula = '$ced_hab'");
    header("Location: " . MOD_PACIENTES . "lista_de_pacientes.php");
    exit();
}

// Manejo de validación en tiempo real (AJAX) integrada
if (isset($_GET['verificar_ajax'])) {
    $cedula = mysqli_real_escape_string($conexion, $_GET['cedula']);
    $stmt = mysqli_prepare($conexion, "SELECT estado FROM pacientes WHERE cedula = ?");
    mysqli_stmt_bind_param($stmt, "s", $cedula);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $estado);
    $found = mysqli_stmt_fetch($stmt);
    
    header('Content-Type: application/json');
    echo json_encode(['existe' => ($found !== null), 'estado' => $estado]);
    mysqli_stmt_close($stmt);
    exit; // Detenemos la ejecución para devolver solo el JSON
}

$pageTitle = "Registrar Paciente | SARCE";
include '../../includes/layout_header.php'; 
?>
<!-- Importamos SweetAlert2 después del header para asegurar compatibilidad -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
$pacienteCtrl = new PacienteController($conexion);

if (isset($_POST['registrar'])) {
    $resultado = $pacienteCtrl->registrar($_POST);
    
    echo "<script>
        Swal.fire({
            icon: '{$resultado['status']}',
            title: '" . ($resultado['status'] == 'success' ? '¡Éxito!' : 'Aviso') . "',
            text: '{$resultado['msg']}'
        }).then(() => {
            " . ($resultado['status'] == 'success' ? "window.location='" . MOD_PACIENTES . "lista_de_pacientes.php';" : "window.history.back();") . "
        });
    </script>";
}
?>
    <div class="contenedor-registro">
        <h2 style="text-align: center; color: #002347; margin-bottom: 30px;">
            <i class="fas fa-user-plus"></i> Registro de Paciente
        </h2>
        
        <form method="POST">
            <div class="form-grid">
                <div class="full-width">
                    <label>Cédula de Identidad</label>
<input type="text" 
       name="cedula"
       id="cedula_input" 
       onkeypress="return soloNumeros(event)" 
       oninput="verificarCedulaPacienteRealTime(this.value)"
       maxlength="8" 
       placeholder="Ej: 22656296" 
       required>
       <small>Máximo 8 dígitos sin puntos ni letras</small>
                </div>

                <div>
                    <label>Nombres</label>
                    <input type="text" name="nombre" maxlength="25" onkeypress="return soloLetras(event)" placeholder="Ej: Maria Elena" required>
                </div>

                <div>
                    <label>Apellidos</label>
                   <input type="text" name="apellido" maxlength="25" onkeypress="return soloLetras(event)" placeholder="Ej: Perez" required>
                </div>

                <div>
                    <label>Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" onchange="calcularEdad()" required>
                </div>

                <div>
                    <label>Edad</label>
                    <input type="number" name="edad" id="edad" readonly placeholder="0">
                </div>

                <div>
                    <label>Género</label>
                    <select name="genero">
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                    </select>
                </div>

                <div class="full-width">
                    <label>Teléfono de Contacto</label>
                    <input type="tel" name="telefono" placeholder="Ej: 04121234567" maxlength="15" onkeypress="return soloNumeros(event)" required>
                </div>

                <div class="full-width">
                    <label>Dirección </label>
                    <input type="text" name="direccion" required placeholder="Calle, Sector, Casa...">
                </div>
            </div>

            <div class="btn-container-sarce">
                <button type="submit" name="registrar" class="btn-sarce btn-sarce-success">
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