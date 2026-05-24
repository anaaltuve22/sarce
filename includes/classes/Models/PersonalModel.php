<?php
require_once 'BaseModel.php';

class PersonalModel extends BaseModel {
    
    public function listarActivos() {
        $sql = "SELECT * FROM personal WHERE estado = 1 ORDER BY apellido ASC";
        return mysqli_query($this->db, $sql);
    }

    public function buscar($valor) {
        $valor = "%$valor%";
        $sql = "SELECT * FROM personal WHERE (nombre LIKE ? OR apellido LIKE ? OR cedula LIKE ?) AND estado = 1 ORDER BY apellido ASC";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $valor, $valor, $valor);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }

    public function getByCedula($cedula) {
        $stmt = mysqli_prepare($this->db, "SELECT * FROM personal WHERE cedula = ?");
        mysqli_stmt_bind_param($stmt, "s", $cedula);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($res);
    }

    public function getByUsuarioId($id_usuario) {
        $sql = "SELECT p.cedula FROM personal p 
                INNER JOIN usuarios u ON p.cedula = u.cedula 
                WHERE u.id = ? AND p.estado = 1";
        $stmt = mysqli_prepare($this->db, $sql);

        if (!$stmt) return null;

        mysqli_stmt_bind_param($stmt, "i", $id_usuario);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($res);
        mysqli_stmt_close($stmt);
        return $data;
    }

    public function existeCedula($cedula) {
        $stmt = mysqli_prepare($this->db, "SELECT estado FROM personal WHERE cedula = ?");
        mysqli_stmt_bind_param($stmt, "s", $cedula);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $existe = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);
        return $existe;
    }

    public function insertar($datos) {
        $stmt = mysqli_prepare($this->db, "INSERT INTO personal (cedula, nombre, apellido, cargo, estado) VALUES (?, ?, ?, ?, 1)");
        mysqli_stmt_bind_param($stmt, "ssss", $datos['cedula'], $datos['nombre'], $datos['apellido'], $datos['cargo']);
        $exito = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $exito;
    }

    public function actualizar($datos, $cedula_vieja) {
        $stmt = mysqli_prepare($this->db, "UPDATE personal SET cedula=?, nombre=?, apellido=?, cargo=? WHERE cedula=?");
        mysqli_stmt_bind_param($stmt, "sssss", $datos['cedula'], $datos['nombre'], $datos['apellido'], $datos['cargo'], $cedula_vieja);
        $exito = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $exito;
    }

    public function setEstado($cedula, $estado) {
        $stmt = mysqli_prepare($this->db, "UPDATE personal SET estado = ? WHERE cedula = ?");
        mysqli_stmt_bind_param($stmt, "is", $estado, $cedula);
        $exito = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $exito;
    }
}