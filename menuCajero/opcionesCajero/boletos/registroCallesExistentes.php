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
    
    // Nueva consulta utilizando la tabla Calles
    $query = "SELECT idCalle, nombre_Calle 
              FROM Calles 
              WHERE nombre_Calle LIKE :term";
    $stmt = $conn->prepare($query);
    $stmt->execute(['term' => '%' . $term . '%']);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $suggestions = [];
    foreach ($results as $row) {
        // Formato de autocompletado basado en el nombre de la calle
        $calleNombre = $row['nombre_Calle'];
        $suggestions[] = [
            'label' => $calleNombre,  // Mostrar la calle con formato 'C. nombre_Calle'
            'value' => $calleNombre,  // Usar el nombre de la calle como valor
            'idCalle' => $row['idCalle']  // Mantener el idCalle si lo necesitas para otras operaciones
        ];
    }

    // Retornar resultados como JSON
    header('Content-Type: application/json');
    echo json_encode($suggestions);
    exit;
}
?>
