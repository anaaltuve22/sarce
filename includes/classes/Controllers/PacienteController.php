<?php
require_once 'BaseController.php';

class PacienteController extends BaseController {
    private $model;

    public function __construct($conexion) {
        parent::__construct($conexion);
        $this->model = new PacienteModel($conexion);
    }

    public function listar($busqueda = null) {
        $busqueda = trim($busqueda);
        if (!empty($busqueda)) {
            return $this->model->buscar($busqueda);
        }
        return $this->model->listarActivos();
    }

    public function obtenerPorCedula($cedula) {
        return $this->model->getByCedula($cedula);
    }

    public function registrar($datos) {
        // Validaciones de negocio
        if ($datos['edad'] < 0) {
            return ['status' => 'error', 'msg' => 'no se puede agregar fechas futuras'];
        }
        if ($datos['edad'] > 100) {
            return ['status' => 'error', 'msg' => 'no se permite un numero mayor a este'];
        }

        if (strlen($datos['nombre']) > 25 || strlen($datos['apellido']) > 25) {
            return ['status' => 'error', 'msg' => 'El nombre y apellido no deben exceder los 25 caracteres cada uno.'];
        }

        if ($this->model->existeCedula($datos['cedula'])) {
            $paciente = $this->model->getByCedula($datos['cedula']);
            if ($paciente && $paciente['estado'] == 0) {
                return ['status' => 'info', 'msg' => 'El paciente ya está registrado pero se encuentra inhabilitado.'];
            }
            return ['status' => 'warning', 'msg' => 'La cédula ya se encuentra registrada en el sistema.'];
        }

        if ($this->model->insertar($datos)) {
            $this->registrarBitacora("Paciente registrado: {$datos['nombre']} {$datos['apellido']} (C.I: {$datos['cedula']})");
            return ['status' => 'success', 'msg' => 'Paciente registrado con éxito.'];
        }
        return ['status' => 'error', 'msg' => 'Error en la base de datos.'];
    }

    public function actualizar($datos, $cedula_vieja) {
        if ($datos['edad'] < 0) {
            return ['status' => 'error', 'msg' => 'no se puede agregar fechas futuras'];
        }
        if ($datos['edad'] > 100) {
            return ['status' => 'error', 'msg' => 'no se permite un numero mayor a este'];
        }

        if (strlen($datos['nombre']) > 25 || strlen($datos['apellido']) > 25) {
            return ['status' => 'error', 'msg' => 'El nombre y apellido no deben exceder los 25 caracteres cada uno.'];
        }

        if ($this->model->actualizar($datos, $cedula_vieja)) {
            $this->registrarBitacora("Datos de paciente actualizados (C.I: $cedula_vieja)");
            return ['status' => 'success', 'msg' => 'Datos actualizados con éxito.'];
        }
        return ['status' => 'error', 'msg' => 'Error al actualizar.'];
    }

    public function inhabilitar($cedula) {
        if ($this->model->setEstado($cedula, 0)) {
            $this->registrarBitacora("Paciente inhabilitado (C.I: $cedula)");
            return true;
        }
        return false;
    }

    public function habilitar($cedula) {
        if ($this->model->setEstado($cedula, 1)) {
            $this->registrarBitacora("Paciente habilitado (C.I: $cedula)");
            return true;
        }
        return false;
    }
}