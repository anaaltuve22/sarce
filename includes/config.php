<?php
// Configuración Global de Rutas
define('BASE_URL', '/sarce/');

// Rutas de Assets (CSS, JS, Imágenes)
define('ASSETS_URL', BASE_URL . 'assets/');
define('IMG_URL', ASSETS_URL . 'img/');

// Rutas de Módulos (Facilita la navegación entre carpetas)
define('MOD_PACIENTES', BASE_URL . 'modules/pacientes/');
define('MOD_PERSONAL', BASE_URL . 'modules/personal/');
define('MOD_MEDICAMENTOS', BASE_URL . 'modules/medicamentos/');
define('MOD_CONSULTAS', BASE_URL . 'modules/consultas/');
define('MOD_SISTEMA', BASE_URL . 'modules/sistema/');
?>