<?php
require_once 'BaseController.php';

class AuthController extends BaseController {
    
    public function login($identificador, $clave) {
        $usuario = $this->usuarioModel->getByLogin($identificador);
        
        // Soporte para claves en texto plano (actual) y hash (futuro)
        if ($usuario && ($clave === $usuario['clave'] || password_verify($clave, $usuario['clave']))) {
            $_SESSION['admin'] = $usuario['usuario'];
            $_SESSION['rol'] = $usuario['rol'];
            $_SESSION['cedula_admin'] = $usuario['cedula'];
            $_SESSION['id_usuario_rel'] = $usuario['id'];
            $_SESSION['ultimo_acceso'] = time();
            
            $this->registrarBitacora("Inicio de sesión exitoso");
            return true;
        }
        return false;
    }

    public function logout() {
        $this->registrarBitacora("Cierre de sesión manual");
        session_unset();
        session_destroy();
    }

    public function checkInactivity($timeout = 600) {
        if (isset($_SESSION['ultimo_acceso']) && (time() - $_SESSION['ultimo_acceso'] > $timeout)) {
            $this->registrarBitacora("Cierre de sesión por inactividad");
            session_unset();
            session_destroy();
            return true;
        }
        $_SESSION['ultimo_acceso'] = time();
        return false;
    }
}