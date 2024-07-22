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
        echo "Error de conexión: " . $e->getMessage();
    }

    // Consulta para obtener boletos apartados por fecha
    $queryBoletosApartados = "
    SELECT DATE(Boletos.fecha_Compra) as fecha, COUNT(*) as total_apartados
    FROM Boletos
    INNER JOIN InfoBoletos ON Boletos.numero_boleto = InfoBoletos.idBoleto
    GROUP BY DATE(Boletos.fecha_Compra)";

    $stmtBoletosApartados = $conn->prepare($queryBoletosApartados);
    $stmtBoletosApartados->execute();
    $boletosApartados = $stmtBoletosApartados->fetchAll(PDO::FETCH_ASSOC);

    // Consulta para obtener boletos vendidos por fecha (de manera similar a la anterior)
    $queryBoletosVendidos = "
    SELECT DATE(Boletos.fecha_Compra) as fecha, COUNT(*) as total_Vendidos
    FROM Boletos
    INNER JOIN InfoBoletos ON Boletos.numero_boleto = InfoBoletos.idBoleto
    WHERE Boletos.status = 3
    GROUP BY DATE(Boletos.fecha_Compra)";

    $stmtBoletosVendidos = $conn->prepare($queryBoletosVendidos);
    $stmtBoletosVendidos->execute();
    $boletosVendidos = $stmtBoletosVendidos->fetchAll(PDO::FETCH_ASSOC);

    //Consulta para obterner el top 5 de las colonias con más boletos vendidos
    $queryTopColonias = "
    SELECT colonia, COUNT(idBoleto) as cantidad_boletos
    FROM InfoBoletos
    GROUP BY colonia
    ORDER BY cantidad_boletos DESC
    LIMIT 5";

    $stmtTopColonias = $conn->prepare($queryTopColonias);
    $stmtTopColonias->execute();
    $topColonias = $stmtTopColonias->fetchAll(PDO::FETCH_ASSOC);

    //Consulta para obterner el top 5 de los domicilios con más boletos vendidos
    $queryTopCalles = "
    SELECT calle, numero, COUNT(idBoleto) as cantidad_boletos
    FROM InfoBoletos
    GROUP BY calle, numero
    ORDER BY cantidad_boletos DESC
    LIMIT 5";

    $stmtTopCalles = $conn->prepare($queryTopCalles);
    $stmtTopCalles->execute();
    $topCalles = $stmtTopCalles->fetchAll(PDO::FETCH_ASSOC);


    // Cerrar la conexión a la base de datos
    $conn = null;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!----======== CSS ======== -->
    <link rel="stylesheet" href="/pruebas/menuUsuario/styleMenu.css">
    <link rel="stylesheet" href="stylesEst.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <!----===== Boxicons CSS ===== -->
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <!-- Agrega un elemento canvas para el gráfico -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <title>Ticket-Mayordomía®/MisDatos</title> 
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
                    <span class="profession">M-2024</span>
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
                        <a href="/pruebas/menuAdministrador/datos/misDatos.html">
                            <i class='bx bx-data icon'></i>
                            <span class="text nav-text">Mis datos</span>
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
                        <a href="estadisticas.php">
                            <i class='bx bx-bar-chart-square icon'></i>
                            <span class="text nav-text">Estadisticas</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="/pruebas/menuAdministrador/cartas/cartas.php">
                            <i class='bx bx-envelope icon' ></i>
                            <span class="text nav-text">Cartas</span>
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
                    <a href="/pruebas/principal-Administracion.php">
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
        <!-- Aquí es donde deberías insertar las tarjetas -->
        <section class="top-colonias">
        <h1>Estadisticas</h1>
            <h3>Top 5 Colonias con más Boletos Registrados</h3>
            <div class="card-container">
                <?php foreach ($topColonias as $colonia): ?>
                <div class="card">
                    <h4><?php echo $colonia['colonia']; ?></h4>
                    <p><?php echo $colonia['cantidad_boletos']; ?> boletos</p>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <!-- Aquí es donde deberías insertar las tarjetas para el top calles -->
        <section class="top-calles">
            <h3>Top 5 Domicilios con más Boletos Registrados</h3>
            <div class="card-container">
                <?php foreach ($topCalles as $calle): ?>
                <div class="card">
                    <h4><?php echo $calle['calle'] ?></h4>
                    <p><?php echo $calle['cantidad_boletos']; ?> boletos</p>
                </div>
                <?php endforeach; ?>
            </div>
            <br>
            <h2>Estadisticas de boletos apartados y vendidos<h2>
        </section>
        <!-- Agrega un elemento canvas para el gráfico -->
        <canvas id="myChart" width="400" height="200"></canvas>
    </section>

    <script src="/pruebas/menuUsuario/script.js"></script>
    
    <script>
        // Procesa los datos obtenidos de PHP
var fechas = <?php echo json_encode(array_column($boletosApartados, 'fecha')); ?>;
var boletosApartados = <?php echo json_encode(array_column($boletosApartados, 'total_apartados')); ?>;
var boletosVendidos = <?php echo json_encode(array_column($boletosVendidos, 'total_Vendidos')); ?>;

// Datos para el gráfico
var ctx = document.getElementById('myChart').getContext('2d');
var data = {
    labels: fechas,
    datasets: [
        {
            label: 'Boletos Apartados',
            data: boletosApartados,
            backgroundColor: 'rgba(75, 192, 192, 0.2)' ,
            borderColor: 'rgba(75, 192, 192, 1)' ,
            borderWidth: 1
        },
        {
            label: 'Boletos Vendidos',
            data: boletosVendidos, // Agrega los datos de boletos vendidos aquí
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1
        }
    ]
};

// Resto del código de configuración del gráfico...


        // Configuración del gráfico
        var options = {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        };

        var myChart = new Chart(ctx, {
            type: 'bar',
            data: data,
            options: options
            
        });
    </script>

</body>
</html>