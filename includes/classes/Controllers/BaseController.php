<?php
class BaseController {
    protected $db;
    protected $usuarioModel;

    public function __construct($conexion) {
        $this->db = $conexion;
        $this->usuarioModel = new UsuarioModel($conexion);
    }

    protected function registrarBitacora($accion) {
        $usuario = $_SESSION['admin'] ?? 'Sistema';
        $id_rel = $_SESSION['id_usuario_rel'] ?? null;
        return $this->usuarioModel->registrarBitacora($usuario, $id_rel, $accion);
    }
}