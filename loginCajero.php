<?php
// Conexión a la base de datos
$host = "localhost";
$db_name = "dbMayordomia";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "SELECT idTipoUsuario FROM Usuarios WHERE usuario = '".$_POST['usuario']."' AND password = '".$_POST['password']."'";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Si";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    die();
}

// Función para obtener el tipo de usuario
/*function obtenerTipoUsuario($conn, $usuario, $password) {
    $query = "SELECT idTipoUsuario FROM Usuarios WHERE usuario = '.$usuario.' AND password = '.$password.'";
    $stmt = $conn->prepare($query);
    /*$stmt->bindParam('usuario', $usuario);
    $stmt->bindParam('password', $password);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['idTipoUsuario'] ?? false;
    echo 1;
}*/

// Verificar el inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["user"];
    $password = $_POST["password"]; // Supongamos que almacenas contraseñas en formato MD5

    // Obtener el tipo de usuario
    $tipoUsuario = obtenerTipoUsuario($conn, $usuario, $password);

    if ($tipoUsuario !== false) {
        if ($tipoUsuario == 2) { // Modificado aquí para permitir solo idTipoUsuario 2
            // Redirigir al usuario con permisos
            header("Location: menuCajero/indexCajero.html");
            exit();
        } else {
            // Mostrar mensaje de falta de permisos
            $mensaje = "Este usuario no cuenta con permisos suficientes para iniciar sesión.";
        }
    } else {
        // Mostrar mensaje de usuario o contraseña incorrectos
        $mensaje = "Usuario o contraseña incorrectos.";
    }
}
?>