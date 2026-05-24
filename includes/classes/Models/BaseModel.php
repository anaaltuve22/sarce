<?php
class BaseModel {
    protected $db;

    public function __construct($conexion) {
        if (!$conexion) {
            throw new Exception("Error: No se proporcionó una conexión válida a la base de datos.");
        }
        $this->db = $conexion;
    }
}