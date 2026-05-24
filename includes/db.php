<?php
// Cargar configuración global de rutas
require_once __DIR__ . '/config.php';

$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "sarce_jaji";
$conexion = mysqli_connect($host, $user, $pass, $db);
if (!$conexion) { die("Error: " . mysqli_connect_error()); }
mysqli_set_charset($conexion, "utf8");