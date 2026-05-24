<?php
require_once '../../includes/auth.php';

// Reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Lógica de Filtros para Reporte EPI-10
$filtro = isset($_GET['periodo']) ? $_GET['periodo'] : 'semanal';
$titulo_sub = "Libro de Registro Semanal de Pacientes (EPI-10)";

if ($filtro == 'semanal') {
    $inicio = date('d/m/Y', strtotime('monday this week'));
    $fin = date('d/m/Y', strtotime('sunday this week'));
    $titulo_sub = "Registro Semanal (Del $inicio al $fin)";
} elseif ($filtro == 'mes') {
    $meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    $nombre_mes = $meses[date('n')-1];
    $titulo_sub = "Consolidado Mensual: $nombre_mes " . date('Y');
} elseif ($filtro == 'año') {
    $titulo_sub = "Consolidado Anual: " . date('Y');
}

$consultaCtrl = new ConsultaController($conexion);
$resultado = $consultaCtrl->listar($filtro);

include '../../includes/layout_header.php';
?>
    <div class="btn-container-sarce no-print controles-reporte reporte-print" style="margin-bottom: 30px; justify-content: center; gap: 15px; display: flex;">
        <a href="?periodo=semanal" class="btn-sarce">SEMANAL</a>
        <a href="?periodo=mes" class="btn-sarce">MENSUAL</a>
        <a href="?periodo=año" class="btn-sarce">ANUAL</a>
        <button onclick="window.print()" class="btn-sarce" style="background-color: #28a745;">
            <i class="fas fa-print"></i> IMPRIMIR
        </button>
    </div>

        <div class="reporte-papel">
            <div class="header-reporte">
                <div class="header-text"> 
                    <img src="<?php echo IMG_URL; ?>logosamulatorio.jpeg" alt="Logo Institucional" style="width: 100%; max-height: 120px; object-fit: contain; margin-bottom: 15px;"> 
                    <h1>REPÚBLICA BOLIVARIANA DE VENEZUELA</h1>
                    <h2>MINISTERIO DEL PODER POPULAR PARA LA SALUD</h2>
                    <h3><i class="fas fa-microscope"></i> <?php echo $titulo_sub; ?></h3>
                </div>
            </div>

            <div class="info-oficial">
                <span>ESTADO: MÉRIDA</span>
                <span>MUNICIPIO: CAMPO ELÍAS</span>
                <span>ESTABLECIMIENTO: AMBULATORIO RURAL II JAJÍ</span>
            </div>

            <div style="text-align: right; font-size: 11px; margin-bottom: 5px;">
                <strong>FECHA DE EMISIÓN:</strong> <?php echo date("d/m/Y h:i A"); ?>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Cédula</th>
                        <th>Paciente</th>
                        <th>Sex</th>
                        <th>T/A</th>
                        <th>Peso</th>
                        <th>Talla</th>
                        <th>Tem</th>
                        <th>Tratamiento / Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(mysqli_num_rows($resultado) > 0) {
                        while($fila = mysqli_fetch_assoc($resultado)) { 
                    ?>
                    <tr>
                        <td><?php echo date("d/m/y", strtotime($fila['fecha'])); ?></td>
                        <td><?php echo number_format($fila['cedula'], 0, ',', '.'); ?></td>
                        <td><strong><?php echo strtoupper($fila['nombre']." ".$fila['apellido']); ?></strong></td>
                        <td style="text-align: center;"><?php echo substr($fila['genero'], 0, 1); ?></td>
                        <td style="text-align: center;"><?php echo !empty($fila['tension']) ? $fila['tension'] : '---'; ?></td>
                        <td style="text-align: center;"><?php echo !empty($fila['peso']) ? $fila['peso'] . " kg" : '---'; ?></td>
                        <td style="text-align: center;"><?php echo !empty($fila['talla']) ? $fila['talla'] . " cm" : '---'; ?></td>
                        <td style="text-align: center;"><?php echo !empty($fila['temperatura']) ? $fila['temperatura'] . "°C" : '---'; ?></td>
                        <td>
                            <?php 
                                echo $fila['tratamiento']; 
                                // Si se entregaron medicamentos, los reflejamos aquí para mayor claridad
                                if(!empty($fila['medicamentos_entregados'])) {
                                    echo "<br><small style='color: #28a745;'><b>MED:</b> " . strtoupper($fila['medicamentos_entregados']) . "</small>";
                                }
                            ?>
                        </td>
                    </tr>
                    <?php 
                        } 
                    } else {
                        echo "<tr><td colspan='8' style='text-align:center;'>No hay registros para mostrar.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <div style="margin-top: 80px; display: flex; justify-content: space-around;">
                <div style="text-align: center; border-top: 1px solid #000; width: 220px; padding-top: 10px; font-size: 13px;">
                    Firma del Médico / Licenciada
                </div>
                <div style="text-align: center; border-top: 1px solid #000; width: 220px; padding-top: 10px; font-size: 13px;">
                    Sello del Ambulatorio Rural II
                </div>
            </div>
        </div>

    <footer class="footer-sarce-principal no-print">
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