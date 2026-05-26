<?php
require_once '../../includes/auth.php';

// 1. Lógica de Filtros (Basada en la fecha de la consulta)
$filtro = isset($_GET['periodo']) ? $_GET['periodo'] : 'todos';
$info_periodo = "Resultados Totales (Histórico)";

if ($filtro == 'semanal') {
    $inicio = date('d/m/Y', strtotime('monday this week'));
    $fin = date('d/m/Y', strtotime('sunday this week'));
    $info_periodo = "Semana Actual: del $inicio al $fin";
} elseif ($filtro == 'mes') {
    $meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    $nombre_mes = $meses[date('n')-1];
    $info_periodo = "Consolidado de $nombre_mes " . date('Y');
} elseif ($filtro == 'año') {
    $info_periodo = "Reporte Anual: " . date('Y');
}

$consultaCtrl = new ConsultaController($conexion);
$res_stats = $consultaCtrl->obtenerTopPatologias($filtro);

$nombres = [];
$cantidades = [];

while($fila = mysqli_fetch_assoc($res_stats)) {
    $nombres[] = $fila['nombre_patologia'];
    $cantidades[] = $fila['total'];
}

$pageTitle = "Tablero Epidemiológico | SARCE";
include '../../includes/layout_header.php';
?>
<div class="container">
    <div class="header-tablero">
        <div>
            <h3 class="text-navy" style="margin:0;"><?php echo htmlspecialchars($info_periodo); ?></h3>
        </div>
        
        <div class="flex-gap-15">
            <div class="bg-input-group">
                <a href="?periodo=semanal" class="btn-filtro <?php echo $filtro=='semanal'?'active':''; ?>">Semana</a>
                <a href="?periodo=mes" class="btn-filtro <?php echo $filtro=='mes'?'active':''; ?>">Mes</a>
                <a href="?periodo=año" class="btn-filtro <?php echo $filtro=='año'?'active':''; ?>">Año</a>
                <a href="?periodo=todos" class="btn-filtro <?php echo $filtro=='todos'?'active':''; ?>">Todo</a>
            </div>
            
            <a href="reporte.php?periodo=<?php echo $filtro; ?>" class="btn-exportar" style="text-decoration: none;">
                <i class="fas fa-file-medical"></i> VER EPI-10
            </a>
            <button onclick="window.print()" class="btn-exportar" style="background-color: #2d3748;">
                <i class="fas fa-print"></i> IMPRIMIR VISTA
            </button>
        </div>
    </div>

    <div class="grid-stats">
        <div class="card-grafico">
            <h4 style="margin-top:0; color: #002347;">Distribución de Patologías (Top 5)</h4>
            <div style="max-width: 320px; margin: 0 auto;">
                <canvas id="graficoMorbilidad"></canvas>
            </div>
        </div>

        <div class="tabla-frecuencia" style="overflow-x: auto;">
            <h4 style="margin-top:0; color: #002347;">Frecuencia de Casos</h4>
            <table>
                <thead>
                    <tr>
                        <th>Patología</th>
                        <th>Casos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    mysqli_data_seek($res_stats, 0); 
                    while($f = mysqli_fetch_assoc($res_stats)): ?>
                    <tr>
                        <td><?php echo $f['nombre_patologia']; ?></td>
                        <td style="text-align: center;"><span class="badge-cantidad"><?php echo $f['total']; ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    window.onload = () => {
        renderGraficoMorbilidad(<?php echo json_encode($nombres); ?>, <?php echo json_encode($cantidades); ?>);
    };
</script>
<?php include '../../includes/layout_footer.php'; ?>