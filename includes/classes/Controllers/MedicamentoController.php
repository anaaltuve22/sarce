<?php
require_once 'BaseController.php';

class MedicamentoController extends BaseController {
    private $model;

    public function __construct($conexion) {
        parent::__construct($conexion);
        $this->model = new MedicamentoModel($conexion);
    }

    public function listar() {
        return $this->model->listarActivos();
    }

    public function obtenerPorId($id) {
        return $this->model->getById($id);
    }

    public function registrar($datos) {
        if ($this->model->insertar($datos)) {
            $this->registrarBitacora("Nuevo medicamento registrado: {$datos['nombre']}");
            return ['status' => 'success', 'msg' => 'Medicamento guardado con éxito.'];
        }
        return ['status' => 'error', 'msg' => 'Error al registrar el medicamento.'];
    }

    public function actualizar($datos) {
        if ($this->model->actualizar($datos)) {
            $this->registrarBitacora("Medicamento actualizado: {$datos['nombre']}");
            return ['status' => 'success', 'msg' => 'Cambios guardados con éxito.'];
        }
        return ['status' => 'error', 'msg' => 'Error al actualizar el medicamento.'];
    }

    public function inhabilitar($id) {
        if ($this->model->setEstado($id, 0)) {
            $this->registrarBitacora("Medicamento inhabilitado (ID: $id)");
            return true;
        }
        return false;
    }
}