<?php
// Conexión a la base de datos
$host = "localhost";
$db_name = "dbMayordomia";
$username = "root";
$password = "";
$conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);

// Manejar solicitud AJAX para autocompletado calles existentes
if (isset($_GET['term'])) {
    $term = $_GET['term'];
    
    // Consulta actualizada para evitar duplicados usando DISTINCT
    $query = "SELECT DISTINCT idCalle, nombre_Calle 
              FROM Calles 
              WHERE nombre_Calle LIKE :term";
    
    $stmt = $conn->prepare($query);
    $stmt->execute(['term' => '%' . $term . '%']);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $suggestions = [];
    foreach ($results as $row) {
        // Mantener solo el nombre de la calle
        $nombreCalle = $row['nombre_Calle'];
        $suggestions[] = [
            'label' => $nombreCalle,  // Mostrar solo el nombre de la calle
            'value' => $nombreCalle,  // Usar el nombre de la calle como valor
            'idCalle' => $row['idCalle']  // Devolver el ID de la calle si es necesario
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($suggestions);
    exit;
}
?>

