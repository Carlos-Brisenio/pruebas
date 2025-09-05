<?php

    // Conexión a la base de datos
    $host = "localhost";
    $db_name = "dbMayordomia";
    $username = "root";
    $password = "";
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);

    // Consulta para obtener el total de boletos por vencerse
    $queryTotalPorVencer = "
    SELECT COUNT(*) as total_por_vencer
    FROM Boletos
    WHERE fecha_Limite >= CURDATE()";
    $stmtTotalPorVencer = $conn->prepare($queryTotalPorVencer);
    $stmtTotalPorVencer->execute();
    $totalPorVencer = $stmtTotalPorVencer->fetch(PDO::FETCH_ASSOC)['total_por_vencer'];

    // Si la fecha de vencimiento no coincide con la fecha actual, establece $totalPorVencer a 0
    if ($totalPorVencer < 0) {
        $totalPorVencer = 0;
    }

    // Consulta para obtener boletos apartados
    $queryBoletosApartados = "
    SELECT COUNT(*) as total_apartados
    FROM Boletos
    WHERE status = 2"; // Suponemos que 2 significa boletos apartados
    $stmtBoletosApartados = $conn->prepare($queryBoletosApartados);
    $stmtBoletosApartados->execute();
    $totalApartados = $stmtBoletosApartados->fetch(PDO::FETCH_ASSOC)['total_apartados'];

    // Consulta para obtener boletos vendidos
    $queryBoletosVendidos = "
    SELECT COUNT(*) as total_vendidos
    FROM Boletos
    WHERE status = 3"; // Suponemos que 3 significa boletos vendidos
    $stmtBoletosVendidos = $conn->prepare($queryBoletosVendidos);
    $stmtBoletosVendidos->execute();
    $totalVendidos = $stmtBoletosVendidos->fetch(PDO::FETCH_ASSOC)['total_vendidos'];

    // Consulta para obtener boletos disponibles
    $queryBoletosDisponibles = "
    SELECT COUNT(*) as total_disponibles
    FROM Boletos
    WHERE status = 1"; // Cambia el valor 1 según corresponda al estado de boletos disponibles en tu base de datos
    $stmtBoletosDisponibles = $conn->prepare($queryBoletosDisponibles);
    $stmtBoletosDisponibles->execute();
    $totalDisponibles = $stmtBoletosDisponibles->fetch(PDO::FETCH_ASSOC)['total_disponibles'];
    
    // Calcula los porcentajes
    $porcentajeVendidos = ($totalVendidos ) ;
    $porcentajeDisponibles = ($totalDisponibles ) ;
    $porcentajeApartados = ($totalApartados ) ;
    
    // Suma los porcentajes
    $porcentajeTotal = $porcentajeVendidos + $porcentajeDisponibles + $porcentajeApartados;

    // Consulta para obtener el total de usuarios
    $queryTotalUsuarios = "
    SELECT COUNT(*) as total_usuarios
    FROM Usuarios";
    $stmtTotalUsuarios = $conn->prepare($queryTotalUsuarios);
    $stmtTotalUsuarios->execute();
    $totalUsuarios = $stmtTotalUsuarios->fetch(PDO::FETCH_ASSOC)['total_usuarios'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Administrador</title>
    <!-- Agregar los enlaces a los archivos CSS de Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="estilosadmin.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <link rel="stylesheet" href="/pruebas/menuUsuario/styleMenu.css">

    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>

        <!-- Incluye la librería de Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
        <div class="container mt-5">
            <div class="text"><h1 class="text-center">Inicio</h1></div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card dashboard-card sales-card">
                            <i class="fas fa-dollar-sign icon"></i>
                            <div class="card-body">
                                <h5 class="card-title">Ventas</h5>
                                <p class="card-text"><?php echo $totalVendidos; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card dashboard-card apartados-card">
                            <i class="fas fa-lock icon"></i>
                            <div class="card-body">
                                <h5 class="card-title">Boletos Apartados</h5>
                                <p class="card-text"><?php echo $totalApartados; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card dashboard-card por-vencer-card">
                            <i class="fas fa-clock icon"></i>
                            <div class="card-body">
                                <h5 class="card-title">Boletos por Vencer</h5>
                                <p class="card-text <?php echo ($totalPorVencer > 0) ? 'text-danger' : ''; ?>"><?php echo $totalPorVencer; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card dashboard-card users-card">
                            <i class="fas fa-users icon"></i>
                            <div class="card-body">
                                <h5 class="card-title">Cajeros y Admins</h5>
                                <p class="card-text"><?php echo $totalUsuarios; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
    <div id="pieChartContainer" class="card dashboard-card pie-chart-card custom-pie-chart">
        <div class="card-body">
            <h5 class="card-title">Gráfica de Boletos</h5>
            <canvas id="pieChart" width="300" height="250"></canvas>
        </div>
    </div>
</div>



    </section>

        <script>
        // Datos para la gráfica de pastel
        var ctx = document.getElementById('pieChart').getContext('2d');
        var pieData = {
            labels: ['Vendidos', 'Disponibles', 'Apartados'],
            datasets: [{
                data: [<?php echo $porcentajeVendidos; ?>, <?php echo $porcentajeDisponibles; ?>, <?php echo $porcentajeApartados; ?>],
                backgroundColor: ['red', 'green', 'yellow']
            }]
        };
        
        // Configuración de la gráfica de pastel
        var pieChart = new Chart(ctx, {
            type: 'pie',
            data: pieData,
            options: {
                responsive: true
            }
        });

    </script>


    <script src="/pruebas/menuUsuario/script.js"></script>

</body>
</html>