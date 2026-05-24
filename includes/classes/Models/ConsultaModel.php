<?php
require_once 'BaseModel.php';

class ConsultaModel extends BaseModel {

    public function listarConPacientes() {
        $sql = "SELECT c.id_consulta, c.fecha, p.cedula, p.nombre, p.apellido, c.tension, c.peso, c.temperatura, c.tratamiento 
                FROM consultas c
                INNER JOIN pacientes p ON c.cedula_paciente = p.cedula
                ORDER BY c.fecha DESC, c.id_consulta DESC";
        return mysqli_query($this->db, $sql);
    }

    public function insertar($datos) {
        $sql = "INSERT INTO consultas (
            cedula_paciente, edad, direccion, tension, peso, talla, temperatura, 
            fecha, motivo, id_patologia, tratamiento, consultorio_procedencia, 
            medicamentos_entregados, cedula_personal
        ) VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE(), ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "sissssssissss",
            $datos['cedula_paciente'], $datos['edad'], $datos['direccion'], 
            $datos['tension'], $datos['peso'], $datos['talla'], $datos['temperatura'],
            $datos['motivo'], $datos['id_patologia'], $datos['tratamiento'], 
            $datos['procedencia'], $datos['medicamentos_entregados'], $datos['cedula_personal']
        );
        $exito = mysqli_stmt_execute($stmt);
        $id_generado = $exito ? mysqli_insert_id($this->db) : false;
        mysqli_stmt_close($stmt);
        return $id_generado;
    }

    public function getHistorialByCedula($cedula) {
        $sql = "SELECT c.*, pat.nombre_patologia 
                FROM consultas c 
                LEFT JOIN patologias pat ON c.id_patologia = pat.id 
                WHERE c.cedula_paciente = ? 
                ORDER BY c.fecha DESC, c.id_consulta DESC";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "s", $cedula);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }

    public function getByIdConDetalles($id) {
        $sql = "SELECT c.*, 
                         p.nombre AS pac_nom, p.apellido AS pac_ape, p.cedula AS pac_ced,
                         per.nombre AS doc_nom, per.apellido AS doc_ape,
                         pat.nombre_patologia 
                  FROM consultas c 
                  INNER JOIN pacientes p ON c.cedula_paciente = p.cedula 
                  LEFT JOIN personal per ON c.cedula_personal = per.cedula
                  LEFT JOIN patologias pat ON c.id_patologia = pat.id
                  WHERE c.id_consulta = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $datos = mysqli_fetch_assoc($res);
        mysqli_stmt_close($stmt);
        return $datos;
    }

    public function getReporteEPI10($where_clause = "") {
        $sql = "SELECT p.cedula, p.nombre, p.apellido, p.genero, 
                       c.direccion, c.tension, c.peso, c.talla, c.temperatura, c.tratamiento, c.medicamentos_entregados, c.fecha 
                FROM consultas c 
                INNER JOIN pacientes p ON c.cedula_paciente = p.cedula 
                $where_clause
                ORDER BY c.fecha DESC, c.id_consulta DESC";
        return mysqli_query($this->db, $sql);
    }

    public function getTopPatologias($where_clause = "") {
        $sql = "SELECT p.nombre_patologia, COUNT(c.id_consulta) as total 
                FROM consultas c
                INNER JOIN patologias p ON c.id_patologia = p.id
                $where_clause
                GROUP BY c.id_patologia
                ORDER BY total DESC 
                LIMIT 5";
        return mysqli_query($this->db, $sql);
    }

    public function getEstadisticasRapidas() {
        $stats = [];
        $res = mysqli_query($this->db, "SELECT COUNT(*) as total FROM pacientes");
        $stats['pacientes'] = mysqli_fetch_assoc($res)['total'];
        
        $res = mysqli_query($this->db, "SELECT COUNT(*) as total FROM consultas WHERE fecha = CURDATE()");
        $stats['consultas_hoy'] = mysqli_fetch_assoc($res)['total'];
        
        return $stats;
    }
}