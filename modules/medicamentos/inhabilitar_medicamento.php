<?php
require_once '../../includes/auth.php';

if ($_SESSION['rol'] !== 'admin') { header("Location: " . MOD_MEDICAMENTOS . "medicamentos.php"); exit(); }

if (isset($_GET['id'])) {
    $medCtrl = new MedicamentoController($conexion);
    $medCtrl->inhabilitar($_GET['id']);
    header("Location: " . MOD_MEDICAMENTOS . "medicamentos.php");
}
?>