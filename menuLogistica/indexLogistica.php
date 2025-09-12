<?php
    session_start();
    /*if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["isLoggedIn"] !== true) {
        header("Location: /principal-Logistica.php"); // Reemplaza 'login.php' con el nombre de tu archivo de inicio de sesión si es diferente
        exit;
    }*/

    // Conexión a la base de datos
    $host = "localhost";
    $db_name = "dbMayordomia";
    $username = "root";
    $password = "";

    //try {
        $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
        /*  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
    }*/

    $anioActual = date('Y');

    // ✅ Tomamos el usuario de la sesión
    $usuarioSesion = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;

    // Décimas entregadas por FECHA (solo del usuario actual -> entrego)
    $queryDecimasEntregadas = "
        SELECT DATE(fechaEntrega) as fecha, SUM(numeroBoletos) as total_entregadas
        FROM Rutas
        WHERE status = 1 
        AND proceso = :anio
        AND TRIM(LOWER(entrego)) = TRIM(LOWER(:usuario))
        GROUP BY DATE(fechaEntrega)
        ORDER BY fechaEntrega ASC
    ";
    $stmtDecimasEntregadas = $conn->prepare($queryDecimasEntregadas);
    $stmtDecimasEntregadas->bindParam(':anio', $anioActual, PDO::PARAM_INT);
    $stmtDecimasEntregadas->bindParam(':usuario', $usuarioSesion, PDO::PARAM_STR);
    $stmtDecimasEntregadas->execute();
    $decimasEntregadasRaw = $stmtDecimasEntregadas->fetchAll(PDO::FETCH_ASSOC);

    // Preparar datos acumulativos y por día
    $labels = [];
    $entregadas = [];      // acumuladas
    $entregadasDia = [];   // entregas del día

    $acumuladas = 0;
    foreach ($decimasEntregadasRaw as $row) {
        $labels[] = $row['fecha'];
        $entregadasDia[] = $row['total_entregadas'];
        $acumuladas += $row['total_entregadas'];
        $entregadas[] = $acumuladas;
    }

    // Si no hay entregas aún
    if (empty($labels)) {
        $labels[] = 'Aún no hay entregas';
        $entregadas[] = 0;
        $entregadasDia[] = 0;
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!----======== CSS ======== -->
    <link rel="stylesheet" href="/pruebas/menuUsuario/styleMenu.css">
    <link rel="stylesheet" href="/pruebas/menuLogistica/logistica/stylesLogEnt.css">
    
    <!----======== identificador de usuario ======== -->
    <link rel="stylesheet" href="/pruebas/etiquetaUsuarios.css">
    
    <!----===== Boxicons CSS ===== -->
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    
    <!----===== Actualizador de procesos ===== -->
    <script src="/pruebas/menuUsuario/actualizadorProceso.js"></script>

    <!-- Agrega un elemento canvas para el gráfico -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">


	<title>Mayordomía Tickets©</title>
</head>
<body>
    <!-- Tarjeta usuario activo -->
    <div class="usuario-activo">
        <i class='bx bx-user-circle'></i>
        <?= isset($_SESSION['usuario']) ? htmlspecialchars($_SESSION['usuario']) : 'Invitado' ?>
    </div>
    <nav class="sidebar close">
        <header>
            <div class="image-text">
                <span class="image">
                    <img src="/pruebas/menuUsuario/logoTM.png" alt="">
                </span>

                <div class="text logo-text">
                    <span class="name">LOGÍSTICA</span>
                    <span class="profession" id="proceso-span">PROCESO</span>
                    <span class="usuario">
                        A: 
                        <?php
                        if (isset($_SESSION['usuario'])) {
                            $usuario = htmlspecialchars($_SESSION['usuario']);
                            $partes = explode(' ', $usuario);

                            if (count($partes) > 2) {
                                // Une las dos primeras palabras en la primera línea
                                echo $partes[0] . ' ' . $partes[1] . '<br>' . implode(' ', array_slice($partes, 2));
                            } else {
                                // Si solo hay 1 o 2 palabras, imprime normal
                                echo $usuario;
                            }
                        } else {
                            echo 'Invitado';
                        }
                        ?>
                    </span>
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
                        <a href="indexLogistica.php">
                            <i class='bx bx-home-alt icon' ></i>
                            <span class="text nav-text">Inicio</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="/pruebas/menuLogistica/logistica/logisticaEntrega.php">
                            <i class='bx bx-select-multiple icon'></i>
                            <span class="text nav-text">Entregar Décima</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="#">
                            <i class='bx bx-cog icon' ></i>
                            <span class="text nav-text">Ajustes</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="bottom-content">
                <li class="">
                    <a href="/pruebas/principal-Logistica.php">
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
    <div class="text">
        Bienvenido "<?= isset($_SESSION['usuario']) ? htmlspecialchars($_SESSION['usuario']) : 'Usuario' ?> 
        "<br> a Mayordomía Tickets Proceso <?= $anioActual+1 ?>©
    </div>
        <h2 class="text">¿Qué deseas hacer?</h2>
        <div class="card-container">
            <div class="card">
                <a href="/pruebas/menuLogistica/logistica/logisticaEntrega.php" class="card-link">
                    <i class='bx bx-select-multiple icon'></i>
                    <span>Entregar Decimas</span>
                </a>
            </div>
            <div class="card">
                <a href="/pruebas/principal-Logistica.php" class="card-link"> 
                    <i class='bx bx-log-out icon'></i>
                    <span>Salir</span>
                </a>
            </div>
            <div class="card">
                <a href="#" class="card-link">
                    <i class='bx bx-cog icon'></i>
                    <span>Ajustes</span>
                </a>
            </div>
        </div>

        <br>
        <!--Aqui debe de ir el llamado para mostrar la grafica-->
            <div class="container">
                <h2 style="text-align: center">
                    Usuario: <?= htmlspecialchars($usuarioSesion) ?> | Proceso <?= $anioActual+1 ?><br><br>
                    Décimas Entregadas (Día) y rango de Entregadas (Acumuladas) <br>    
                </h2>
                <canvas id="decimasChart" width="800" height="400"></canvas>
            </div>
    </section>
    <style>
        .card-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px; 
        }

        .card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 1px solid #ccc;
            padding: 10px;
            flex-basis: 20%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s; 
        }

        .card:hover {
            transform: translateY(-5px); 
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .card:nth-child(1) {
            background-color: rgb(32, 77, 12);
        }

        .card:nth-child(2) {
            background-color: rgb(255, 255, 0);
        }

        .card:nth-child(3) {
            background-color: rgb(110, 13, 13);
        }

        .icon {
            font-size: 48px; /* Tamaño incrementado para los íconos */
        }

        .card-link {
            text-decoration: none; /* Eliminar subrayado */
            color: inherit; /* Heredar el color del texto padre (negro por defecto) */
        }

        /* Estilos para los enlaces cuando están en estado activo (hover) o visitado */
        .card-link:hover,
        .card-link:visited {
            color: inherit; /* Heredar el color del texto padre */
        }

        .card-container .card:nth-child(2) span {
            color: rgb(0, 0, 0);
            font-weight: bold;
        }

        .card-container .card:nth-child(1) span,
        .card-container .card:nth-child(1) .icon {
            color: white;
            font-weight: bold;
        }

        /* Estilo para el span y el ícono de la tarjeta 3 */
        .card-container .card:nth-child(3) span,
        .card-container .card:nth-child(3) .icon {
            color: white;
            font-weight: bold;
        }

    </style>

    <script src="/pruebas/menuUsuario/script.js"></script>
    <script src="/pruebas/menuAdministrador/autologout.js"></script>

    <script>
        // Datos desde PHP
        var labels = <?php echo json_encode($labels); ?>;
        var entregadas = <?php echo json_encode($entregadas); ?>;
        var entregadasDia = <?php echo json_encode($entregadasDia); ?>;

        var ctxDecimas = document.getElementById('decimasChart').getContext('2d');
        var decimasChart = new Chart(ctxDecimas, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Décimas Entregadas del Día',
                        data: entregadasDia,
                        backgroundColor: 'rgba(255, 206, 86, 0.6)',
                        borderColor: 'rgba(255, 206, 86, 1)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Décimas Entregadas (Acumuladas)',
                        data: entregadas,
                        type: 'line',
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.3,
                        yAxisID: 'y'
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Número de Décimas' }
                    },
                    x: {
                        title: { display: true, text: 'Fecha de Entrega' }
                    }
                }
            }
        });

    </script>
</body>
</html>