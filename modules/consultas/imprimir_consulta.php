<?php
require_once '../../includes/auth.php';

// Configuración de errores para diagnóstico
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['id'])) {
    $id_consulta = $_GET['id'];
    $consultaCtrl = new ConsultaController($conexion);
    $datos = $consultaCtrl->obtenerDetalles($id_consulta);

    if (!$datos) { die("Error: No se encontró la consulta #" . htmlspecialchars($id_consulta)); }
} else {
    die("Error: ID no recibido.");
}

$pageTitle = "Ticket SARCE #$id_consulta";
include '../../includes/layout_header.php';
?>
<div class="body-impresion-ticket" style="background: transparent; padding: 0; min-height: auto;">

<div class="ticket-card">
    <div class="ticket-header">
                    <img src="<?php echo IMG_URL; ?>logosamulatorio.jpeg" alt="Logo" style="max-height: 60px; margin-bottom: 10px; border-radius: 5px;">
                    <h3>AMBULATORIO RURAL II JAJÍ</h3>
                    <h3>MUNICIPIO CAMPO ELÍAS - ESTADO MÉRIDA</h3>
                    <h3>COMPROBANTE DE CONSULTA</h3>
        <p style="margin:0; font-size:11px;"> Sistema SARCE</p>
    </div>

    <div class="ticket-content">
        <!-- 1. IDENTIFICACIÓN DEL PACIENTE -->
        <div class="info-grid">
            <div class="info-item"><b>Cédula</b> <span>V-<?php echo $datos['pac_ced']; ?></span></div>
            <div class="info-item"><b>Fecha</b> <span><?php echo date("d/m/Y", strtotime($datos['fecha'])); ?></span></div>
            <div class="info-item" style="grid-column: span 2;"><b>Paciente</b> <span><?php echo strtoupper($datos['pac_nom'] . " " . $datos['pac_ape']); ?></span></div>
        </div>

        <!-- 2. SIGNOS VITALES -->
        <div class="vitals-grid" style="margin-top: 10px; background: #f8fafc; padding: 12px; border-radius: 10px;">
            <div class="info-item"><b>T/A</b> <span><?php echo !empty($datos['tension']) ? $datos['tension'] : '---'; ?></span></div>
            <div class="info-item"><b>Peso</b> <span><?php echo !empty($datos['peso']) ? $datos['peso']." kg" : '---'; ?></span></div>
            <div class="info-item"><b>Talla</b> <span><?php echo !empty($datos['talla']) ? $datos['talla']." cm" : '---'; ?></span></div>
            <div class="info-item"><b>Tem</b> <span><?php echo !empty($datos['temperatura']) ? $datos['temperatura']."°C" : '---'; ?></span></div>
        </div>

        <!-- 3. DIAGNÓSTICO Y TRATAMIENTO -->
        <div class="detail-box">
            <h4><i class="fas fa-file-medical"></i> Diagnóstico</h4>
            <p>
                <?php 
                // Si existe un nombre de patología, lo mostramos. Si no, mostramos el diagnóstico manual (compatibilidad)
                if(!empty($datos['nombre_patologia'])) {
                    echo strtoupper($datos['nombre_patologia']);
                } else {
                    echo nl2br($datos['motivo']); 
                }
                ?>
            </p>
        </div>

        <div class="detail-box">
            <h4><i class="fas fa-prescription"></i> Tratamiento</h4>
            <p><?php echo nl2br($datos['tratamiento']); ?></p>
        </div>

        <!-- 4. MEDICAMENTOS ENTREGADOS -->
        <div class="detail-box">
            <h4><i class="fas fa-pills"></i> Entrega de medicamentos</h4>
            <p style="background: transparent; border-style: none; padding: 0; font-weight: bold; color: #000;">
                <?php echo (!empty($datos['medicamentos_entregados'])) ? strtoupper($datos['medicamentos_entregados']) : "NINGUNA"; ?>
            </p>
        </div>

        <!-- 5. FIRMA Y SELLO -->
        <div class="seccion-sellos">
            <div>
                <div class="cuadro-sello">Espacio para Sello Húmedo</div>
                <div class="etiqueta-sello">SELLO DEL AMBULATORIO</div>
            </div>
            <div>
                <div class="cuadro-sello" style="border-style: solid; border-width: 1px; color: #4a5568; font-size: 10px; flex-direction: column;">
                    <br><br>
                    <span>_______________________</span>
                    <span>Firma Autorizada</span>
                </div>
                <div class="etiqueta-sello">
                    <?php echo !empty($datos['doc_nom']) ? "DR(A). ".strtoupper($datos['doc_nom']." ".$datos['doc_ape']) : "FIRMA DEL MÉDICO"; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="ticket-footer">
        <b>COMPROBANTE DE CONSULTA #<?php echo $id_consulta; ?></b><br>
        Generado el <?php echo date("d/m/Y h:i A"); ?>
    </div>
</div>

<div class="btn-area">
    <button onclick="window.print()" class="btn-ticket btn-print"><i class="fas fa-print"></i> IMPRIMIR TICKET</button>

</div>

</div>
<?php include '../../includes/layout_footer.php'; ?>