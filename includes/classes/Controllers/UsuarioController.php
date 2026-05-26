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
                'p1' => $userData['pregunta_1'] ?? '', 
                'p2' => $userData['pregunta_2'] ?? '', 
                'p3' => $userData['pregunta_3'] ?? ''
            ];
        }
        return null;
    }

    public function obtenerBitacora($busqueda = null) {
        return $this->model->getBitacora($busqueda);
    }

    public function iniciarRecuperacion($identificador) {
        $userData = $this->model->getSecurityQuestionsByIdentifier($identificador);
        if ($userData) {
            return [
                'status' => 'success', 
                'user_id' => $userData['id'], 
                'p1' => $userData['pregunta_1'] ?? '', 
                'p2' => $userData['pregunta_2'] ?? '', 
                'p3' => $userData['pregunta_3'] ?? ''
            ];
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
        // Mapeo de campos de seguridad
        $mapeo = [
            'p1' => 'pregunta_1', 'r1' => 'respuesta_1',
            'p2' => 'pregunta_2', 'r2' => 'respuesta_2',
            'p3' => 'pregunta_3', 'r3' => 'respuesta_3'
        ];
        foreach ($mapeo as $vista => $db) {
            if (isset($datos[$vista])) {
                $datos[$db] = $datos[$vista];
                unset($datos[$vista]);
            }
        }
        unset($datos['registrar_personal']);

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

        // Validación obligatoria de preguntas de seguridad
        for ($i = 1; $i <= 3; $i++) {
            $p_col = "pregunta_$i";
            $r_col = "respuesta_$i";
            if (empty($datos[$p_col]) || trim($datos[$p_col]) === '' || empty($datos[$r_col])) {
                return ['status' => 'error', 'msg' => 'Debe completar todas las preguntas y respuestas de seguridad.'];
            }
        }

        if ($datos['clave'] !== ($datos['confirmar_clave'] ?? '')) {
            return ['status' => 'error', 'msg' => 'Las contraseñas no coinciden.'];
        }

        if ($this->model->getByLogin($datos['usuario']) || $this->model->getByLogin($datos['correo'])) {
            return ['status' => 'error', 'msg' => 'El nombre de usuario o el correo electrónico ya están registrados.'];
        }

        $datos['clave'] = password_hash($datos['clave'], PASSWORD_DEFAULT);

        // Forzar que el usuario se cree con estado Activo (1)
        $datos['estado'] = 1;

        if ($this->model->insertar($datos)) {
            $this->registrarBitacora("Nuevo usuario registrado: {$datos['usuario']} (Rol: {$datos['rol']})");
            return ['status' => 'success', 'msg' => 'La cuenta de personal ha sido creada con éxito.'];
        }
        return ['status' => 'error', 'msg' => 'Error al registrar el usuario en la base de datos.'];
    }

    public function listar($busqueda = null) {
        $busqueda = trim($busqueda ?? '');
        if (!empty($busqueda)) {
            return $this->model->buscar($busqueda);
        }
        return $this->model->getAll();
    }

    public function actualizar($id, $datos, $esAdmin = false) {
        // 1. Limpieza estricta de campos que no pertenecen a la tabla usuarios
        unset($datos['guardar'], $datos['id_usuario_rel'], $datos['actualizar_perfil']);

        // 2. Procesamiento de preguntas de seguridad
        for ($i = 1; $i <= 3; $i++) {
            $p_key = "p$i";
            $r_key = "r$i";
            // Solo actualizamos la pregunta si el usuario proporcionó una respuesta
            if (!empty($datos[$r_key])) {
                $datos["pregunta_$i"] = $datos[$p_key];
                $datos["respuesta_$i"] = $datos[$r_key];
            }
            unset($datos[$p_key], $datos[$r_key]);
        }

        // Validaciones de negocio: Nombre y Apellido
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
            return ['status' => 'error', 'msg' => 'Formato de correo electrónico inválido.'];
        }

        // Procesar contraseña si se envió una nueva
        if (!empty($datos['clave'])) {
            if ($datos['clave'] !== ($datos['confirmar_clave'] ?? '')) {
                return ['status' => 'error', 'msg' => 'Las contraseñas no coinciden.'];
            }
            $datos['clave'] = password_hash($datos['clave'], PASSWORD_DEFAULT);
        } else {
            unset($datos['clave']);
        }
        unset($datos['confirmar_clave']);

        // Seguridad: Si no es admin, impedimos que se modifique el rol o el nombre de usuario
        if (!$esAdmin) {
            unset($datos['rol']);
            unset($datos['usuario']);
        }

        // 3. LLAMADA CRÍTICA: Se pasan (datos, id) para evitar error 500 en el modelo
        if ($this->model->actualizar($datos, $id)) {
            $this->registrarBitacora("Usuario actualizado (ID: $id)");
            return ['status' => 'success', 'msg' => 'Los cambios han sido guardados correctamente.'];
        }
        return ['status' => 'error', 'msg' => 'No se realizaron cambios o error en la base de datos.'];
    }

    public function cambiarEstado($id, $estado) {
        if ($this->model->setEstado($id, $estado)) {
            $accion = $estado ? "habilitado" : "inhabilitado";
            $this->registrarBitacora("Usuario $accion (ID: $id)");
            return ['status' => 'success', 'msg' => "Usuario $accion correctamente."];
        }
        return ['status' => 'error', 'msg' => 'Error al cambiar el estado.'];
    }

    public function reiniciarClave($id, $nuevaClave) {
        $hash = password_hash($nuevaClave, PASSWORD_DEFAULT);
        return $this->model->updatePassword($id, $hash);
    }
}