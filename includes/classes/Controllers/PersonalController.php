<?php
require_once 'BaseController.php';

class PersonalController extends BaseController {
    private $model;

    public function __construct($conexion) {
        parent::__construct($conexion);
        $this->model = new PersonalModel($conexion);
    }

    public function listar($busqueda = null) {
        $busqueda = trim($busqueda ?? '');
        if (!empty($busqueda)) {
            return $this->model->buscar($busqueda);
        }
        return $this->model->listarActivos();
    }

    public function obtenerPorCedula($cedula) {
        return $this->model->getByCedula($cedula);
    }

    public function obtenerCedulaPorUsuario($id_usuario) {
        $data = $this->model->getByUsuarioId($id_usuario);
        return $data ? $data['cedula'] : null;
    }

    public function registrar($datos) {
        if (strlen($datos['nombre']) > 25 || strlen($datos['apellido']) > 25) {
            return ['status' => 'error', 'msg' => 'Nombre o apellido demasiado largo.'];
        }
        if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $datos['nombre'])) {
            return ['status' => 'error', 'msg' => 'El nombre solo puede contener letras.'];
        }
        if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $datos['apellido'])) {
            return ['status' => 'error', 'msg' => 'El apellido solo puede contener letras.'];
        }

        if ($this->model->insertar($datos)) {
            $this->registrarBitacora("Personal médico registrado: {$datos['nombre']} {$datos['apellido']}");
            return ['status' => 'success', 'msg' => 'Personal registrado con éxito.'];
        }
        return ['status' => 'error', 'msg' => 'Error al registrar personal.'];
    }

    public function actualizar($datos, $cedula_vieja) {
        if (strlen($datos['nombre']) > 25 || strlen($datos['apellido']) > 25) {
            return ['status' => 'error', 'msg' => 'Nombre o apellido demasiado largo (Máximo 25 caracteres).'];
        }
        if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $datos['nombre'])) {
            return ['status' => 'error', 'msg' => 'El nombre solo puede contener letras.'];
        }
        if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $datos['apellido'])) {
            return ['status' => 'error', 'msg' => 'El apellido solo puede contener letras.'];
        }

        if ($this->model->actualizar($datos, $cedula_vieja)) {
            $this->registrarBitacora("Datos de personal actualizados (C.I: $cedula_vieja)");
            return ['status' => 'success', 'msg' => 'Personal actualizado.'];
        }
        return ['status' => 'error', 'msg' => 'Error al actualizar.'];
    }

    public function inhabilitar($cedula) {
        if ($this->model->setEstado($cedula, 0)) {
            $this->registrarBitacora("Personal médico inhabilitado (C.I: $cedula)");
            return true;
        }
        return false;
    }
}