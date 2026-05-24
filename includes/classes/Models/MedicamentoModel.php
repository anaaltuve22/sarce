<?php
require_once 'BaseModel.php';

class MedicamentoModel extends BaseModel {

    public function listarActivos() {
        $sql = "SELECT * FROM medicamentos WHERE estado = 1 ORDER BY nombre ASC";
        return mysqli_query($this->db, $sql);
    }

    public function buscar($valor) {
        $valor = "%$valor%";
        $sql = "SELECT * FROM medicamentos WHERE (nombre LIKE ? OR descripcion LIKE ?) AND estado = 1 ORDER BY nombre ASC";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $valor, $valor);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }

    public function getById($id) {
        $stmt = mysqli_prepare($this->db, "SELECT * FROM medicamentos WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    }

    public function insertar($datos) {
        $vencimiento = !empty($datos['fecha_vencimiento']) ? $datos['fecha_vencimiento'] : NULL;
        $stmt = mysqli_prepare($this->db, "INSERT INTO medicamentos (nombre, descripcion, fecha_vencimiento, estado) VALUES (?, ?, ?, 1)");
        mysqli_stmt_bind_param($stmt, "sss", $datos['nombre'], $datos['descripcion'], $vencimiento);
        $exito = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $exito;
    }

    public function actualizar($datos) {
        $vencimiento = !empty($datos['fecha_vencimiento']) ? $datos['fecha_vencimiento'] : NULL;
        $stmt = mysqli_prepare($this->db, "UPDATE medicamentos SET nombre=?, descripcion=?, fecha_vencimiento=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sssi", $datos['nombre'], $datos['descripcion'], $vencimiento, $datos['id_med']);
        $exito = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $exito;
    }

    public function setEstado($id, $estado) {
        $stmt = mysqli_prepare($this->db, "UPDATE medicamentos SET estado = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $estado, $id);
        $exito = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $exito;
    }
}