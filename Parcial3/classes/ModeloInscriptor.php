<?php

class ModeloInscriptor {
    private $conexion;
    
    public function __construct() {
        $conexion_obj = new Conexion();
        $this->conexion = $conexion_obj->getConexion();
    }
    
    public function obtenerPaises() {
        $sql = "SELECT id, nombre FROM paises ORDER BY nombre";
        $resultado = $this->conexion->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
    
    public function obtenerNacionalidades() {
        $sql = "SELECT id, nombre FROM nacionalidades ORDER BY nombre";
        $resultado = $this->conexion->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
    
    public function obtenerAreas() {
        $sql = "SELECT id, nombre FROM areas_interes ORDER BY nombre";
        $resultado = $this->conexion->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
    
    public function guardarInscriptor($datos, $areas) {
    $identificacion = $datos['identificacion'];
    $nombre = $datos['nombre'];
    $apellido = $datos['apellido'];
    $edad = $datos['edad'];
    $sexo = $datos['sexo'];
    $pais_residencia_id = $datos['pais_residencia_id'];
    $nacionalidad_id = $datos['nacionalidad_id'];
    $correo = $datos['correo'];
    $celular = $datos['celular'];
    $observaciones = $datos['observaciones'];
    $firma = $datos['firma'] ?? null;

    $stmt = $this->conexion->prepare("INSERT INTO inscriptores (identificacion, nombre, apellido, edad, sexo, pais_residencia_id, nacionalidad_id, correo, celular, observaciones, firma) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        error_log("Error prepare: " . $this->conexion->error);
        return false;
    }

    $stmt->bind_param("sssiiiissss", $identificacion, $nombre, $apellido, $edad, $sexo, $pais_residencia_id, $nacionalidad_id, $correo, $celular, $observaciones, $firma);
    
    if (!$stmt->execute()) {
        error_log("Error execute: " . $stmt->error);
        return false;
    }
    
    $id_inscriptor = $stmt->insert_id;
    
    foreach ($areas as $area_id) {
        $area_id = intval($area_id);
        $stmt2 = $this->conexion->prepare("INSERT INTO inscriptor_temas (inscriptor_id, area_interes_id) VALUES (?, ?)");
        $stmt2->bind_param("ii", $id_inscriptor, $area_id);
        $stmt2->execute();
    }
    
    $stmt->close();
    
    return $id_inscriptor;
}
    
    public function obtenerInscriptor($id) {
        $sql = "SELECT i.*, p.nombre as pais_nombre, n.nombre as nacionalidad_nombre 
                FROM inscriptores i
                LEFT JOIN paises p ON i.pais_residencia_id = p.id
                LEFT JOIN nacionalidades n ON i.nacionalidad_id = n.id
                WHERE i.id = $id";
        
        $resultado = $this->conexion->query($sql);
        return $resultado->fetch_assoc();
    }
    
    public function obtenerTodos() {
        $sql = "SELECT i.*, p.nombre as pais_nombre, n.nombre as nacionalidad_nombre 
                FROM inscriptores i
                LEFT JOIN paises p ON i.pais_residencia_id = p.id
                LEFT JOIN nacionalidades n ON i.nacionalidad_id = n.id
                ORDER BY i.fecha_registro DESC";
        
        $resultado = $this->conexion->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
    
    public function obtenerAreasInscriptor($id_inscriptor) {
        $sql = "SELECT a.nombre FROM inscriptor_temas it
                JOIN areas_interes a ON it.area_interes_id = a.id
                WHERE it.inscriptor_id = $id_inscriptor";
        
        $resultado = $this->conexion->query($sql);
        $areas = [];
        while ($fila = $resultado->fetch_assoc()) {
            $areas[] = $fila['nombre'];
        }
        
        return implode(', ', $areas);
    }
}

?>