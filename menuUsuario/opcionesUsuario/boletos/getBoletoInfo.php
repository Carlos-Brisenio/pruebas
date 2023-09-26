<?php
header('Content-Type: application/json');

$host = "localhost";
$db_name = "dbMayordomia";
$username = "root";
$password = "";
$conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);

if(isset($_GET['numeroBoleto'])) {
    $numeroBoleto = $_GET['numeroBoleto'];

    $stmt = $conn->prepare("SELECT * FROM InfoBoletos WHERE idBoleto = ?");
    $stmt->execute([$numeroBoleto]);
    $boletoInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    if($boletoInfo) {
        echo json_encode($boletoInfo);
    } else {
        echo json_encode(['error' => 'No se encontró información para el boleto.']);
    }
} else {
    echo json_encode(['error' => 'No se especificó número de boleto.']);
}
?>