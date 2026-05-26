<?php
// 1. Configuración de rutas y Base de Datos
require_once __DIR__ . '/config.php';

// Configuración de seguridad para la cookie de sesión (Antes de session_start)
ini_set('session.cookie_httponly', 1); // Bloquea acceso a la cookie vía JS (evita XSS)
ini_set('session.use_only_cookies', 1); // Fuerza el uso de cookies, prohibiendo IDs en la URL
ini_set('session.cookie_samesite', 'Strict'); // Evita que la cookie se envíe en ataques CSRF

// Restringir la cookie al path definido en config.php
session_set_cookie_params([
    'path' => BASE_URL,
    'httponly' => true,
    'samesite' => 'Strict'
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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