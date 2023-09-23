<?php
if(isset($_POST['numero_boleto'])) {
    $numeroBoleto = $_POST['numero_boleto'];

    // Tu conexión aquí
    $host = "localhost";
    $db_name = "dbMayordomia";
    $username = "root";
    $password = "";

    try {
        $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "UPDATE Boletos SET status = 2 WHERE numero_boleto = :numero_boleto";  // Cambiando status a 2, por ejemplo
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':numero_boleto', $numeroBoleto);
        $stmt->execute();

        echo "Boleto actualizado";
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
