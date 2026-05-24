<?php
require_once '../../includes/auth.php';

$consultaCtrl = new ConsultaController($conexion);
$resultado = $consultaCtrl->listarConsultas();

$pageTitle = "Listado de Consultas | SARCE";
include '../../includes/layout_header.php';
?>
<body class="bg-gradient-list">
<div class="contenedor" style="margin-top: 20px;">
    <h2 style="color: #333; border-bottom: 2px solid #28a745; padding-bottom: 10px;">[3.3] Listado General de Consultas Médicas</h2>
    <div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th class="header-verde">Fecha</th>
                <th class="header-verde">Cédula</th>
                <th class="header-verde">Paciente</th>
                <th>T/A</th>
                <th>Peso (kg)</th>
                <th>Tem (°C)</th>
                <th>Tratamiento / Observación</th>
            </tr>
        </thead>
        <tbody>
            <?php if(mysqli_num_rows($resultado) > 0): ?>
                <?php while($fila = mysqli_fetch_assoc($resultado)): ?>
                <tr>
                    <td><span class="badge-fecha"><?php echo date("d/m/Y", strtotime($fila['fecha'])); ?></span></td>
                    <td><?php echo $fila['cedula']; ?></td>
                    <td><?php echo strtoupper($fila['apellido']) . ", " . $fila['nombre']; ?></td>
                    <td><?php echo $fila['tension']; ?></td>
                    <td><?php echo $fila['peso']; ?></td>
                    <td><?php echo $fila['temperatura']; ?></td>
                    <td><?php echo $fila['tratamiento']; ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" style="text-align:center; padding: 20px;">No hay consultas registradas todavía.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
<?php include '../../includes/layout_footer.php'; ?>
