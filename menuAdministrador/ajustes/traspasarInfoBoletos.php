<?php
$host = "localhost";
$db_name = "dbMayordomia";
$username = "root";
$password = "";

// Conexión a la base de datos
try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit();
}

// Datos de autenticación
$authorizedUser = "GABC990316HJCRRR09";
$authorizedPassword = "GABC990316HJCRRR09";

// Obtener datos del formulario
$user = $_POST['user'];
$password = $_POST['password'];
$proceso = isset($_POST['proceso']) ? intval($_POST['proceso']) : 0; // Obtener valor del campo proceso

if ($user === $authorizedUser && $password === $authorizedPassword) {
    // Verifica que el campo proceso no esté vacío y sea un número positivo
    if ($proceso > 0) {
        try {
            // Preparar la consulta para insertar datos en la tabla Historico
            $stmt = $conn->prepare("
                INSERT INTO Historico (
                    idInfoBoletos, idBoleto, nombre, ciudad, colonia, calle, numero, colinda1, colinda2, referencia, telefono1, telefono2, correo_Electronico, proceso
                )
                SELECT
                    CONCAT(idInfoBoletos, '-', :proceso) AS idInfoBoletos, 
                    idBoleto, nombre, ciudad, colonia, calle, numero, colinda1, colinda2, referencia, telefono1, telefono2, correo_Electronico, :proceso
                FROM
                    InfoBoletos
            ");

            $stmt->bindParam(':proceso', $proceso, PDO::PARAM_INT);
            $stmt->execute();

            //Inserción de Ventas a historicoVentas
            $stmtV = $conn->prepare("
                INSERT INTO HistoricoVentas (
                    idVenta, idBoletos, idUsuario, fecha_Venta, proceso
                )
                SELECT
                    CONCAT(idVenta, '-', :proceso) AS idVenta, 
                    idBoletos, idUsuario, fecha_Venta, :proceso
                FROM
                    Ventas
            ");

            $stmtV->bindParam(':proceso', $proceso, PDO::PARAM_INT);
            $stmtV->execute();

            //Inserción de Boletos a HistoricoBoletos
            $stmtB = $conn->prepare("
                INSERT INTO HistoricoBoletos (
                    idBoleto, numero_boleto, status, fecha_Compra, fecha_Limite, proceso
                )
                SELECT
                    CONCAT(idBoleto, '-', :proceso) AS idBoleto, 
                    numero_boleto, status, fecha_Compra, fecha_Limite, :proceso
                FROM
                    Boletos
            ");

            $stmtB->bindParam(':proceso', $proceso, PDO::PARAM_INT);
            $stmtB->execute();

            echo "El traspaso a Historico se ha ejecutado correctamente.";
        } catch (PDOException $e) {
            echo "Error al insertar datos: " . $e->getMessage();
        }
    } else {
        echo "El valor del campo proceso debe ser un número positivo.";
    }
} else {
    echo "No se pueden ejecutar los cambios deseados.";
}
?>