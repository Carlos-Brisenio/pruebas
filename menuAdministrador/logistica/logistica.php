<?php

    //session_start();
    /*if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["isLoggedIn"] !== true) {
        header("Location: /pruebas/principal-Logistica.php"); // Reemplaza 'login.php' con el nombre de tu archivo de inicio de sesión si es diferente
        exit;
    }*/

// Conexión a la base de datos
    $host = "localhost";
    $db_name = "dbMayordomia";
    $username = "root";
    $password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}

/*/ Función para truncar el campo nombres
    function truncarNombres($nombre, $maxLength = 45) {
        if (mb_strlen($nombre) > $maxLength) {
            return mb_substr($nombre, 0, $maxLength) . '...';
        }
        return $nombre;
    }

// Consulta para obtener rutas
    $queryRutasTable = "
        SELECT
            idRutas, 
            ruta,
            recorrido,
            nombres,
            domicilio,
            numeroBoletos,
            proceso
        FROM Rutas";
    
    $stmtRutasTable = $conn->prepare($queryRutasTable);
    $stmtRutasTable->execute();
    $rutasTable = $stmtRutasTable->fetchAll(PDO::FETCH_ASSOC);
    
    // Aplicar la función de truncado a cada nombre en los resultados
    $rutasTable = array_map(function($row) {
        $row['nombres'] = truncarNombres($row['nombres']);
        return $row;
    }, $rutasTable);

    // Consulta para obtener las rutas
    //$queryRutasExisten = "SELECT ruta FROM Rutas";
    $queryRutasExisten = "SELECT DISTINCT ruta FROM Rutas ORDER BY ruta ASC";
    $stmtRutasExisten = $conn->prepare($queryRutasExisten);
    $stmtRutasExisten->execute();
    $rutas = $stmtRutasExisten->fetchAll(PDO::FETCH_COLUMN);*/

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!----======== CSS ======== -->
    <link rel="stylesheet" href="/pruebas/menuUsuario/styleMenu.css">
    <link rel="stylesheet" href="stylesLog.css">
    
    <!----===== Boxicons CSS ===== -->
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    
    <!----===== Actualizador de procesos ===== -->
    <script src="/pruebas/menuCajero/actualizadorProceso.js"></script>
    
    <!-- Tabla de rutas -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    
    <!-- Agrega un elemento canvas para el gráfico -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

 
    <title>Mayordomía Tickets®/Log&iacute;stica</title> 
