<?php
require_once '../../includes/auth.php';

// Seguridad: Solo admin puede inhabilitar personal
if (!isset($_SESSION['admin']) || $_SESSION['rol'] !== 'admin') {
    header("Location: " . BASE_URL . "login.php");
    exit();
}

if (isset($_GET['cedula'])) {
    $personalCtrl = new PersonalController($conexion);
    $personalCtrl->inhabilitar($_GET['cedula']);
    header("Location: " . MOD_PERSONAL . "personal.php");
    exit();
}
?>