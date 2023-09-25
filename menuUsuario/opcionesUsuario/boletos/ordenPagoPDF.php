<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require '../pdf/fpdf.php';

// Conexión a la base de datos
$host = "localhost";
$db_name = "dbMayordomia";
$username = "root";
$password = "";
$conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Recuperar el número de boleto desde la URL
if(isset($_GET['numeroBoleto'])) {
    $numeroBoleto = $_GET['numeroBoleto'];

    // Realizar consulta a la base de datos
    $stmt = $conn->prepare("SELECT * FROM InfoBoletos WHERE idBoleto = ?");
    $stmt->execute([$numeroBoleto]);
    $boletoInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "Antes de generar el PDF.<br>";

    if($boletoInfo) {
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        $pdf->Cell(40, 10, 'Boleto: ' . $boletoInfo['nombre']);
        $pdf->Ln();  // Nueva línea
        $pdf->Cell(40, 10, 'Ciudad: ' . $boletoInfo['ciudad']);
        $pdf->Ln();
        // Puedes continuar agregando más datos aquí

        $pdf->Output();
        echo "Después de generar el PDF.";
    } else {
        echo "Error: No se encontró información para el boleto.";
    }
} else {
    echo "Error: No se especificó número de boleto.";
}
?>