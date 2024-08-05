<?php
// Conexión a la base de datos este es para la tabla historial de clientes
    $host = "localhost";
    $db_name = "dbMayordomia";
    $username = "root";
    $password = "";
$conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);

// Manejar solicitud AJAX para autocompletado
if (isset($_GET['term'])) {
    $term = $_GET['term'];
    $query = "SELECT nombre, colonia, calle, numero, colinda1, colinda2, referencia, telefono1, telefono2, correo_Electronico 
              FROM Historico 
              WHERE nombre LIKE :term";
    $stmt = $conn->prepare($query);
    $stmt->execute(['term' => '%' . $term . '%']);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $suggestions = [];
    foreach ($results as $row) {
        $suggestions[] = [
            'label' => $row['nombre'],
            'value' => $row['nombre'],
            'data' => $row
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($suggestions);
    exit;
}
?>