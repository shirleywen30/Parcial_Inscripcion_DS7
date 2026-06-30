<?php
session_start();

require_once '../classes/Validacion.php';
require_once '../classes/SanitizacionDatos.php';
require_once '../classes/ModeloInscriptor.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// 1. Sanitizar primero con SanitizacionDatos
$identificacion_raw = SanitizacionDatos::limpiarIdentificacion($_POST['identificacion'] ?? '');
$nombre_raw         = SanitizacionDatos::limpiarNombre($_POST['nombre'] ?? '');
$apellido_raw       = SanitizacionDatos::limpiarNombre($_POST['apellido'] ?? '');
$correo_raw         = SanitizacionDatos::limpiarCorreo($_POST['correo'] ?? '');
$celular_raw        = SanitizacionDatos::limpiarCelular($_POST['celular'] ?? '');

// 2. Validar con Validacion
$identificacion = Validacion::validarIdentificacion($identificacion_raw ?: ($_POST['identificacion'] ?? ''));
$nombre         = Validacion::validarNombre($nombre_raw ?: ($_POST['nombre'] ?? ''));
$apellido       = Validacion::validarNombre($apellido_raw ?: ($_POST['apellido'] ?? ''));
$edad           = Validacion::validarEdad($_POST['edad'] ?? '');
$sexo           = $_POST['sexo'] ?? '';
$pais_residencia_id = $_POST['pais_residencia_id'] ?? '';
$nacionalidad_id    = $_POST['nacionalidad_id'] ?? '';
$correo         = Validacion::validarCorreo($correo_raw ?: ($_POST['correo'] ?? ''));
$celular        = Validacion::validarCelular($celular_raw ?: ($_POST['celular'] ?? ''));
$observaciones  = trim($_POST['observaciones'] ?? '');
$areas          = $_POST['areas'] ?? [];

$errores = [];

if (!$identificacion) {
    $errores['identificacion'] = 'Identificacion invalida';
} elseif (Validacion::identificacionExiste($identificacion)) {
    $errores['identificacion'] = 'La cedula ya esta registrada';
}

if (!$nombre)   $errores['nombre']   = 'Nombre invalido';
if (!$apellido) $errores['apellido'] = 'Apellido invalido';
if (!$edad)     $errores['edad']     = 'Edad invalida';
if (empty($sexo))               $errores['sexo']              = 'Sexo requerido';
if (empty($pais_residencia_id)) $errores['pais_residencia_id'] = 'Pais requerido';
if (empty($nacionalidad_id))    $errores['nacionalidad_id']   = 'Nacionalidad requerida';

if (!$correo) {
    $errores['correo'] = 'Correo invalido';
} elseif (Validacion::correoExiste($correo)) {
    $errores['correo'] = 'El correo ya esta registrado';
}

if (!$celular) {
    $errores['celular'] = 'Celular invalido';
} elseif (Validacion::celularExiste($celular)) {
    $errores['celular'] = 'El celular ya esta registrado';
}

if (empty($areas)) $errores['areas'] = 'Selecciona al menos un area';

if (!empty($errores)) {
    $_SESSION['errores'] = $errores;
    header('Location: index.php');
    exit;
}

// 3. Generar firma digital OpenSSL (Punto 20)
$datos_a_firmar = [
    'identificacion' => $identificacion,
    'nombre'         => $nombre,
    'apellido'       => $apellido,
    'correo'         => $correo,
    'celular'        => $celular,
    'sexo'           => $sexo,
];
$firma = Validacion::generarFirma($datos_a_firmar);

$datos = [
    'identificacion'    => $identificacion,
    'nombre'            => $nombre,
    'apellido'          => $apellido,
    'edad'              => $edad,
    'sexo'              => $sexo,
    'pais_residencia_id'=> intval($pais_residencia_id),
    'nacionalidad_id'   => intval($nacionalidad_id),
    'correo'            => $correo,
    'celular'           => $celular,
    'observaciones'     => $observaciones,
    'firma'             => $firma,
];

$modelo = new ModeloInscriptor();
$id = $modelo->guardarInscriptor($datos, $areas);

if ($id) {
    header('Location: confirmacion.php?id=' . $id);
    exit;
} else {
    $_SESSION['error'] = 'Error al guardar los datos';
    header('Location: index.php');
    exit;
}
?>
