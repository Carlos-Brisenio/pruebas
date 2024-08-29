<?php
    $host = "localhost";
    $db_name = "dbMayordomia";
    $username = "root";
    $password = "";
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    
    $conn->exec("SET time_zone='+06:00'"); // Configura la zona horaria de la conexión.
    
    if (isset($_POST['startDate'])) {
        $startDate = $_POST['startDate'] . " 00:00:00";
        $endDate = isset($_POST['endDate']) ? $_POST['endDate'] . " 23:59:59" : $startDate;
    
        $queryBoletosVendidos = "
            SELECT 
                Boletos.numero_boleto, InfoBoletos.nombre, InfoBoletos.telefono1, InfoBoletos.telefono2, InfoBoletos.colonia, InfoBoletos.calle, InfoBoletos.numero, InfoBoletos.referencia, 
                Boletos.fecha_Compra, Boletos.fecha_Limite, Ventas.idVenta, Ventas.idUsuario, Ventas.fecha_venta
            FROM Boletos 
            INNER JOIN InfoBoletos ON Boletos.numero_boleto = InfoBoletos.idBoleto
            INNER JOIN Ventas ON Boletos.numero_boleto = Ventas.idBoletos
            WHERE Boletos.status = 3 AND Ventas.fecha_venta BETWEEN :startDate AND :endDate";
    
        $stmtBoletosVendidos = $conn->prepare($queryBoletosVendidos);
        $stmtBoletosVendidos->bindParam(':startDate', $startDate);
        $stmtBoletosVendidos->bindParam(':endDate', $endDate);
        $stmtBoletosVendidos->execute();
    
        $boletosVendidos = $stmtBoletosVendidos->fetchAll(PDO::FETCH_ASSOC);
    
        echo json_encode($boletosVendidos); // Retornamos los datos en formato JSON
    }
?>