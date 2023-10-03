<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Conexión a la base de datos
    $host = "localhost";
    $db_name = "dbMayordomia";
    $username = "root";
    $password = "";
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);

    $numeroBoleto = $_GET['numero_boleto'];

    // Actualizar la tabla Boletos
    $stmtBoleto = $conn->prepare("UPDATE Boletos SET status = 1 WHERE numero_boleto = ?");
    $stmtBoleto->execute([$numeroBoleto]);

    if ($stmtBoleto->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>