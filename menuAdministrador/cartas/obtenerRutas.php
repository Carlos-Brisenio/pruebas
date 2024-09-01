<?php
// Conexión a la base de datos
$host = "localhost";
$db_name = "dbMayordomia";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]);
    exit;
}

if (isset($_POST['ruta'])) {
    $rutaSeleccionada = $_POST['ruta'];

    $query = "SELECT idRutas, ruta, recorrido, nombres, domicilio, numeroBoletos FROM Rutas WHERE ruta = :ruta";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':ruta', $rutaSeleccionada, PDO::PARAM_INT);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($resultados);
}
?>
