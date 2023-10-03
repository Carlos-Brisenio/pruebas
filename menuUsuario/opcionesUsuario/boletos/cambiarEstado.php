<?php
$host = "localhost";
$db_name = "dbMayordomia";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["numero_boleto"])) {
    $numero_boleto = $_POST["numero_boleto"];
    
    $query = "UPDATE Boletos SET status = 2 WHERE numero_boleto = :numero_boleto";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":numero_boleto", $numero_boleto, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "invalid_request";
}
?>
