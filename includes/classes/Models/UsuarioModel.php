<?php
require_once 'BaseModel.php';

class UsuarioModel extends BaseModel {

    public function getByLogin($identificador) {
        $stmt = mysqli_prepare($this->db, "SELECT * FROM usuarios WHERE usuario = ? OR correo = ?");
        mysqli_stmt_bind_param($stmt, "ss", $identificador, $identificador);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        return $resultado;
    }

    public function getById($id) {
        $stmt = mysqli_prepare($this->db, "SELECT * FROM usuarios WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        return $resultado;
    }

    public function getSecurityQuestionsById($id) {
        $stmt = mysqli_prepare($this->db, "SELECT id, pregunta_1, pregunta_2, pregunta_3, respuesta_1, respuesta_2, respuesta_3 FROM usuarios WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $data;
    }

    public function getSecurityQuestionsByIdentifier($identificador) {
        $stmt = mysqli_prepare($this->db, "SELECT id, pregunta_1, pregunta_2, pregunta_3, respuesta_1, respuesta_2, respuesta_3 FROM usuarios WHERE usuario = ? OR correo = ?");
        mysqli_stmt_bind_param($stmt, "ss", $identificador, $identificador);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $data;
    }

    public function getAll() {
        $sql = "SELECT * FROM usuarios ORDER BY nombre ASC";
        return mysqli_query($this->db, $sql);
    }

    public function buscar($valor) {
        $valor = "%$valor%";
        $sql = "SELECT * FROM usuarios WHERE nombre LIKE ? OR apellido LIKE ? OR usuario LIKE ? ORDER BY nombre ASC";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $valor, $valor, $valor);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }

    public function insertar($datos) {
        $sql = "INSERT INTO usuarios (nombre, apellido, correo, usuario, clave, rol, pregunta_1, respuesta_1, pregunta_2, respuesta_2, pregunta_3, respuesta_3, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssssssss", 
            $datos['nombre'], $datos['apellido'], $datos['correo'], $datos['usuario'], 
            $datos['clave'], $datos['rol'], 
            $datos['pregunta_1'], $datos['respuesta_1'], 
            $datos['pregunta_2'], $datos['respuesta_2'], 
            $datos['pregunta_3'], $datos['respuesta_3']
        );
        $exito = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $exito;
    }

    public function actualizar($datos, $id) {
        $campos = [];
        $tipos = "";
        $valores = [];

        foreach ($datos as $columna => $valor) {
            $campos[] = "$columna = ?";
            $tipos .= "s";
            $valores[] = $valor;
        }

        $sql = "UPDATE usuarios SET " . implode(", ", $campos) . " WHERE id = ?";
        $tipos .= "i";
        $valores[] = $id;

        $stmt = mysqli_prepare($this->db, $sql);
        if (!$stmt) return false;

        mysqli_stmt_bind_param($stmt, $tipos, ...$valores);
        $exito = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $exito;
    }

    public function updatePassword($userId, $newHashedPassword) {
        $stmt = mysqli_prepare($this->db, "UPDATE usuarios SET clave = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $newHashedPassword, $userId);
        $exito = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $exito;
    }

    public function setEstado($id, $estado) {
        $stmt = mysqli_prepare($this->db, "UPDATE usuarios SET estado = ? WHERE id = ?");
        if (!$stmt) return false;

        mysqli_stmt_bind_param($stmt, "ii", $estado, $id);
        $exito = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $exito;
    }

    public function registrarBitacora($usuario, $id_rel, $accion) {
        $stmt = mysqli_prepare($this->db, "INSERT INTO bitacora (usuario, id_usuario_rel, accion, fecha_hora) VALUES (?, ?, ?, NOW())");
        if (!$stmt) return false;

        mysqli_stmt_bind_param($stmt, "sis", $usuario, $id_rel, $accion);
        $exito = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $exito;
    }

    public function getBitacora($busqueda = null) {
        if (!empty($busqueda)) {
            $valor = "%$busqueda%";
            $sql = "SELECT * FROM bitacora WHERE usuario LIKE ? OR accion LIKE ? ORDER BY fecha_hora DESC";
            $stmt = mysqli_prepare($this->db, $sql);
            mysqli_stmt_bind_param($stmt, "ss", $valor, $valor);
            mysqli_stmt_execute($stmt);
            return mysqli_stmt_get_result($stmt);
        }
        return mysqli_query($this->db, "SELECT * FROM bitacora ORDER BY fecha_hora DESC");
    }
}