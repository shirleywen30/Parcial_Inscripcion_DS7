<?php
class SanitizacionDatos {
    
    public static function limpiarNombre($nombre) {
        $nombre = trim($nombre);
        $nombre = strtolower($nombre);
        $nombre = ucwords($nombre);
        return preg_replace("/[^a-záéíóúñ\s']/i", "", $nombre);
    }
    
    public static function limpiarCorreo($correo) {
        $correo = trim(strtolower($correo));
        return filter_var($correo, FILTER_VALIDATE_EMAIL) ? $correo : false;
    }
    
    public static function limpiarCelular($celular) {
        $celular = trim($celular);
        $celular = preg_replace("/[^\d\-\+]/", "", $celular);
        $soloNumeros = preg_replace("/[^\d]/", "", $celular);
        return strlen($soloNumeros) >= 7 ? $celular : false;
    }
    
    public static function limpiarIdentificacion($identificacion) {
        $identificacion = trim(preg_replace("/[^\d\-]/", "", $identificacion));
        return strlen($identificacion) >= 5 ? $identificacion : false;
    }
    
    public static function validarSexo($sexo) {
        $sexo = strtoupper(trim($sexo));
        return ($sexo === 'M' || $sexo === 'F') ? $sexo : false;
    }
}
?>