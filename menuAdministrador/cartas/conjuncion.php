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
$inputPassword = $_POST['password'];
$proceso = isset($_POST['proceso']) ? intval($_POST['proceso']) : 0; // Obtener valor del campo proceso

if ($user === $authorizedUser && $inputPassword === $authorizedPassword) {
    // Verifica que el campo proceso no esté vacío y sea un número positivo
    if ($proceso > 0) {
        try {
            // Primero, inserta en la tabla Rutas
            $queryInsert = "
                SELECT CONCAT(calle, ' #', numero, ', ', colonia) AS domicilio,
                       GROUP_CONCAT(DISTINCT nombre ORDER BY nombre SEPARATOR ', ') AS nombres
                FROM Historico
                GROUP BY calle, numero, colonia
            ";
            $resultInsert = $conn->query($queryInsert);

            $insertRouteStmt = $conn->prepare("
                INSERT INTO Rutas (ruta, recorrido, nombres, domicilio, numeroBoletos, proceso)
                VALUES (0, 0, :nombres, :domicilio, NULL, :proceso)
            ");

            $insertRouteStmt->bindParam(':nombres', $nombres, PDO::PARAM_STR);
            $insertRouteStmt->bindParam(':domicilio', $domicilio, PDO::PARAM_STR);
            $insertRouteStmt->bindParam(':proceso', $proceso, PDO::PARAM_INT);

            foreach ($resultInsert as $row) {
                // Asigna los valores a las variables
                $nombres = $row['nombres'];
                $domicilio = $row['domicilio'];

                $insertRouteStmt->execute();
            }

            // Ahora, actualiza la columna numeroBoletos
            $queryUpdate = "
                SELECT CONCAT(calle, ' #', numero, ', ', colonia) AS domicilio,
                       COUNT(*) AS repetidos
                FROM Historico
                GROUP BY calle, numero, colonia
                HAVING COUNT(*) > 0
            ";
            $resultUpdate = $conn->query($queryUpdate);

            $updateRouteStmt = $conn->prepare("
                UPDATE Rutas
                SET numeroBoletos = :numeroBoletos
                WHERE domicilio = :domicilio
            ");

            $updateRouteStmt->bindParam(':domicilio', $domicilio, PDO::PARAM_STR);
            $updateRouteStmt->bindParam(':numeroBoletos', $numeroBoletos, PDO::PARAM_INT);

            foreach ($resultUpdate as $row) {
                // Asigna los valores a las variables
                $domicilio = $row['domicilio'];
                $numeroBoletos = $row['repetidos'];

                $updateRouteStmt->execute();
            }

            echo "El traspaso a Historico y la actualización de rutas se ha ejecutado correctamente.";
        } catch (PDOException $e) {
            echo "Error al insertar o actualizar datos en la tabla Rutas: " . $e->getMessage();
        }
    } else {
        echo "El valor del campo proceso debe ser un número positivo.";
    }
} else {
    echo "No se pueden ejecutar los cambios deseados.";
}
?>