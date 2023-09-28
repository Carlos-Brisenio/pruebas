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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

    
    <!----======== CSS ======== -->
    <link rel="stylesheet" href="/pruebas/menuUsuario/styleMenu.css">
    <link rel="stylesheet" href="/pruebas/menuUsuario/opcionesUsuario/boletos/resgistro.css">
    
    <!----===== Boxicons CSS ===== -->
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <!-- Asegúrate de que la ruta de la imagen sea la correcta -->
    <img id="imagenParaPdf" src="/pruebas/bannerV2.png" style="display: none;">

    
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
                <input type="text" id="numero-boleto" name="numero-boleto" value="<?php echo $numeroBoleto; ?>" readonly>
              </div>
              <div class="form-group">
                <label for="nombre_boleto">Nombre del Boleto:</label>
                <input type="text" id="nombre_boleto" name="nombre_boleto" value="<?php echo $nombreBoleto; ?>" readonly>
              </div>
            </div>
            <div class="button-container">
                <button class="button" id="ordenPago" name="ordenPago" onclick="generarOrdenPago()">Imprimir orden de pago</button>
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
        function generarOrdenPago() {
            const numeroBoleto = document.getElementById("numero-boleto").value;

            fetch(`/pruebas/menuUsuario/opcionesUsuario/boletos/getBoletoInfo.php?numeroBoleto=${numeroBoleto}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF();

                    doc.setFontSize(16);
                    doc.setFont("helvetica", "bold");
                    doc.text('Forma de pago ', 87, 10);

                    const img = document.getElementById('imagenParaPdf');
                    const canvas = document.createElement('canvas');
                    canvas.width = img.width;
                    canvas.height = img.height;

                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0);

                    const imgData = canvas.toDataURL('image/png', 1.0);

                    // Agregar imagen al PDF
                    // Ajusta las coordenadas y dimensiones según lo necesites
                    doc.addImage(imgData, 'JPEG', 20, 20, 55, 50);
                    doc.setFontSize(12);
                    doc.setFont("helvetica", "normal");
                    doc.text('Diocesis De Ciudad Gúzman', 80, 17);
                    doc.text('Mayordomía 2024', 90, 23);

                    doc.setFontSize(12);
                    doc.setFont("helvetica", "normal");
                    doc.text('Número de Boleto: ' + data.idBoleto, 130, 40);
                    doc.text('Fecha de apartado: ', 130, 47);
                    doc.text('Fecha limite de pago: ', 130, 54);
                    doc.text('Nombre del Boleto: ' + data.nombre, 10, 80);
                    doc.setFont("helvetica", "bold");
                    doc.text('Formas de pago: ', 10, 95);
                    doc.text('Directamente en la Santa Iglesia Catedral', 10, 105);
                    doc.setFont("helvetica", "normal");
                    doc.rect(10, 108, 160, 17);

                    doc.text('Número de boleto',10,113);
                    doc.text('Fecha Limite',70,113);
                    doc.text('Costo',110,113);
                    doc.text(''+data.idBoleto,10,120);
                    doc.text('FL',70,120);
                    doc.text('$170',110,120);

                    doc.setFont("helvetica", "bold");
                    doc.text('Instrucciones de pago: ', 10, 130);
                    doc.setFont("helvetica", "normal");
                    doc.text('Acude a rectoría de catedral en los siguientes horarios a pagar tu boleto: ', 10, 137);
                    doc.text('Lunes a domingo', 10, 144);
                    doc.text('11:30 hrs a 14:00 hrs', 10, 151);
                    doc.text('17:30 hrs a 20:00 hrs', 10, 158);

                    //Pago en BBVA Bancomer
                    doc.setFont("helvetica", "bold");
                    doc.rect(10, 173, 180, 15);
                    doc.rect(10, 173, 180, 7);
                    doc.rect(10, 173, 60, 15);
                    doc.rect(10, 173, 120, 15);
                    // Las coordenadas iniciales son 10, 15 y el rectángulo tiene un ancho de 180 y un alto de 130
                    doc.text('BBVA Bancomer', 10, 170);
                    doc.setFont("helvetica", "normal");
                    doc.text('Concepto',20,177);
                    doc.text('Fecha Limite',80,177);
                    doc.text('Costo',130,177);
                    doc.text(''+data.idBoleto+'-Mayordomia2024',20,184);
                    doc.text('FL',80,184);
                    doc.text('$170',130,184);

                    doc.text('En caja o practicaja del banco BBVA Bancomer ó bien, transferencia bancaria UNICAMENTE',10,194);
                    doc.text('de BBVA Bancomer a BBVA Bancomer.',10,201);
                    doc.text('El concepto de pago sera el número de boleto seguido de un "-" y la Frase "Mayordomia2024"',10,208);
                    doc.text('Como se muestra en la tabla de arriba',10,215);
                    doc.text('Los datos de la cuenta son:',10,222);
                    doc.text('Nombre: Diócesis de Ciudad Guzmán A.R.',10,229);
                    doc.text('Cuenta: 0110330213',10,236);
                    doc.text('Clave interbancaria: 012320001103302136',10,243);
                    
                    doc.setFont("helvetica", "bold");
                    doc.text('IMPORTANTE.',10,260);
                    doc.setFont("helvetica", "normal");
                    doc.text('Presenta en catedral tu comprobante de depósito o captura de pantalla en caso de haber',10,267);
                    doc.text('realizado transferencia.',10,274)
                    
                    doc.save('OrdenPago.pdf');
                }
            })
            .catch(error => {
                console.error('Hubo un problema con la petición Fetch:', error);
            });
        }
    </script> 
</body>
</html>