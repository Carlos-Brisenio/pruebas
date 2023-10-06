<?php
    $host = "localhost";
    $db_name = "dbMayordomia";
    $username = "root";
    $password = "";
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);

    $authorizedUser = "GABC990316HJCRRR09";
    $authorizedPassword = "GABC990316HJCRRR09";

    $user = $_POST['user'];
    $password = $_POST['password'];

    if ($user === $authorizedUser && $password === $authorizedPassword) {
        // Modificación aquí: actualización de registros en la tabla Boletos
        $stmt = $conn->prepare("
            UPDATE Boletos 
            SET status = 1 
            WHERE status = 2 
            AND fecha_Compra IS NULL 
            AND fecha_Limite IS NULL 
            AND idBoleto NOT IN (SELECT idBoleto FROM InfoBoletos)
        ");
        $stmt->execute();
        echo "Status de boletos actualizado correctamente.";
    } else {
        echo "No se pueden ejecutar los cambios deseados.";
    }
?>

