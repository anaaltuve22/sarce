<?php
require_once 'BaseModel.php';

class PatologiaModel extends BaseModel {
    public function listarTodas() {
        return mysqli_query($this->db, "SELECT * FROM patologias ORDER BY nombre_patologia ASC");
    }

    public function obtenerOInsertar($nombre) {
        $nombre = mb_strtoupper(trim($nombre), 'UTF-8');
        
        $stmt = mysqli_prepare($this->db, "SELECT id FROM patologias WHERE nombre_patologia = ?");
        mysqli_stmt_bind_param($stmt, "s", $nombre);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($res)) {
            return $row['id'];
        }

        $stmt_ins = mysqli_prepare($this->db, "INSERT INTO patologias (nombre_patologia) VALUES (?)");
        mysqli_stmt_bind_param($stmt_ins, "s", $nombre);
        mysqli_stmt_execute($stmt_ins);
        $nuevo_id = mysqli_insert_id($this->db);
        mysqli_stmt_close($stmt_ins);
        return $nuevo_id;
    }
}