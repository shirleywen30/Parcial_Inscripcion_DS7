<?php
session_start();
require_once '../classes/Conexion.php';
require_once '../classes/Validacion.php';

$conexion_obj = new Conexion();
$conexion = $conexion_obj->conexion;

$sql = "SELECT i.*, p.nombre as pais_nombre, n.nombre as nacionalidad_nombre
        FROM inscriptores i
        LEFT JOIN paises p ON i.pais_residencia_id = p.id
        LEFT JOIN nacionalidades n ON i.nacionalidad_id = n.id
        ORDER BY i.fecha_registro DESC";

$resultado = $conexion->query($sql);
$inscritos = $resultado->fetch_all(MYSQLI_ASSOC);

foreach ($inscritos as &$inscrito) {
    $temas_sql = "SELECT GROUP_CONCAT(a.nombre ORDER BY a.nombre SEPARATOR ', ') as temas
                  FROM inscriptor_temas it
                  JOIN areas_interes a ON it.area_interes_id = a.id
                  WHERE it.inscriptor_id = " . intval($inscrito['id']);
    $temas_resultado = $conexion->query($temas_sql);
    $temas_row = $temas_resultado->fetch_assoc();
    $inscrito['temas'] = $temas_row['temas'] ?? '';

    // Punto 20: Verificar integridad con firma OpenSSL
    $datos_a_verificar = [
        'identificacion' => $inscrito['identificacion'],
        'nombre'         => $inscrito['nombre'],
        'apellido'       => $inscrito['apellido'],
        'correo'         => $inscrito['correo'],
        'celular'        => $inscrito['celular'],
        'sexo'           => $inscrito['sexo'],
    ];

    // Si el registro no tiene firma, generarla y guardarla automáticamente
    if (empty($inscrito['firma'])) {
        $firma_nueva = Validacion::generarFirma($datos_a_verificar);
        if ($firma_nueva) {
            $stmt_upd = $conexion->prepare("UPDATE inscriptores SET firma = ? WHERE id = ?");
            $stmt_upd->bind_param("si", $firma_nueva, $inscrito['id']);
            $stmt_upd->execute();
            $inscrito['firma'] = $firma_nueva;
        }
    }

    $inscrito['integridad'] = !empty($inscrito['firma'])
        ? Validacion::verificarFirma($datos_a_verificar, $inscrito['firma'])
        : false;
}
unset($inscrito);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Inscritos</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <style>
        .badge-integridad {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            white-space: nowrap;
        }
        .badge-ok    { background-color: #e8f5f1; color: #2e7d5e; border: 1px solid #6b9080; }
        .badge-error { background-color: #fef5f5; color: #c94a5c; border: 1px solid #c94a5c; }
        .badge-integridad::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }
        .badge-ok::before    { background-color: #6b9080; }
        .badge-error::before { background-color: #c94a5c; }
    </style>
</head>
<body>
    <div class="contenedor">
        <div class="encabezado">
            <h1>Reporte de Inscritos</h1>
            <p>Total de registros: <strong><?= count($inscritos) ?></strong></p>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alerta error" style="margin-bottom:20px;padding:14px;border-radius:6px;background-color:#fef5f5;border-left:3px solid #c94a5c;color:#c94a5c;font-weight:500;">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="reporte-header">
            <a href="index.php" class="btn-volver">Volver al Formulario</a>
            <a href="exportar_excel.php" class="btn-excel">Descargar Excel</a>
        </div>

        <div class="seccion">
            <table>
                <thead>
                    <tr>
                        <th style="width:40px">ID</th>
                        <th style="width:90px">Integridad</th>
                        <th>Identificacion</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th style="width:40px">Edad</th>
                        <th style="width:70px">Sexo</th>
                        <th>Pais</th>
                        <th>Correo</th>
                        <th>Celular</th>
                        <th>Temas</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($inscritos as $inscrito): ?>
                    <tr>
                        <td><?= $inscrito['id'] ?></td>
                        <td>
                            <?php if ($inscrito['integridad']): ?>
                                <span class="badge-integridad badge-ok" title="Firma OpenSSL verificada">Verificado</span>
                            <?php else: ?>
                                <span class="badge-integridad badge-error" title="Firma inválida o ausente">Vulnerado</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($inscrito['identificacion']) ?></td>
                        <td><?= htmlspecialchars($inscrito['nombre']) ?></td>
                        <td><?= htmlspecialchars($inscrito['apellido']) ?></td>
                        <td><?= $inscrito['edad'] ?></td>
                        <td><?= htmlspecialchars($inscrito['sexo']) ?></td>
                        <td><?= htmlspecialchars($inscrito['pais_nombre']) ?></td>
                        <td><?= htmlspecialchars($inscrito['correo']) ?></td>
                        <td><?= htmlspecialchars($inscrito['celular']) ?></td>
                        <td><?= htmlspecialchars($inscrito['temas']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($inscrito['fecha_registro'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="total">
                Total de Inscritos: <?= count($inscritos) ?>
            </div>
        </div>

        <div class="pie-pagina">
            <p><strong>© <?= date('Y') ?> iTECH</strong> - All rights reserved.</p>
        </div>
    </div>
</body>
</html>
