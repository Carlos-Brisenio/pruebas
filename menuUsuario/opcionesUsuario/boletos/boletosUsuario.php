<?php
    $host = "localhost";
    $db_name = "dbMayordomia";
    $username = "root";
    $password = "";
    try {
        $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    $boletosStatus2 = [];

    $queryStatus2 = "SELECT numero_boleto FROM Boletos WHERE status = 2";
    $stmtStatus2 = $conn->prepare($queryStatus2);
    $stmtStatus2->execute();

    while ($row = $stmtStatus2->fetch(PDO::FETCH_ASSOC)) {
        $boletosStatus2[] = $row['numero_boleto'];
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
    <link rel="stylesheet" href="boletos.css">
    
    <!----===== Boxicons CSS ===== -->
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    
    <title>Ticket-M®</title> 
</head>
<body>
    <nav class="sidebar close">
        <header>
            <div class="image-text">
                <span class="image">
                    <img src="/pruebas/menuUsuario/logoTM.png" alt="">
                </span>

                <div class="text logo-text">
                    <span class="name">USUARIO</span>
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
                        <a href="boletosUsuario.html">
                            <i class='bx bx-purchase-tag-alt icon'></i>
                            <span class="text nav-text">Boletos</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="bottom-content">
                <li class="">
                    <a href="/pruebas/index.html">
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
        <div class="text">Boletos Mayordomia 2024</div>
        <div class="container">
            <div id="timer" class="timer-container">04:00 <br>Tiempo restante</div>
            <script>
                // Pasar la información de PHP a JavaScript
                var boletosStatus2 = <?php echo json_encode($boletosStatus2); ?>;
            </script>
            <div class="botonesInfo">
                <!-- Tabla de estados -->
                <table>
                    <tr>
                        <td><button style="background-color: #204d0c;"></button></td>
                        <td>Disponible</td>
                    </tr>
                    <tr>
                        <td><button style="background-color: yellow;"></button></td>
                        <td>Apartado</td>
                    </tr>
                    <tr>
                        <td><button style="background-color: red;"></button></td>
                        <td>Vendido</td>
                    </tr>
                </table>
            </div>
            <div id="board">
                <!--Generar tablero de botones de 10x10-->
                
            </div>
            <br/>
            <button class='bx bxs-left-arrow' onclick="previous()"></button>
            <button class='bx bxs-right-arrow'  onclick="next()"></button>
            <!--bx bxs-left-arrow-->
        </div>
    </section>

    <script src="/pruebas/menuUsuario/script.js"></script>
    <script src="reloj.js"></script>
    <script src="boletos.js"></script>s

</body>
</html>