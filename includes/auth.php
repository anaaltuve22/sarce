<?php
// 1. Cargar inicialización central
require_once __DIR__ . '/bootstrap.php';

// 2. Cabeceras de seguridad para evitar caché
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// 4. Verificación de sesión
if (!isset($_SESSION['admin'])) {
    header("Location: " . BASE_URL . "login.php");
    exit();
}

// 5. Control de inactividad (10 minutos)
if ($authCtrl->checkInactivity(600)) {
    header("Location: " . BASE_URL . "login.php?timeout=1");
    exit();
}