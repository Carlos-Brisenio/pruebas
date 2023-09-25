<?php
// archivo generarOrdenPago.php
require('/pruebas/menuUsuario/opcionesUsuario/boletos/fpdf.php');

// Conexión a la base de datos
$host = "localhost";
$db_name = "dbMayordomia";
$username = "root";
$password = "";
$conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);

// Crear instancia de PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);

// Consulta a la base de datos
$stmt = $conn->prepare("SELECT * FROM InfoBoletos WHERE idBoleto = ?");
$stmt->execute([$_GET['numeroBoleto']]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    // Aquí, puedes imprimir la información en el PDF como desees
    $pdf->Cell(40,10,'Nombre: ' . $result['nombre']);
    //... (agregar más información según lo requieras)
}

// Salida del PDF
$pdf->Output();
?>