</head>
<body>
    <nav class="sidebar close">
        <header>
            <div class="image-text">
                <span class="image">
                    <img src="/pruebas/menuUsuario/logoTM.png" alt="">
                </span>

                <div class="text logo-text">
                	<span class="name">ADMINISTRADOR</span>
                 	<span class="profession" id="proceso-span">PROCESO</span>
                </div>
            </div>

            <i class='bx bx-chevron-right toggle'></i>
        </header>

        <div class="menu-bar">
            <div class="menu">

                <li class="search-box">
                    <i class='bx bx-search icon'></i>
                    <input type="text" placeholder="Buscar...">
                </li>

                <ul class="menu-links">
                    <li class="nav-link">
                        <a href="/pruebas/menuAdministrador/indexAdministrador.php">
                            <i class='bx bx-home-alt icon' ></i>
                            <span class="text nav-text">Inicio</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="/pruebas/menuAdministrador/datos/datosBoletos.php">
                            <i class='bx bx-data icon'></i>
                            <span class="text nav-text">Datos</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="/pruebas/menuAdministrador/usuarios/Usuarios.php">
                            <i class='bx bx-user icon'></i>
                            <span class="text nav-text">Usuarios</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="/pruebas/menuAdministrador/boletos/adminboletos.php">
                            <i class='bx bx-purchase-tag-alt icon'></i>
                            <span class="text nav-text">Boletos</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="/pruebas/menuAdministrador/estadisticas/estadisticas.php">
                            <i class='bx bx-bar-chart-square icon'></i>
                            <span class="text nav-text">Estadisticas</span>
                        </a>
                    </li>
                    
                    <li class="nav-link">
                        <a href="/pruebas/menuAdministrador/cartas/cartas.php">
                            <i class='bx bx-envelope icon'></i>
                            <span class="text nav-text">Cartas<br>Decimas</span>
                        </a>
                    </li>
                    <li class="nav-link">
                            <a href="/pruebas/menuAdministrador/logistica/logistica.php">
                            <i class='bx bxs-truck icon'></i>
                            <span class="text nav-text">log&iacute;stica</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="/pruebas/menuAdministrador/ajustes/moduloAjustes.php">
                            <i class='bx bx-cog icon' ></i>
                            <span class="text nav-text">Ajustes</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="bottom-content">
                <li class="">
                    <a href="/pruebas/menuAdministrador/logout.php">
                        <i class='bx bx-log-out icon' ></i>
                        <span class="text nav-text">Salir</span>
                    </a>
                </li>

                <li class="mode">
                    <div class="sun-moon">
                        <i class='bx bx-moon icon moon'></i>
                        <i class='bx bx-sun icon sun'></i>
                    </div>
                    <span class="mode-text text">Modo oscuro</span>

                    <div class="toggle-switch">
                        <span class="switch"></span>
                    </div>
                </li>
                
            </div>
        </div>

    </nav>
   
        
    <section class="home">
        <div class="section">
            <section class="logistica">
                <h1>Logística de entrega de cartas y décimas</h1>
                <br>
            </section>
                <div class="container">
                    
                    <table id="rutasTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="display:none;">idRutas</th>
                                <th style="display:none;">RUTAS<i class='bx bx-cycling icon'></i></th>
                                <th style="display:none;">RECORRIDO <i class='bx bx-trip icon'></th>
                                <th>NOMBRES</th>
                                <th>DOMICILIO</th>
                                <th>N° BOLETOS COMPRADOS</th>
                                <th>PROCESO</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rutasTable as $ruta): ?>
                                <tr>
                                    <td class="idRutas" style="display:none;"><?= htmlspecialchars($ruta['idRutas']) ?></td>
                                    <td style="display:none;"><input type="text" name="ruta" value="<?= htmlspecialchars($ruta['ruta']) ?>" style="width: 100px;" disabled></td>
                                    <td style="display:none;"><input type="text" name="recorrido" value="<?= htmlspecialchars($ruta['recorrido']) ?>" style="width: 100px;" disabled></td>
                                    <td><?= htmlspecialchars($ruta['nombres']) ?></td>
                                    <td><?= htmlspecialchars($ruta['domicilio']) ?></td>
                                    <td><?= htmlspecialchars($ruta['numeroBoletos']) ?></td>
                                    <td><?= htmlspecialchars($ruta['proceso']) ?></td>
                                    <td><button><i class='bx bx-check'></i></button><button style="background:red"><i class='bx bx-x'></i></button></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        
                    </table>
                </div>

        </div>
    </section>
    <script src="/pruebas/menuUsuario/script.js"></script>
    <script src="/pruebas/menuAdministrador/autologout.js"></script>
    <script>
        $(document).ready(function() {
    var rutasTable = $('#rutasTable').DataTable({
        "searching": true,
        "searchMinLength": 1
    });

    // Búsqueda global personalizada
    $('#searchVendidos').on('keyup', function() {
        rutasTable.search(this.value).draw();
    });

    // Filtrar por ruta (columna 1, que tiene un <input>)
    $('#rutaSelect').on('change', function() {
        var ruta = this.value;
        if (ruta) {
            rutasTable.column(1).search(ruta, true, false).draw();
        } else {
            rutasTable.column(1).search('').draw();
        }
    });

    // Filtrar por proceso (columna 6)
    $('#procesoSelect').on('change', function() {
        var proceso = this.value;
        if (proceso) {
            rutasTable.column(6).search(proceso, true, false).draw();
        } else {
            rutasTable.column(6).search('').draw();
        }
    });
});


    </script>
</body>
</html>