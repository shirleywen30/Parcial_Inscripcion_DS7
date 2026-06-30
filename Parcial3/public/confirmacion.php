<?php
require_once '../classes/Conexion.php';

$id = $_GET['id'] ?? 0;

$conexion_obj = new Conexion();
$conexion = $conexion_obj->conexion;

$resultado = $conexion->query("SELECT * FROM inscriptores WHERE id=$id");
$inscriptor = $resultado->fetch_assoc();

if (!$inscriptor) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmacion de Inscripcion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
    <div class="contenedor">
        <div class="encabezado">
            <h1>Inscripcion Confirmada</h1>
            <p>Tu solicitud fue procesada exitosamente</p>
        </div>

        <div class="confirmacion-container">
            <div class="seccion">
                <div class="alerta exito">
                    Numero de inscripcion: <strong>#<?= $inscriptor['id'] ?></strong>
                </div>

                <div class="datos-confirmacion">
                    <p>
                        <strong>Identificacion:</strong><br>
                        <?= $inscriptor['identificacion'] ?>
                    </p>
                    <p>
                        <strong>Nombre:</strong><br>
                        <?= $inscriptor['nombre'] . ' ' . $inscriptor['apellido'] ?>
                    </p>
                    <p>
                        <strong>Edad:</strong><br>
                        <?= $inscriptor['edad'] ?> anos
                    </p>
                    <p>
                        <strong>Sexo:</strong><br>
                        <?= $inscriptor['sexo'] ?>
                    </p>
                    <p>
                        <strong>Correo:</strong><br>
                        <?= $inscriptor['correo'] ?>
                    </p>
                    <p>
                        <strong>Celular:</strong><br>
                        <?= $inscriptor['celular'] ?>
                    </p>
                    <p>
                        <strong>Fecha de Inscripcion:</strong><br>
                        <?= date('d/m/Y H:i', strtotime($inscriptor['fecha_registro'])) ?>
                    </p>
                </div>

                <div class="btn-reporte">
                    <a href="index.php" class="btn-volver">Volver al Formulario</a>
                </div>
            </div>
        </div>

        <div class="pie-pagina">
    <p><strong>© <?= date('Y') ?> iTECH</strong> - All rights reserved. </p>
</div>
    </div>
</body>
</html>