<?php

class Conexion {
    private $host = '127.0.0.1';
    private $usuario = 'root';
    private $password = '';
    private $basedatos = 'formulario_inscripcion';
    public $conexion;
    
    public function __construct() {
        $this->conexion = new mysqli(
            $this->host,
            $this->usuario,
            $this->password,
            $this->basedatos
        );
        
        if ($this->conexion->connect_error) {
            die("Error de conexión: " . $this->conexion->connect_error);
        }
        
        $this->conexion->set_charset("utf8mb4");
    }
    
    public function getConexion() {
        return $this->conexion;
    }
    
    public function cerrar() {
        $this->conexion->close();
    }
}

?>