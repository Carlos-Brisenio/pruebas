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
    echo "Error de conexión: " . $e->getMessage();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['rutas'])) {
        $rutas = $_POST['rutas'];
        foreach ($rutas as $ruta) {
            $stmt = $conn->prepare("UPDATE Rutas SET ruta = :ruta, recorrido = :recorrido WHERE idRutas = :idRutas");
            $stmt->bindParam(':ruta', $ruta['ruta'], PDO::PARAM_INT);
            $stmt->bindParam(':recorrido', $ruta['recorrido'], PDO::PARAM_INT);
            $stmt->bindParam(':idRutas', $ruta['idRutas'], PDO::PARAM_INT);

            if (!$stmt->execute()) {
                echo "Error al actualizar la ruta con ID " . $ruta['idRutas'];
                exit;
            }
        }
        echo "Cambios guardados exitosamente";
    } else {
        echo "No se recibieron datos para actualizar.";
    }
}
?>