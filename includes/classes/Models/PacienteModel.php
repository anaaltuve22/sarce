<?php
require_once 'BaseModel.php';

class PacienteModel extends BaseModel {
    
    public function getByCedula($cedula) {
        $stmt = mysqli_prepare($this->db, "SELECT * FROM pacientes WHERE cedula = ?");
        mysqli_stmt_bind_param($stmt, "s", $cedula);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        $paciente = mysqli_fetch_assoc($resultado);
        mysqli_stmt_close($stmt);
        return $paciente;
    }

    public function buscar($valor) {
        $valor = "%$valor%";
        $sql = "SELECT * FROM pacientes WHERE (nombre LIKE ? OR apellido LIKE ? OR cedula LIKE ?) AND estado = 1 ORDER BY apellido ASC";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $valor, $valor, $valor);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }

    public function listarActivos() {
        $sql = "SELECT * FROM pacientes WHERE estado = 1 ORDER BY apellido ASC, nombre ASC";
        return mysqli_query($this->db, $sql);
    }

    public function existeCedula($cedula) {
        $stmt = mysqli_prepare($this->db, "SELECT cedula FROM pacientes WHERE cedula = ?");
        mysqli_stmt_bind_param($stmt, "s", $cedula);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $existe = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);
        return $existe;
    }

    public function insertar($datos) {
        $sql = "INSERT INTO pacientes (cedula, nombre, apellido, genero, edad, fecha_nacimiento, direccion, telefono, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "ssssisss", 
            $datos['cedula'], 
            $datos['nombre'], 
            $datos['apellido'], 
            $datos['genero'], 
            $datos['edad'], 
            $datos['fecha_nacimiento'], 
            $datos['direccion'], 
            $datos['telefono']
        );
        $exito = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $exito;
    }

    public function actualizar($datos, $cedula_vieja) {
        $sql = "UPDATE pacientes SET cedula=?, nombre=?, apellido=?, genero=?, edad=?, fecha_nacimiento=?, telefono=?, direccion=? WHERE cedula=?";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "ssssissss", 
            $datos['cedula'], 
            $datos['nombre'], 
            $datos['apellido'], 
            $datos['genero'], 
            $datos['edad'], 
            $datos['fecha_nacimiento'], 
            $datos['telefono'], 
            $datos['direccion'], 
            $cedula_vieja
        );
        $exito = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $exito;
    }

    public function setEstado($cedula, $estado) {
        $stmt = mysqli_prepare($this->db, "UPDATE pacientes SET estado = ? WHERE cedula = ?");
        mysqli_stmt_bind_param($stmt, "is", $estado, $cedula);
        $exito = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $exito;
    }
}