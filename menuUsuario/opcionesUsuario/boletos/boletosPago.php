<?php
    // Conexión a la base de datos
    $host = "localhost";
    $db_name = "dbMayordomia";
    $username = "root";
    $password = "";
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);

    // Recuperar el número de boleto desde la URL
    $numeroBoleto = "";
    $nombreBoleto = "";
    if(isset($_GET['numeroBoleto'])) {
        $numeroBoleto = $_GET['numeroBoleto'];

        // Realizar consulta a la base de datos
        $stmt = $conn->prepare("SELECT nombre FROM InfoBoletos WHERE idBoleto = ?");
        $stmt->execute([$numeroBoleto]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $nombreBoleto = $result['nombre'];
        }
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
    <link rel="stylesheet" href="/pruebas/menuUsuario/opcionesUsuario/boletos/resgistro.css">
    
    <!----===== Boxicons CSS ===== -->
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    
    <title>Ticket-Mayordomía®</title> 
</head>
<body>
    <nav class="sidebar close">
        <header>
            <div class="image-text">
                <span class="image">
                    <img src="/pruebas/menuUsuario/logoTM.png" alt="">
                </span>

                <div class="text logo-text">
                    <span class="name">USUARIOS</span>
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
                        <a href="/menuUsuario/opcionesUsuario/boletos/boletosUsuario.html">
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
        <div class="text"> Formas de pago</div>

        <div class="section">
            <h2 class="section-title">Método de pago</h2>
            <h3>Información del boleto</h3>
            <div class="form-row">
              <div class="form-group">
                <label for="numero-boleto">Número de Boleto:</label>
                <input type="text" id="numero-boleto" name="numero-boleto" readonly>
              </div>
              <div class="form-group">
                <label for="nombre_boleto">Nombre del Boleto:</label>
                <input type="text" id="nombre_boleto" name="nombre_boleto" readonly>
              </div>
            </div>
            <div class="button-container">
                <button class="button" id="ordenPago" name="ordenPago" onclick="window.location.href='/pruebas/menuUsuario/opcionesUsuario/boletos/ordenPagoPDF.php?numeroBoleto=<?php echo $numeroBoleto; ?>'">Imprimir orden de pago</button>
            </div>


            <div class="reglasPago">
                <h2 class="section-title">Instrucciones de pago</h2>
                <ul>
                    <li>Acude a rectoría de catedral en los siguientes horarios a pagar tu boleto:</li>
                    <ul class="sublista">
                        <li>Lunes a domingo</li>
                        <li>11:30 hrs a 14:00 hrs</li>
                        <li>17:30 hrs a 20:00 hrs</li>
                    </ul>
                </ul>
                <div class="form-row">
                </div>
            </div>
            
                <div class="button-container">
                    <button class="button" onclick="window.location.href='/pruebas/index.html'">Salir</button>
                </div>
          </div>
    </section>
    <script src="/pruebas/menuUsuario/script.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const numeroBoleto = urlParams.get('numeroBoleto');
            if (numeroBoleto) {
                document.getElementById("numero-boleto").value = numeroBoleto;
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
        const numeroBoletoElement = document.getElementById("numero-boleto");
        const nombreBoletoElement = document.getElementById("nombre_boleto");
        
        numeroBoletoElement.value = "<?php echo $numeroBoleto; ?>";
        nombreBoletoElement.value = "<?php echo $nombreBoleto; ?>";
    });
    </script>    
</body>
</html>