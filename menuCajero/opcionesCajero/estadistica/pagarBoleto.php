<?php
// Establecer la conexión a la base de datos (asegúrate de tener las credenciales adecuadas)
$host = "localhost";
$db_name = "dbMayordomia";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener el número de boleto desde la solicitud POST
    $numero_boleto = $_POST['numero_boleto'];

    // Obtener el ID de usuario de la sesión (reemplaza esto con tu lógica de autenticación)
    $idUsuario = 1; // Reemplaza con la lógica para obtener el ID del usuario de la sesión

    // Obtener la fecha actual
    date_default_timezone_set('America/Mexico_City');
    $fechaVenta = date("Y-m-d H:i:s", strtotime("-1 hour"));
    //strtotime("-1 hour")); funciona para que el registro de venta se guarde de manera correcta en el sistema
    //$fechaVenta = date("Y-m-d H:i:s"); //tiene como funcion de obtener la hora y fecha de méxico




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