<?php
require_once '../../includes/auth.php';
$pageTitle = "Gestión de Medicamentos | SARCE";
include '../../includes/layout_header.php';
?>

<div class="header-modulo-meds">
    <h2><i class="fas fa-pills"></i> Lista de Medicamentos</h2>
    <a href="<?php echo MOD_MEDICAMENTOS; ?>registrar_medicamento.php" class="btn-original btn-atender">
        <i class="fas fa-plus"></i> AGREGAR MEDICAMENTO
    </a>
</div>

<div class="container-tabla">
    <div style="position: relative;">
        <i class="fas fa-search" style="position: absolute; left: 15px; top: 15px; color: #cbd5e0;"></i>
        <input type="text" id="buscador" class="search-box" placeholder="Buscar medicamento...">
    </div>

    <table>
        <thead>
            <tr>
                <th>Nombre del Medicamento</th>
                <th>Descripción / Presentación</th>
                <th style="text-align: center;">Acciones</th>
            </tr>
        </thead>
        <tbody id="cuerpoTabla">
            <?php
            $medCtrl = new MedicamentoController($conexion);
            $res = $medCtrl->listar();
            if ($res && mysqli_num_rows($res) > 0) {
                while($m = mysqli_fetch_assoc($res)) {
                    $boton_inhabilitar = "";
                    if ($_SESSION['rol'] === 'admin') {
                        $boton_inhabilitar = "
                            <a href='javascript:void(0);' class='btn-original btn-inhabilitar' style='padding: 6px 12px; font-size: 12px;' onclick=\"confirmarInhabilitacion('{$m['id']}', 'medicamento')\">
                                <i class='fas fa-trash-alt'></i> INHABILITAR
                            </a>";
                    }
                    echo "<tr>
                            <td><b style='color:#002347;'>" . htmlspecialchars(strtoupper($m['nombre'])) . "</b></td>
                            <td>" . htmlspecialchars($m['descripcion']) . "</td>
                            <td class='td-acciones'>
                                <div class='flex-acciones'>
                                    <a href='" . MOD_MEDICAMENTOS . "editar_medicamento.php?id={$m['id']}' class='btn-original btn-editar' style='padding: 6px 12px; font-size: 12px;'>
                                        <i class='fas fa-edit'></i> EDITAR
                                    </a>
                                    $boton_inhabilitar
                                </div>
                            </td>
                          </tr>";
                }
            } elseif ($res === false) {
                echo "<tr><td colspan='4' style='text-align:center; color:red;'>Error en la consulta: " . htmlspecialchars(mysqli_error($conexion)) . "</td></tr>";
            } else {
                echo "<tr><td colspan='4' style='text-align:center;'>No hay medicamentos registrados.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include '../../includes/layout_footer.php'; ?>