<?php
// 1. CONFIGURACIÓN E INICIO DE SESIÓN
require_once '../../includes/auth.php';

$pageTitle = "Personal Médico | SARCE";
include '../../includes/layout_header.php';
?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; background: white; padding: 20px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-top: 6px solid #00152b;">
            <h2><i class="fas fa-user-md"></i> Personal Médico</h2>
            <a href="<?php echo MOD_PERSONAL; ?>registrar_personal.php" class="btn-original btn-atender">
                <i class="fas fa-plus"></i> REGISTRAR PERSONAL
            </a>
        </div>

        <div class="container-tabla">
            <div style="position: relative;">
                <i class="fas fa-search" style="position: absolute; left: 15px; top: 15px; color: #cbd5e0;"></i>
                <input type="text" id="buscador" class="search-box" placeholder="Buscar por nombre, cédula o cargo...">
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Cédula</th>
                            <th>Nombre y Apellido</th>
                            <th>Cargo</th>
                            <th style="text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoTabla">
                        <?php
                        $personalCtrl = new PersonalController($conexion);
                        $res = $personalCtrl->listar();
                        if($res && mysqli_num_rows($res) > 0) {
                            while($p = mysqli_fetch_assoc($res)) {
                                $boton_inhabilitar = "";
                                if ($_SESSION['rol'] === 'admin') {
                                    $boton_inhabilitar = "
                                        <a href='javascript:void(0);' class='btn-original btn-inhabilitar' style='padding: 6px 12px; font-size: 12px;' onclick=\"confirmarInhabilitacion('{$p['cedula']}', 'personal')\">
                                            <i class='fas fa-user-slash'></i> INHABILITAR 
                                        </a>";
                                }
    
                                echo "<tr>
                                        <td><b style='color:#002347;'>{$p['cedula']}</b></td>
                                        <td style='text-transform: uppercase;'>{$p['apellido']}, {$p['nombre']}</td>
                                        <td><span style='background:#e2e8f0; padding:4px 10px; border-radius:15px; font-size:12px;'>{$p['cargo']}</span></td>
                                        <td>
                                            <div style='display: flex; gap: 8px; justify-content: center;'>
                                                <a href='" . MOD_PERSONAL . "editar_personal.php?cedula={$p['cedula']}' class='btn-original btn-editar' style='padding: 6px 12px; font-size: 12px;'>
                                                    <i class='fas fa-edit'></i> EDITAR
                                                </a>
                                                $boton_inhabilitar
                                            </div>
                                        </td>
                                      </tr>";
                            }
                        } elseif (!$res) {
                            echo "<tr><td colspan='4' style='text-align:center; color:red;'>Error en Base de Datos: " . mysqli_error($conexion) . "</td></tr>";
                        } else {
                            echo "<tr><td colspan='4' style='text-align:center;'>No hay personal registrado aún.</td></tr>";
                        }
                        ?>
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