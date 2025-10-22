<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['numero_boleto'])) {
    $numero_boleto = $_POST['numero_boleto'];

    try {
        $host = "localhost";
        $db_name = "dbMayordomia";
        $username = "root";
        $password = "";
        $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
            SELECT 
                b.idBoleto,
                b.numero_boleto,
                i.nombre,
                CONCAT(i.calle, ' ', i.numero, ', ', i.colonia, ', ', i.ciudad) AS domicilio
            FROM Boletos b
            INNER JOIN InfoBoletos i ON b.idBoleto = i.idBoleto
            WHERE b.numero_boleto = :numero_boleto AND b.status = 2
        ";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':numero_boleto', $numero_boleto);
        $stmt->execute();

        $boleto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($boleto) {
            $boleto['precio'] = 180;
            echo json_encode(['success' => true, 'data' => $boleto]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Boleto no encontrado o no disponible (status ≠ 2).']);
        }

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $e->getMessage()]);
    }
}
?>
