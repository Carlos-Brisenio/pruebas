<?php
    require '/pruebas/fpdf/fpdf.php';

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',15);

    $pdf->Cell(100, 10, 'Hola Mundo', 1, 0, 'C');

    $pdf->Output();
?>