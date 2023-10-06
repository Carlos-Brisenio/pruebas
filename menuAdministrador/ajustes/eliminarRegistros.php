<?php
    $host = "localhost";
    $db_name = "dbMayordomia";
    $username = "root";
    $password = "";
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    
    // Suponiendo que el usuario autorizado es "admin" y la contraseña es "12345"
    $authorizedUser = "GABC990316HJCRRR09";
    $authorizedPassword = "GABC990316HJCRRR09";
    
    $user = $_POST['user'];
    $password = $_POST['password'];
    
    if ($user === $authorizedUser && $password === $authorizedPassword) {
        $stmt = $conn->prepare("UPDATE Boletos SET status = 1, fecha_Compra = NULL, fecha_Limite = NULL WHERE status IN (2, 3)");
        $stmt->execute();
        echo "Registros eliminados correctamente.";
        
    } else {
    echo "No se pueden ejecutar los cambios deseados.";
    }

    // Función para eliminar registros
    function eliminarRegistros() {
        global $conn;

        $stmt = $conn->prepare("UPDATE Boletos SET status = 1, fecha_Compra = NULL, fecha_Limite = NULL WHERE status IN (2, 3)");
        $stmt->execute();

        echo "Registros actualizados correctamente.";
    }

?>