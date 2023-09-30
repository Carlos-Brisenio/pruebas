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

    $boletosStatus3 = [];

    $queryStatus3 = "SELECT numero_boleto FROM Boletos WHERE status = 3";
    $stmtStatus3 = $conn->prepare($queryStatus3);
    $stmtStatus3->execute();

    while ($row = $stmtStatus3->fetch(PDO::FETCH_ASSOC)) {
        $boletosStatus3[] = $row['numero_boleto'];
    }

    $boletosStatus4 = [];

    $queryStatus4 = "SELECT numero_boleto FROM Boletos WHERE status = 4";
    $stmtStatus4 = $conn->prepare($queryStatus4);
    $stmtStatus4->execute();

    while ($row = $stmtStatus4->fetch(PDO::FETCH_ASSOC)) {
        $boletosStatus4[] = $row['numero_boleto'];
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
                    <span class="name">CAJERO</span>
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
                            <a href="/pruebas/menuCajero/indexCajero.html">
                                <i class='bx bx-home-alt icon' ></i>
                                <span class="text nav-text">Inicio</span>
                            </a>
                    </li>

                    <li class="nav-link">
                        <a href="#">
                            <i class='bx bx-data icon'></i>
                            <span class="text nav-text">Mis datos</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="/pruebas/menuCajero/opcionesCajero/boletos/boletosCajero.php">
                            <i class='bx bx-purchase-tag-alt icon'></i>
                            <span class="text nav-text">Boletos</span>
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
        <div class="text">Boletos</div>
        <div class="container">
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
            <!--<<i class='bx bx-left-arrow-circle' ></i>-->
        </div>
    </section>

    <script src="/pruebas/menuUsuario/script.js"></script>
    <script>
        var currentBoard = 1;
        var totalBoards = 15;  // Cambiar esto para agregar más tableros

        var boletosStatus2 = <?php echo json_encode($boletosStatus2); ?>;
        var boletosStatus3 = <?php echo json_encode($boletosStatus3); ?>;
        var boletosStatus4 = <?php echo json_encode($boletosStatus4); ?>;

        function showAlert(buttonNumber) {
            // Cambiar el color del botón seleccionado a amarillo
            var selectedButton = document.getElementById('button-' + buttonNumber);
            selectedButton.style.backgroundColor = 'yellow';

            // Redirigir a boletosRegistro.php con el número de boleto
            setTimeout(function () {
                var encodedBoleto = btoa(buttonNumber);
                window.location.href = '/pruebas/menuCajero/opcionesCajero/boletos/boletosRegistroCajero.php?token=' + encodedBoleto;
            }, 1000);
        }

        function previous() {
            if (currentBoard > 1) {
                currentBoard--;
                updateBoard();
            }
        }

        function next() {
            if (currentBoard < totalBoards) {
                currentBoard++;
                updateBoard();
            }
        }

        function updateBoard() {
            var boardDiv = document.getElementById("board");
            boardDiv.innerHTML = "";
            
            for (var i = 1; i <= 10; i++) {
                for (var j = 1; j <= 10; j++) {
                    var buttonNumber = (currentBoard - 1) * 100 + (i - 1) * 10 + j;

                    if (boletosStatus2.includes(buttonNumber.toString())) {
                        boardDiv.innerHTML += '<button id="button-' + buttonNumber + '" class="button" style="background-color: yellow;" onclick="showAlert(' + buttonNumber + ')" disabled>' + buttonNumber + '</button>';
                    } else if (boletosStatus3.includes(buttonNumber.toString())) {
                                boardDiv.innerHTML += '<button id="button-' + buttonNumber + '" class="button" style="background-color: red;" onclick="showAlert(' + buttonNumber + ')" disabled>' + buttonNumber + '</button>';
                    } else if (boletosStatus4.includes(buttonNumber.toString())) {
                                boardDiv.innerHTML += '<button id="button-' + buttonNumber + '" class="button" style="background-color: #2e055d;" onclick="showAlert(' + buttonNumber + ')" disabled>' + buttonNumber + '</button>';
                    } else{
                        boardDiv.innerHTML += '<button id="button-' + buttonNumber + '" class="button" onclick="showAlert(' + buttonNumber + ')">' + buttonNumber + '</button>';
                    }
                }
                boardDiv.innerHTML += '<br/>';
            }
        }

        // Llamar a updateBoard para generar el tablero inicial cuando se carga la página
        window.onload = updateBoard;
    </script>

</body>
</html>