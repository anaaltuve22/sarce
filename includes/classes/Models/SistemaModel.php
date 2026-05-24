<?php
require_once 'BaseModel.php';

class SistemaModel extends BaseModel {

    /**
     * Genera un volcado SQL de toda la base de datos.
     * Mejora la versión anterior manejando nulos y escapado de caracteres.
     */
    public function generarDump() {
        $tablas = [];
        $result = mysqli_query($this->db, "SHOW TABLES");
        while ($row = mysqli_fetch_row($result)) {
            $tablas[] = $row[0];
        }

        $salida = "-- Respaldo SARCE generado el " . date("Y-m-d H:i:s") . "\n";
        $salida .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tablas as $tabla) {
            // Obtener estructura
            $resStructure = mysqli_query($this->db, "SHOW CREATE TABLE `$tabla` ");
            $rowStructure = mysqli_fetch_row($resStructure);
            $salida .= "DROP TABLE IF EXISTS `$tabla`;\n";
            $salida .= $rowStructure[1] . ";\n\n";

            // Obtener datos
            $resData = mysqli_query($this->db, "SELECT * FROM `$tabla` ");
            while ($rowData = mysqli_fetch_row($resData)) {
                $salida .= "INSERT INTO `$tabla` VALUES(";
                for ($j = 0; $j < count($rowData); $j++) {
                    if (isset($rowData[$j])) {
                        $val = mysqli_real_escape_string($this->db, $rowData[$j]);
                        $salida .= '"' . $val . '"';
                    } else {
                        $salida .= 'NULL';
                    }
                    if ($j < (count($rowData) - 1)) $salida .= ',';
                }
                $salida .= ");\n";
            }
        }
        $salida .= "\nSET FOREIGN_KEY_CHECKS=1;";
        return $salida;
    }

    /**
     * Ejecuta un script SQL de múltiples consultas.
     */
    public function ejecutarSql($sql) {
        mysqli_query($this->db, "SET FOREIGN_KEY_CHECKS = 0");
        $exito = mysqli_multi_query($this->db, $sql);
        
        if ($exito) {
            do {
                if ($result = mysqli_store_result($this->db)) mysqli_free_result($result);
            } while (mysqli_next_result($this->db));
        }
        
        mysqli_query($this->db, "SET FOREIGN_KEY_CHECKS = 1");
        return $exito;
    }
}