<?php
require_once 'BaseModel.php';

class UsuarioModel extends BaseModel {

    public function getByLogin($identificador) {
        $stmt = mysqli_prepare($this->db, "SELECT * FROM usuarios WHERE usuario = ? OR correo = ?");
        mysqli_stmt_bind_param($stmt, "ss", $identificador, $identificador);
        mysqli_stmt_execute($stmt);
        return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    }

    public function getById($id) {
        $stmt = mysqli_prepare($this->db, "SELECT * FROM usuarios WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
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

    public function insertar($datos) {
        $sql = "INSERT INTO usuarios (nombre, apellido, correo, usuario, clave, rol, pregunta_1, respuesta_1, pregunta_2, respuesta_2, pregunta_3, respuesta_3) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssssssss", 
            $datos['nombre'], $datos['apellido'], $datos['correo'], $datos['usuario'], 
            $datos['clave'], $datos['rol'], $datos['p1'], $datos['r1'], 
            $datos['p2'], $datos['r2'], $datos['p3'], $datos['r3']
        );
        return mysqli_stmt_execute($stmt);
    }

    public function updatePassword($userId, $newHashedPassword) {
        $stmt = mysqli_prepare($this->db, "UPDATE usuarios SET clave = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $newHashedPassword, $userId);
        $exito = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $exito;
    }
    public function registrarBitacora($usuario, $id_rel, $accion) {
        $stmt = mysqli_prepare($this->db, "INSERT INTO bitacora (usuario, id_usuario_rel, accion, fecha_hora) VALUES (?, ?, ?, NOW())");
        mysqli_stmt_bind_param($stmt, "sis", $usuario, $id_rel, $accion);
        return mysqli_stmt_execute($stmt);
    }

    public function getBitacora() {
        return mysqli_query($this->db, "SELECT * FROM bitacora ORDER BY fecha_hora DESC");
    }
}