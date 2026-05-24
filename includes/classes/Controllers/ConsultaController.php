<?php
require_once 'BaseController.php';

class ConsultaController extends BaseController {
    private $model;
    private $patologiaModel;

    public function __construct($conexion) {
        parent::__construct($conexion);
        $this->model = new ConsultaModel($conexion);
        $this->patologiaModel = new PatologiaModel($conexion);
    }

    public function registrar($datos) {
        // Gestión de nueva patología si aplica
        if ($datos['id_patologia'] === 'nueva') {
            if (empty($datos['nueva_patologia_nombre'])) {
                return ['status' => 'error', 'msg' => 'Debe indicar el nombre de la patología.'];
            }
            $datos['id_patologia'] = $this->patologiaModel->obtenerOInsertar($datos['nueva_patologia_nombre']);
        }

        $id_consulta = $this->model->insertar($datos);
        
        if ($id_consulta) {
            $this->registrarBitacora("Consulta médica registrada para paciente: {$datos['cedula_paciente']}");
            return ['status' => 'success', 'id' => $id_consulta];
        }
        
        return ['status' => 'error', 'msg' => 'Error al guardar la consulta.'];
    }

    public function listar($periodo = 'todos') {
        $where = "";
        if ($periodo == 'semanal') $where = "WHERE YEARWEEK(c.fecha, 1) = YEARWEEK(CURRENT_DATE(), 1)";
        if ($periodo == 'mes') $where = "WHERE MONTH(c.fecha) = MONTH(CURRENT_DATE())";
        
        return $this->model->getReporteEPI10($where);
    }

    public function obtenerDetalles($id) {
        return $this->model->getByIdConDetalles($id);
    }

    public function obtenerHistorial($cedula) {
        return $this->model->getHistorialByCedula($cedula);
    }

    public function obtenerEstadisticas() {
        return $this->model->getEstadisticasRapidas();
    }

    public function listarPatologias() {
        return $this->patologiaModel->listarTodas();
    }

    public function listarConsultas() {
        return $this->model->listarConPacientes();
    }

    public function obtenerTopPatologias($periodo = 'todos') {
        $where = "";
        if ($periodo == 'semanal') $where = "WHERE YEARWEEK(c.fecha, 1) = YEARWEEK(CURRENT_DATE(), 1)";
        if ($periodo == 'mes') $where = "WHERE MONTH(c.fecha) = MONTH(CURRENT_DATE()) AND YEAR(c.fecha) = YEAR(CURRENT_DATE())";
        if ($periodo == 'año') $where = "WHERE YEAR(c.fecha) = YEAR(CURRENT_DATE())";
        
        return $this->model->getTopPatologias($where);
    }
}