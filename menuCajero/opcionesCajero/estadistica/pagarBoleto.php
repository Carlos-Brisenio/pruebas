<?php
$host = "localhost";
$db_name = "dbMayordomia";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $numero_boleto = $_POST['numero_boleto'];
    $forma_pago = $_POST['forma_pago'];
    $recibe = $_POST['recibe']; // 👈 nuevo campo
    $precio = 180; // 👈 precio fijo por boleto

    // 🚨 Validación en servidor
    if ($recibe < $precio) {
        echo "Error: La cantidad recibida ($recibe) es insuficiente para cubrir el costo del boleto ($precio).";
        exit; // detenemos el script
    }

    $idUsuario = 1;
    date_default_timezone_set('America/Mexico_City');
    $fechaVenta = date("Y-m-d H:i:s", strtotime("-1 hour"));

    // Actualizar boleto
    $stmt = $conn->prepare("UPDATE Boletos SET status = 3 WHERE numero_boleto = :numero_boleto");
    $stmt->bindParam(':numero_boleto', $numero_boleto);
    $stmt->execute();

    // Registrar venta
    $stmtVentas = $conn->prepare("INSERT INTO Ventas (idBoletos, idUsuario, fecha_Venta, forma_pago) 
                                  VALUES (:idBoletos, :idUsuario, :fechaVenta, :forma_pago)");
    $stmtVentas->bindParam(':idBoletos', $numero_boleto);
    $stmtVentas->bindParam(':idUsuario', $idUsuario);
    $stmtVentas->bindParam(':fechaVenta', $fechaVenta);
    $stmtVentas->bindParam(':forma_pago', $forma_pago);
    $stmtVentas->execute();

    echo "Boleto pagado y registrado en la venta con éxito.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>