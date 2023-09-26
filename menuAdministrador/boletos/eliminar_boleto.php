<?php
// Establecer la conexión a la base de datos (asegúrate de tener las credenciales adecuadas)
$host = "localhost";
$db_name = "dbmayordomia";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener el número de boleto desde la solicitud POST
    $numero_boleto = $_POST['numero_boleto'];

    // Actualizar el estado del boleto a "Disponible" (status 1) y eliminar datos de infoBoletos
    $stmt = $conn->prepare("UPDATE boletos SET status = 1, fecha_Compra = NULL, fecha_Limite = NULL WHERE numero_boleto = :numero_boleto");
    $stmt->bindParam(':numero_boleto', $numero_boleto);
    $stmt->execute();

    // Eliminar datos de infoBoletos relacionados con el boleto
    $stmtInfoBoletos = $conn->prepare("DELETE FROM infoBoletos WHERE idBoleto = :idBoleto");
    $stmtInfoBoletos->bindParam(':idBoleto', $numero_boleto);
    $stmtInfoBoletos->execute();

    echo "Boleto eliminado con éxito.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

