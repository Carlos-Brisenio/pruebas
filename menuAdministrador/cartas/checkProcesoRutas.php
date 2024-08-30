<?php
$host = "localhost";
$db_name = "dbMayordomia";
$username = "root";
$password = "";
$conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);

// Obtener el proceso enviado por POST
$proceso = isset($_POST['proceso']) ? intval($_POST['proceso']) : null;

if ($proceso !== null) {
    // Consulta para verificar si el proceso ya existe
    $stmt = $conn->prepare("SELECT COUNT(*) FROM Rutas WHERE proceso = :proceso");
    $stmt->bindParam(':proceso', $proceso, PDO::PARAM_INT);
    $stmt->execute();

    $count = $stmt->fetchColumn();

    // Retornar si el proceso ya existe o no
    if ($count > 0) {
        echo "El proceso ya se encuentra registrado.";
    } else {
        echo "Proceso disponible";
    }
} else {
    echo "Proceso no proporcionado.";
}
?>