<?php
// Conexión a la base de datos
$host = "localhost";
$db_name = "dbMayordomia";
$username = "root";
$password = "";
$conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);

// Manejar solicitud AJAX para autocompletado (ORIGINAL)
if (isset($_GET['term'])) {
    $term = $_GET['term'];
    $query = "SELECT nombre, colonia, calle, numero, colinda1, colinda2, referencia, telefono1, telefono2, correo_Electronico 
              FROM Historico 
              WHERE calle LIKE :term";
    
    $stmt = $conn->prepare($query);
    $stmt->execute(['term' => '%' . $term . '%']);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $suggestions = [];
    foreach ($results as $row) {
        // Concatenar calle y número en el formato deseado
        $calleNumero = $row['calle'] . ' #' . $row['numero'] . ', ' . $row['nombre'];
        $suggestions[] = [
            'label' => $calleNumero,  // Mostrar calle y número
            'value' => $calleNumero,  // Usar calle y número como valor
            'data' => $row            // Mantener el resto de los datos
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($suggestions);
    exit;
} 

/*try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Manejar solicitud AJAX para autocompletado
    if (isset($_GET['term'])) {
        $term = $_GET['term'];
        $query = "
            SELECT h.nombre, c.nombre AS colonia, c.codigo_postal, h.calle, h.numero, 
                   h.colinda1, h.colinda2, h.referencia, h.telefono1, h.telefono2, h.correo_Electronico
            FROM Historico h
            LEFT JOIN Colonias c ON h.colonia = c.idColonia
            WHERE h.calle LIKE :term
        ";

        $stmt = $conn->prepare($query);
        $stmt->execute(['term' => '%' . $term . '%']);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $suggestions = [];
        foreach ($results as $row) {
            $calleNumero = $row['calle'] . ' #' . $row['numero'] . ', ' . $row['nombre'];
            $suggestions[] = [
                'label' => $calleNumero,
                'value' => $calleNumero,
                'data'  => $row
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($suggestions);
        exit;
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}*/
?>
