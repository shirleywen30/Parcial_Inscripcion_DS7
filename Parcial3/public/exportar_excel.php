<?php
session_start();
require_once '../vendor/autoload.php';
require_once '../classes/Conexion.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

$conexion_obj = new Conexion();
$conexion = $conexion_obj->conexion;

$sql = "SELECT i.id, i.identificacion, i.nombre, i.apellido, i.edad, i.sexo,
               p.nombre as pais_nombre, n.nombre as nacionalidad_nombre,
               i.correo, i.celular, i.observaciones, i.fecha_registro
        FROM inscriptores i
        LEFT JOIN paises p ON i.pais_residencia_id = p.id
        LEFT JOIN nacionalidades n ON i.nacionalidad_id = n.id
        ORDER BY i.fecha_registro DESC";

$resultado = $conexion->query($sql);
$inscritos = $resultado->fetch_all(MYSQLI_ASSOC);

foreach ($inscritos as &$inscrito) {
    $temas_sql = "SELECT a.nombre FROM inscriptor_temas it
                  JOIN areas_interes a ON it.area_interes_id = a.id
                  WHERE it.inscriptor_id = " . intval($inscrito['id']);
    $temas_res = $conexion->query($temas_sql);
    $temas = [];
    while ($t = $temas_res->fetch_assoc()) {
        $temas[] = $t['nombre'];
    }
    $inscrito['temas'] = implode(', ', $temas);
}
unset($inscrito);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Inscritos');

$spreadsheet->getProperties()
    ->setCreator('iTECH')
    ->setTitle('Reporte de Inscritos')
    ->setDescription('Reporte generado automáticamente');

// ── Cabeceras ──────────────────────────────────────────────────────────────
$columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];
$cabeceras = [
    'A' => ['texto' => 'ID',               'ancho' => 6],
    'B' => ['texto' => 'Identificación',   'ancho' => 18],
    'C' => ['texto' => 'Nombre',           'ancho' => 20],
    'D' => ['texto' => 'Apellido',         'ancho' => 20],
    'E' => ['texto' => 'Edad',             'ancho' => 8],
    'F' => ['texto' => 'Sexo',             'ancho' => 12],
    'G' => ['texto' => 'País',             'ancho' => 20],
    'H' => ['texto' => 'Nacionalidad',     'ancho' => 20],
    'I' => ['texto' => 'Correo',           'ancho' => 30],
    'J' => ['texto' => 'Celular',          'ancho' => 16],
    'K' => ['texto' => 'Áreas de Interés','ancho' => 35],
    'L' => ['texto' => 'Observaciones',   'ancho' => 35],
    'M' => ['texto' => 'Fecha Registro',  'ancho' => 22],
];

// Fila de título principal
$sheet->mergeCells('A1:M1');
$sheet->setCellValue('A1', 'Reporte de Inscritos - iTECH');
$sheet->getStyle('A1')->applyFromArray([
    'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1a3a5c']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER],
]);
$sheet->getRowDimension(1)->setRowHeight(32);

// Fila de cabeceras (fila 2)
foreach ($cabeceras as $col => $info) {
    $sheet->setCellValue($col . '2', $info['texto']);
    $sheet->getColumnDimension($col)->setWidth($info['ancho']);
}

$sheet->getStyle('A2:M2')->applyFromArray([
    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2e6da4']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                    'wrapText'   => true],
    'borders'   => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']],
    ],
]);
$sheet->getRowDimension(2)->setRowHeight(22);

// ── Datos ──────────────────────────────────────────────────────────────────
$fila = 3;
$coloresPares   = 'dce6f1';
$coloresImpares = 'FFFFFF';

foreach ($inscritos as $i => $inscrito) {
    $bgColor = ($i % 2 === 0) ? $coloresPares : $coloresImpares;

    $sheet->setCellValue('A' . $fila, $inscrito['id']);
    $sheet->setCellValue('B' . $fila, $inscrito['identificacion']);
    $sheet->setCellValue('C' . $fila, $inscrito['nombre']);
    $sheet->setCellValue('D' . $fila, $inscrito['apellido']);
    $sheet->setCellValue('E' . $fila, (int) $inscrito['edad']);
    $sheet->setCellValue('F' . $fila, $inscrito['sexo']);
    $sheet->setCellValue('G' . $fila, $inscrito['pais_nombre']);
    $sheet->setCellValue('H' . $fila, $inscrito['nacionalidad_nombre']);
    $sheet->setCellValue('I' . $fila, $inscrito['correo']);
    $sheet->setCellValue('J' . $fila, $inscrito['celular']);
    $sheet->setCellValue('K' . $fila, $inscrito['temas']);
    $sheet->setCellValue('L' . $fila, $inscrito['observaciones']);
    $sheet->setCellValue('M' . $fila, $inscrito['fecha_registro']);

    $sheet->getStyle('A' . $fila . ':M' . $fila)->applyFromArray([
        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        'borders'   => [
            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'c0c0c0']],
        ],
    ]);

    // Centrar columnas numéricas y cortas
    $sheet->getStyle('A' . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('E' . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('F' . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->getRowDimension($fila)->setRowHeight(18);
    $fila++;
}

// Fila de total
$totalFila = $fila;
$sheet->mergeCells('A' . $totalFila . ':D' . $totalFila);
$sheet->setCellValue('A' . $totalFila, 'Total de inscritos:');
$sheet->setCellValue('E' . $totalFila, count($inscritos));

$sheet->getStyle('A' . $totalFila . ':M' . $totalFila)->applyFromArray([
    'font'      => ['bold' => true],
    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1a3a5c']],
    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'borders'   => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']],
    ],
]);
$sheet->getStyle('A' . $totalFila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
$sheet->getStyle('E' . $totalFila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Fijar cabeceras al hacer scroll
$sheet->freezePane('A3');

// ── Descargar ──────────────────────────────────────────────────────────────
$nombre_archivo = 'reporte_inscritos_' . date('Y-m-d_H-i-s') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $nombre_archivo . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
