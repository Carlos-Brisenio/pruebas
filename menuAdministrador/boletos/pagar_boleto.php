<?php
// Establecer la conexión a la base de datos (asegúrate de tener las credenciales adecuadas)
    $host = "localhost";
    $db_name = "u833492021_dbMayordomia";
    $username = "u833492021_root";
    $password = "#kDbV9r>9UJ5";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener el número de boleto desde la solicitud POST
    $numero_boleto = $_POST['numero_boleto'];

    // Obtener el ID de usuario de la sesión (reemplaza esto con tu lógica de autenticación)
    $idUsuario = 1; // Reemplaza con la lógica para obtener el ID del usuario de la sesión

    // Obtener la fecha actual
    $fechaVenta = date("Y-m-d H:i:s");

    // Actualizar el estado del boleto a "Vendido" (status 3)
    $stmt = $conn->prepare("UPDATE Boletos SET status = 3 WHERE numero_boleto = :numero_boleto");
    $stmt->bindParam(':numero_boleto', $numero_boleto);
    $stmt->execute();

    // Insertar la venta en la tabla 'ventas'
    $stmtVentas = $conn->prepare("INSERT INTO Ventas (idBoletos, idUsuario, fecha_Venta) VALUES (:idBoletos, :idUsuario, :fechaVenta)");
    $stmtVentas->bindParam(':idBoletos', $numero_boleto);
    $stmtVentas->bindParam(':idUsuario', $idUsuario);
    $stmtVentas->bindParam(':fechaVenta', $fechaVenta);
    $stmtVentas->execute();

    echo "Boleto pagado y registrado en la venta con éxito.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
s