<?php
require_once 'BaseController.php';

class SistemaController extends BaseController {
    private $model;

    public function __construct($conexion) {
        parent::__construct($conexion);
        $this->model = new SistemaModel($conexion);
    }

    /**
     * Genera el respaldo y fuerza la descarga del archivo SQL.
     */
    public function respaldar() {
        $contenido = $this->model->generarDump();
        $nombre = "respaldo_sarce_" . date("Y-m-d_H-i-s") . ".sql";
        
        $this->registrarBitacora("Respaldo de base de datos generado con éxito.");
        
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $nombre);
        echo $contenido;
        exit();
    }

    /**
     * Procesa el archivo subido para restaurar la base de datos.
     */
    public function restaurar($archivo) {
        if ($archivo['error'] !== 0) {
            return ['status' => 'error', 'msg' => 'No se pudo procesar el archivo subido.'];
        }

        $ext = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        if (strtolower($ext) !== 'sql') {
            return ['status' => 'error', 'msg' => 'Formato inválido. Por favor suba un archivo .sql'];
        }

        $sql = file_get_contents($archivo['tmp_name']);
        if ($this->model->ejecutarSql($sql)) {
            $this->registrarBitacora("Restauración completa del sistema realizada.");
            return ['status' => 'success', 'msg' => 'La base de datos ha sido restablecida correctamente.'];
        }
        
        return ['status' => 'error', 'msg' => 'Error técnico al ejecutar el archivo de restauración.'];
    }

    /**
     * Gestiona la descarga del manual de usuario desde el directorio docs.
     */
    public function descargarManual() {
        $file = $_SERVER['DOCUMENT_ROOT'] . '/sarce/docs/manual_usuario.pdf';

        if (file_exists($file)) {
            $this->registrarBitacora("Descarga de manual de usuario realizada.");
            
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="Manual_Usuario_SARCE.pdf"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit();
        }
        
        return ['status' => 'error', 'msg' => 'El archivo del manual no fue encontrado en el servidor.'];
    }
}