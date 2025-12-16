<?php
require_once 'libs/fpdf.php';

// Crear un PDF simple para probar
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(40,10,'¡Hola Mundo!');
$pdf->Output('F', 'test.pdf');

echo "PDF de prueba generado correctamente en test.pdf";
?>
