<?php

class myConexionPDO {
    private $host = '127.0.0.1';
    private $usuario = 'root';
    private $password = '';
    private $basedatos = 'formulario_inscripcion';
    private $conexion;
    
    public function __construct() {
        try {
            $this->conexion = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->basedatos . ";charset=utf8mb4",
                $this->usuario,
                $this->password
            );
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
    
    public function getConexion() {
        return $this->conexion;
    }
    
    public function cerrar() {
        $this->conexion = null;
    }
}

?>