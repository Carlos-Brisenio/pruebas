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

    $boletosStatus1 = [];

    $queryStatus1 = "SELECT numero_boleto FROM Boletos WHERE status = 1";
    $stmtStatus1 = $conn->prepare($queryStatus1);
    $stmtStatus1->execute();

    while ($row = $stmtStatus1->fetch(PDO::FETCH_ASSOC)) {
        $boletosStatus1[] = $row['numero_boleto'];
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!--======== CSS ========
    <link rel="stylesheet" href="/pruebas/menuUsuario/styleMenu.css"> -->
    <link rel="stylesheet" href="/pruebas/menuCajero/opcionesCajero/boletos/boletos.css">
    
    <!--===== Boxicons CSS ===== -->
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    
    <title>Ticket-M®</title> 
    <style>

        :root{
            /* ===== Colors ===== */
            --body-color: #E4E9F7;
            --sidebar-color: #FFF;
            --primary-color: #204d0c;
            --primary-color-light: #F6F5FF;
            --toggle-color: #DDD;
            --text-color: #000000;

            /* ====== Transition ====== */
            --tran-03: all 0.2s ease;
            --tran-03: all 0.3s ease;
            --tran-04: all 0.3s ease;
            --tran-05: all 0.3s ease;
        }

        body{
            min-height: 115vh;
            background-color: var(--body-color);
            transition: var(--tran-05);
        }

        .home{
            position: absolute;
            top: 0;
            left: 15px;
            height: 100vh;
            background-color: var(--body-color);
            transition: var(--tran-05);
        }
        .home .text{
            font-size: 30px;
            font-weight: 500;
            color: var(--text-color);
            padding: 12px 60px;
        }
        .container {
            margin-left: 0;
            text-align: left;
        }

        .btn-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;  /* Espacio desde las reglas, ajustable según tus necesidades */
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            background-color: white;
            color: black;
            cursor: pointer;
            border-radius: 5px;
        }

        button:hover {
            background-color: #28a745 ;
        }
    </style>

</head>
<body>
    <section class="home">
        <div class="text">Boletos disponibles</div>
        <div class="container">
            <div id="board">
                <!--Generar tablero de botones de 10x10-->
            </div>
            <br/>
            <button class='bx bxs-left-arrow' onclick="previous()"></button>
            <button class='bx bxs-right-arrow'  onclick="next()"></button>
        </div>
    </section>

    <script src="/pruebas/menuUsuario/script.js"></script>
    <script>
        var currentBoard = 1;
        var totalBoards = 15;  // Cambiar esto para agregar más tableros

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

        var boletosStatus1 = <?php echo json_encode($boletosStatus1); ?>;
        var buttonsPerPage = 264; // 20x10

        function updateBoard() {
            var boardDiv = document.getElementById("board");
            boardDiv.innerHTML = "";
            
            var startIndex = (currentBoard - 1) * buttonsPerPage;
            var endIndex = startIndex + buttonsPerPage;

            for (var i = startIndex; i < endIndex; i++) {
                if (i < boletosStatus1.length) {
                    var buttonNumber = boletosStatus1[i];
                    boardDiv.innerHTML += '<button id="button-' + buttonNumber + '" class="button" onclick="showAlert(' + buttonNumber + ')">' + buttonNumber + '</button>';

                    // Crea un salto de línea después de cada 20 botones
                    if ((i - startIndex + 1) % 22 === 0) {
                        boardDiv.innerHTML += '<br/>';
                    }
                }
            }
        }

        // Llamar a updateBoard para generar el tablero inicial cuando se carga la página
        window.onload = updateBoard;

        function fetchAndUpdateBoard() {
            fetch('/pruebas/menuCajero/opcionesCajero/boletos/getBoletoStatus.php')
            .then(response => response.json())
            .then(data => {
                // Reinicia las listas de status
                boletosStatus1 = [];

                for (let numeroBoleto in data) {
                    let status = data[numeroBoleto];

                    switch (status) {
                        case '1':
                            boletosStatus1.push(numeroBoleto);
                            break;
                    }
                }

                // Actualiza el tablero con los nuevos datos
                updateBoard();
            })
            .catch(error => {
                console.error('Error fetching boleto status:', error);
            });
        }

        // Llamar a fetchAndUpdateBoard() cada 5 segundos
        setInterval(fetchAndUpdateBoard, 3000);

        // Llamar a fetchAndUpdateBoard para generar el tablero inicial cuando se carga la página
        window.onload = fetchAndUpdateBoard;
    </script>
</body>
</html>
