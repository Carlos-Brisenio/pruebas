<?php
    $host = "localhost";
    $db_name = "dbMayordomia";
    $username = "root";
    $password = "";

    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "SELECT numero_boleto, status FROM Boletos";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    $results = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $results[$row['numero_boleto']] = $row['status'];
    }

    echo json_encode($results);
?>
