<?php
require_once 'BaseController.php';

class UsuarioController extends BaseController {
    private $model;

    public function __construct($conexion) {
        parent::__construct($conexion);
        $this->model = new UsuarioModel($conexion);
    }

    public function obtenerPorId($id) {
        return $this->model->getById($id);
    }

    /**
     * Obtiene específicamente las preguntas de seguridad de un usuario por su ID
     */
    public function obtenerPreguntasPorId($userId) {
        $userData = $this->model->getSecurityQuestionsById($userId);
        if ($userData) {
            return [
                'p1' => $userData['pregunta_1'], 
                'p2' => $userData['pregunta_2'], 
                'p3' => $userData['pregunta_3']
            ];
        }
        return null;
    }

    public function obtenerBitacora() {
        return $this->model->getBitacora();
    }

    public function iniciarRecuperacion($identificador) {
        $userData = $this->model->getSecurityQuestionsByIdentifier($identificador);
        if ($userData) {
            return ['status' => 'success', 'user_id' => $userData['id'], 'p1' => $userData['pregunta_1'], 'p2' => $userData['pregunta_2'], 'p3' => $userData['pregunta_3']];
        }
        return ['status' => 'error', 'msg' => 'Usuario o correo no encontrado.'];
    }

    public function verificarRespuestas($userId, $r1_u, $r2_u, $r3_u) {
        $userData = $this->model->getSecurityQuestionsById($userId);
        if ($userData &&
            strcasecmp($userData['respuesta_1'], $r1_u) == 0 &&
            strcasecmp($userData['respuesta_2'], $r2_u) == 0 &&
            strcasecmp($userData['respuesta_3'], $r3_u) == 0) {
            return ['status' => 'success'];
        }
        return ['status' => 'error', 'msg' => 'Una o más respuestas son incorrectas.'];
    }

    public function restablecerClave($userId, $nuevaClave, $confirmarClave) {
        if (strlen($nuevaClave) < 8) {
            return ['status' => 'error', 'msg' => 'La contraseña debe tener al menos 8 caracteres.'];
        }
        if ($nuevaClave !== $confirmarClave) {
            return ['status' => 'error', 'msg' => 'Las contraseñas no coinciden.'];
        }
        $clave_hash = password_hash($nuevaClave, PASSWORD_DEFAULT);
        if ($this->model->updatePassword($userId, $clave_hash)) {
            $this->registrarBitacora("Cambio de contraseña por preguntas de seguridad para usuario ID: $userId");
            return ['status' => 'success', 'msg' => '¡Contraseña actualizada con éxito! Ya puede iniciar sesión.'];
        }
        return ['status' => 'error', 'msg' => 'Error al actualizar la contraseña.'];
    }

    public function registrar($datos) {
        // Validaciones de negocio
        if (strlen($datos['nombre']) > 25 || strlen($datos['apellido']) > 25) {
            return ['status' => 'error', 'msg' => 'El nombre y apellido no deben exceder los 25 caracteres cada uno.'];
        }
        if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $datos['nombre'])) {
            return ['status' => 'error', 'msg' => 'El nombre solo puede contener letras y espacios.'];
        }
        if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $datos['apellido'])) {
            return ['status' => 'error', 'msg' => 'El apellido solo puede contener letras y espacios.'];
        }
        if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'error', 'msg' => 'El formato del correo electrónico no es correcto.'];
        }
        if (strlen($datos['usuario']) < 4 || strlen($datos['usuario']) > 50) {
            return ['status' => 'error', 'msg' => 'El nombre de usuario debe tener entre 4 y 50 caracteres.'];
        }
        if (strlen($datos['clave']) < 8) {
            return ['status' => 'error', 'msg' => 'La contraseña debe tener al menos 8 caracteres.'];
        }

        if ($this->model->getByLogin($datos['usuario']) || $this->model->getByLogin($datos['correo'])) {
            return ['status' => 'error', 'msg' => 'El nombre de usuario o el correo electrónico ya están registrados.'];
        }

        $datos['clave'] = password_hash($datos['clave'], PASSWORD_DEFAULT);

        if ($this->model->insertar($datos)) {
            $this->registrarBitacora("Nuevo usuario registrado: {$datos['usuario']} (Rol: {$datos['rol']})");
            return ['status' => 'success', 'msg' => 'La cuenta de personal ha sido creada con éxito.'];
        }
        return ['status' => 'error', 'msg' => 'Error al registrar el usuario en la base de datos.'];
    }
}