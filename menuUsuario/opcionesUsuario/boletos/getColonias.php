<?php
header('Content-Type: application/json');

// Conexión a la base de datos
$host = "localhost";
$db_name = "dbMayordomia";
$username = "root";
$password = "";
$conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);

$cp = $_GET['codigo_postal'];

// Consulta para obtener las colonias según el código postal
$query = "SELECT nombre FROM Colonias WHERE codigo_postal = :cp";
$stmt = $conn->prepare($query);
$stmt->bindParam(':cp', $cp);
$stmt->execute();

$colonias = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

echo json_encode($colonias);
?>