<?php
require_once 'BaseController.php';

class AuthController extends BaseController {
    
    public function login($identificador, $clave) {
        // Solo permite usuario (no correo) y respeta longitudes 20/12
        if (filter_var($identificador, FILTER_VALIDATE_EMAIL) || strlen($identificador) > 20 || strlen($clave) > 12 || empty($identificador) || empty($clave)) {
            return false;
        }

        $usuario = $this->usuarioModel->getByLogin($identificador);
        
        // Soporte para claves en texto plano (actual) y hash (futuro)
        if ($usuario && ($clave === $usuario['clave'] || password_verify($clave, $usuario['clave']))) {
            
            // Solo permitir el acceso si el estado es exactamente 1 (Activo)
            // Si no está definido o es distinto de 1, se considera inactivo
            if (!isset($usuario['estado']) || (int)$usuario['estado'] !== 1) {
                $this->registrarBitacora("Intento de acceso denegado: Cuenta inhabilitada ({$usuario['usuario']})");
                return "inactivo";
            }
            
            // Control de sesión única: Validar si existe sesión activa hace menos de 10 min
            if (!empty($usuario['session_id_activa']) && !empty($usuario['ultima_actividad'])) {
                $ultimo_clic = strtotime($usuario['ultima_actividad']);
                if ((time() - $ultimo_clic) < 600 && $usuario['session_id_activa'] !== session_id()) {
                    return "sesion_activa";
                }
            }

            $_SESSION['admin'] = $usuario['usuario'];
            $_SESSION['rol'] = $usuario['rol'];
            $_SESSION['id_usuario_rel'] = $usuario['id'];
            $_SESSION['ultimo_acceso'] = time();

            // Registrar rastro en DB
            $this->usuarioModel->actualizar([
                'session_id_activa' => session_id(),
                'ultima_actividad' => date("Y-m-d H:i:s")
            ], $usuario['id']);
            
            $this->registrarBitacora("Inicio de sesión exitoso");
            return true;
        }
        return false;
    }

    public function logout() {
        if (isset($_SESSION['id_usuario_rel'])) {
            // Limpiar datos de sesión en DB
            $this->usuarioModel->actualizar([
                'session_id_activa' => null,
                'ultima_actividad' => null
            ], $_SESSION['id_usuario_rel']);
        }
        $this->registrarBitacora("Cierre de sesión manual");
        $this->destruirSesionCompleta();
    }

    public function checkInactivity($timeout = 600) {
        if (isset($_SESSION['ultimo_acceso'])) {
            if (time() - $_SESSION['ultimo_acceso'] > $timeout) {
                $this->registrarBitacora("Cierre de sesión por inactividad");
                $this->destruirSesionCompleta();
                return true;
            }
            // Mantener la sesión "viva" en la DB
            if (isset($_SESSION['id_usuario_rel'])) {
                $this->usuarioModel->actualizar([
                    'ultima_actividad' => date("Y-m-d H:i:s")
                ], $_SESSION['id_usuario_rel']);
            }
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