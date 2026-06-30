<?php
require_once 'Conexion.php';

class Validacion {
    
    public static function validarIdentificacion($identificacion) {
        $identificacion = trim($identificacion);
        return strlen($identificacion) >= 5 ? $identificacion : false;
    }
    
    public static function validarNombre($nombre) {
        $nombre = trim($nombre);
        $nombre = strtolower($nombre);
        $nombre = ucwords($nombre);
        $nombre = preg_replace("/[^a-záéíóúñ\s\-']/i", "", $nombre);
        $nombre = preg_replace("/\s+/", " ", $nombre);
        return strlen($nombre) >= 2 ? $nombre : false;
    }
    
    public static function validarEdad($edad) {
        $edad = intval($edad);
        return ($edad >= 13 && $edad <= 120) ? $edad : false;
    }
    
    public static function validarCorreo($correo) {
        $correo = trim(strtolower($correo));
        return filter_var($correo, FILTER_VALIDATE_EMAIL) ? $correo : false;
    }
    
    public static function validarCelular($celular) {
        $celular = trim($celular);
        $soloNumeros = preg_replace("/[^\d]/", "", $celular);
        return strlen($soloNumeros) >= 7 ? $celular : false;
    }
    
    // Verificar duplicados
    public static function identificacionExiste($identificacion) {
        $conexion_obj = new Conexion();
        $conexion = $conexion_obj->conexion;
        $stmt = $conexion->prepare("SELECT id FROM inscriptores WHERE identificacion = ?");
        $stmt->bind_param("s", $identificacion);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
    
    public static function correoExiste($correo) {
        $conexion_obj = new Conexion();
        $conexion = $conexion_obj->conexion;
        $stmt = $conexion->prepare("SELECT id FROM inscriptores WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
    
    public static function celularExiste($celular) {
        $conexion_obj = new Conexion();
        $conexion = $conexion_obj->conexion;
        $stmt = $conexion->prepare("SELECT id FROM inscriptores WHERE celular = ?");
        $stmt->bind_param("s", $celular);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    // Punto 20: Firma digital de datos con OpenSSL (clave persistente)
    public static function generarFirma($datos) {
        require_once __DIR__ . '/ClaveOpenSSL.php';
        $private_key = ClaveOpenSSL::getPrivateKey();
        if (!$private_key) return null;

        $datos_string = json_encode($datos, JSON_UNESCAPED_UNICODE);
        $ok = openssl_sign($datos_string, $firma_raw, $private_key, OPENSSL_ALGO_SHA256);
        return $ok ? base64_encode($firma_raw) : null;
    }

    public static function verificarFirma($datos, $firma_base64) {
        require_once __DIR__ . '/ClaveOpenSSL.php';
        $public_key = ClaveOpenSSL::getPublicKey();
        if (!$public_key) return false;

        $datos_string = json_encode($datos, JSON_UNESCAPED_UNICODE);
        $firma_raw    = base64_decode($firma_base64);
        return openssl_verify($datos_string, $firma_raw, $public_key, OPENSSL_ALGO_SHA256) === 1;
    }
}