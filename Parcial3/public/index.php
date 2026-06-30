<?php
session_start();
require_once '../classes/Conexion.php';

$conexion_obj = new Conexion();
$conexion = $conexion_obj->conexion;

$paises = $conexion->query("SELECT id, nombre FROM paises ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
$nacionalidades = $conexion->query("SELECT id, nombre FROM nacionalidades ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
$areas = $conexion->query("SELECT id, nombre FROM areas_interes ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Inscripcion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
    <div class="contenedor">
        <div class="encabezado">
            <h1>Inscripcion</h1>
        </div>

        <?php
if (isset($_SESSION['error'])) {
    echo '<div class="alerta error" style="margin-bottom: 20px; padding: 14px; border-radius: 6px; background-color: #fef5f5; border-left: 3px solid #c94a5c; color: #c94a5c; font-weight: 500;">' . htmlspecialchars($_SESSION['error']) . '</div>';
    unset($_SESSION['error']);
}
if (isset($_SESSION['errores']) && !empty($_SESSION['errores'])) {
    echo '<div class="alerta error" style="margin-bottom: 20px; padding: 14px; border-radius: 6px; background-color: #fef5f5; border-left: 3px solid #c94a5c; color: #c94a5c; font-weight: 500;">';
    echo '<ul style="margin: 0; padding-left: 18px;">';
    foreach ($_SESSION['errores'] as $campo => $msg) {
        echo '<li>' . htmlspecialchars($msg) . '</li>';
    }
    echo '</ul></div>';
    unset($_SESSION['errores']);
}
?>

        <form id="formulario" method="POST" action="procesar.php" novalidate>
            
            <div class="seccion">
                <div class="seccion-titulo">Datos Personales</div>
                
                <div class="grupo">
                    <label>Identificacion <span class="required">*</span></label>
                    <input type="text" name="identificacion" placeholder="8-123-456" required>
                    <div class="error-msg"></div>
                </div>

                <div class="grupo dos-col">
                    <div>
                        <label>Nombre <span class="required">*</span></label>
                        <input type="text" name="nombre" placeholder="Nombre" required>
                        <div class="error-msg"></div>
                    </div>
                    <div>
                        <label>Apellido <span class="required">*</span></label>
                        <input type="text" name="apellido" placeholder="Apellido" required>
                        <div class="error-msg"></div>
                    </div>
                </div>

                <div class="grupo tres-col">
                    <div>
                        <label>Edad <span class="required">*</span></label>
                        <input type="number" name="edad" min="13" max="120" placeholder="25" required>
                        <div class="error-msg"></div>
                    </div>
                    <div>
                        <label>Sexo <span class="required">*</span></label>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" name="sexo" value="Masculino" id="sexo-m" required>
                                <label for="sexo-m">Masculino</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" name="sexo" value="Femenino" id="sexo-f" required>
                                <label for="sexo-f">Femenino</label>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label>Pais <span class="required">*</span></label>
                        <select name="pais_residencia_id" required>
                            <option value="">Selecciona</option>
                            <?php foreach($paises as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= $p['nombre'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="error-msg"></div>
                    </div>
                </div>

                <div class="grupo">
                    <label>Nacionalidad <span class="required">*</span></label>
                    <select name="nacionalidad_id" required>
                        <option value="">Selecciona</option>
                        <?php foreach($nacionalidades as $n): ?>
                            <option value="<?= $n['id'] ?>"><?= $n['nombre'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="error-msg"></div>
                </div>
            </div>

            <div class="seccion">
                <div class="seccion-titulo">Contacto</div>
                
                <div class="grupo dos-col">
                    <div>
                        <label>Correo <span class="required">*</span></label>
                        <input type="email" name="correo" placeholder="email@ejemplo.com" required>
                        <div class="error-msg"></div>
                    </div>
                    <div>
                        <label>Celular <span class="required">*</span></label>
                        <input type="tel" name="celular" placeholder="+507 61234567" required>
                        <div class="error-msg"></div>
                    </div>
                </div>
            </div>

            <div class="seccion">
                <div class="seccion-titulo">Areas de Interes</div>
                
                <div class="grupo grupo-areas">
                    <div class="checkboxes">
                        <?php foreach($areas as $a): ?>
                            <div class="checkbox-item">
                                <input type="checkbox" name="areas[]" value="<?= $a['id'] ?>" id="area-<?= $a['id'] ?>">
                                <label for="area-<?= $a['id'] ?>"><?= $a['nombre'] ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="error-msg"></div>
                </div>
            </div>

            <div class="seccion">
                <div class="seccion-titulo">Observaciones</div>
                
                <div class="grupo">
                    <textarea name="observaciones" placeholder="Comentarios adicionales..."></textarea>
                </div>
            </div>

            <div class="botones">
                <button type="submit" class="btn-enviar">Enviar Inscripcion</button>
                <button type="reset" class="btn-limpiar">Limpiar</button>
                <a href="reporte.php" class="btn-excel" style="display: flex; align-items: center; justify-content: center;">Ver Reporte</a>
            </div>
        </form>

        <div class="pie-pagina">
    <p><strong>© <?= date('Y') ?> iTECH</strong> - All rights reserved. </p>
</div>
    </div>

    <script src="../assets/js/validacion.js"></script>
</body>
</html>