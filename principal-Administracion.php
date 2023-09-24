<?php
// Conexión a la base de datos
$host = "localhost";
$db_name = "dbMayordomia";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    die();
}

// Función para obtener el tipo de usuario
function obtenerTipoUsuario($conn, $usuario, $password) {
    $query = "SELECT idTipoUsuario FROM Usuarios WHERE usuario = '".$usuario."' AND password = '".$password."'";
    //echo $query;
    $stmt = $conn->prepare($query);
    $stmt->execute();    
    @$result = $stmt->fetch(PDO::FETCH_ASSOC);    
    return @$result['idTipoUsuario'];
}

// Verificar el inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {    
    $usuario = $_POST["userAdmin"];
    $password = $_POST["passwordAdmin"]; // Supongamos que almacenas contraseñas en formato MD5

    // Obtener el tipo de usuario
    $tipoUsuario = obtenerTipoUsuario($conn, $usuario, $password);
    echo $tipoUsuario;
    if ($tipoUsuario != "") {
        if ($tipoUsuario == 1) { // Modificado aquí para permitir solo idTipoUsuario 1
            // Redirigir al usuario con permisos
            header("Location: menuAdministrador/indexAdministrador.html");
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
<!DOCTYPE html>
<html>
<head>
    <meta name="robots" content="noindex">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="google" content="notranslate">
    <meta name="csrf-token" content="KrT0K4JDi7KSb2c43qIg4ffxdybnDXB6ru5JXNjT">
	<title>Ticket-Mayordomia©</title>
    <link rel='shortcut icon' type='image/x-icon' href='/favicon.ico'>
    <link rel="stylesheet" href="style.css"></head>
<body>
	<div class="container-fluid bg-gray-100" id="main-container">
		<div class="flex flex-col items-center justify-center h-screen w-full bg-gray-100">
    <div class="flex flex-col md:flex-row w-full md:w-2/3 h-full md:h-auto bg-white rounded-lg md:shadow px-2 py-12 md:px-8 md:py-8">
        <div class="hidden md:flex md:flex-col justify-center w-1/3 mr-12 pl-6">
            <img src="ticketMayordomia.png" alt="MindBox" class="max-w-full">
            <br>
            <img src="bannerV2.png" alt="Login" class="max-w-full">
            <br>
            <p class="text-center text-xs">
                <a target="_blank">Ticket-Mayordomia©</a><br>Todos los derechos reservados © 2023.
                <br>Un producto de DreamCreatorss</a>.
            </p>
        </div>
        <div class="flex flex-col items-center w-full md:w-2/3">
            <img src="banderinLogo.png" alt="Logo" class="max-w-full h-24">
            <br>
            <ul class="nav nav-pills-2 nav-center">
                <li role="presentation"><a href="index.html">Usuarios</a></li>
                <li role="presentation"><a href="principal-Cajeros.php">Cajeros</a></li>
                <li role="presentation" class="active"><a href="principal-Administracion.php">Administración</a></li>
            </ul>
            <form action="<?php $_SERVER['PHP_SELF']?>" method="POST" id="login" class="w-full md:w-2/3 p-4">
                <input type="hidden" name="_token">                <div class="row">
                    <div class="col-12">
                        <div class="form-group ">
                            <label for="nsolicitud" class="control-label">Usuario</label>
                            <input type="text" name="userAdmin" id="userAdmin" class="form-control" required>
                        </div>
                        <div class="form-group ">
                            <label for="password" class="control-label">Contraseña</label>
                            <input type="password" name="passwordAdmin" id="passwordAdmin" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">Iniciar sesión</button>
                        </div>
                    </div>
                </div>
            </form>
            <?php
                if (isset($mensaje)) {
                    echo '<div class="alert alert-danger">' . $mensaje . '</div>';
                }
                ?>
            <div class="inline-block">
                <p class="flex flex-col text-center text-xs flex md:hidden">
                        <img src="banderinLogo.png" alt="Ticket-Mayordomia©" class="w-32 mx-auto">
                    </a>
                    Todos los derechos reservados © 2023.
                </p>
            </div>
        </div>
    </div>
</div>
	</div>
	<div id="notify" class="notifxi-alert"></div>
</body>
</html>
