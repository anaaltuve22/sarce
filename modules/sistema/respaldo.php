<?php
require_once '../../includes/auth.php';
// Solo el admin puede respaldar
if (!isset($_SESSION['admin']) || $_SESSION['rol'] !== 'admin') { header("Location: " . BASE_URL . "inicio.php"); exit(); }

/* El SistemaController maneja la lógica de generación, bitácora y descarga */
$sistemaCtrl = new SistemaController($conexion);
$sistemaCtrl->respaldar();
?>
