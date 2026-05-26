<?php
require_once 'BaseController.php';

class AuthController extends BaseController {
    
    public function login($identificador, $clave) {
        $usuario = $this->usuarioModel->getByLogin($identificador);
        
        // Soporte para claves en texto plano (actual) y hash (futuro)
        if ($usuario && ($clave === $usuario['clave'] || password_verify($clave, $usuario['clave']))) {
            
            // Solo permitir el acceso si el estado es exactamente 1 (Activo)
            // Si no está definido o es distinto de 1, se considera inactivo
            if (!isset($usuario['estado']) || (int)$usuario['estado'] !== 1) {
                $this->registrarBitacora("Intento de acceso denegado: Cuenta inhabilitada ({$usuario['usuario']})");
                return "inactivo";
            }

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
        $this->destruirSesionCompleta();
    }

    public function checkInactivity($timeout = 600) {
        if (isset($_SESSION['ultimo_acceso']) && (time() - $_SESSION['ultimo_acceso'] > $timeout)) {
            $this->registrarBitacora("Cierre de sesión por inactividad");
            $this->destruirSesionCompleta();
            return true;
        }
        $_SESSION['ultimo_acceso'] = time();
        return false;
    }

    /**
     * Limpia las variables de sesión, destruye la cookie en el navegador 
     * y finaliza la sesión en el servidor.
     */
    private function destruirSesionCompleta() {
        // 1. Limpiar el array de sesión
        session_unset();
        $_SESSION = array();

        // 2. Borrar la cookie de sesión en el navegador
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // 3. Destruir la sesión en el servidor
        session_destroy();
    }
}