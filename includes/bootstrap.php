<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Configuración de rutas y Base de Datos
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

// 2. Configuración regional
date_default_timezone_set('America/Caracas');

// 3. Autoloader de Clases MVC
spl_autoload_register(function ($className) {
    $baseDir = __DIR__ . '/classes/';
    $folders = ['Models/', 'Controllers/'];

    foreach ($folders as $folder) {
        $file = $baseDir . $folder . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// 4. Inicialización de controlador base para uso global (como Auth)
$authCtrl = new AuthController($conexion);