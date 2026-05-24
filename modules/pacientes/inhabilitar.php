<?php
require_once '../../includes/auth.php';

// Seguridad: Solo admin puede inhabilitar
if (!isset($_SESSION['admin']) || $_SESSION['rol'] !== 'admin') {
    header("Location: " . BASE_URL . "login.php");
    exit();
}

if (isset($_GET['cedula'])) {
    $pacienteCtrl = new PacienteController($conexion);
    $pacienteCtrl->inhabilitar($_GET['cedula']);
    header("Location: " . MOD_PACIENTES . "lista_de_pacientes.php");
}
?>