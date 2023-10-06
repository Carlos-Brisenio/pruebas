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
    if(isset($_GET['token'])) {
        $numeroBoleto = base64_decode($_GET['token']);

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
                    const fechaFormateada = formatDateToSpanish(data.fecha_Limite);

                    doc.setFontSize(16);
                    doc.setFont("helvetica", "bold");
                    doc.text('Orden de pago ', 87, 10);

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
                    doc.text('Diócesis De Ciudad Gúzman', 80, 17);
                    doc.text('Mayordomía 2024', 90, 23);

                    doc.setFontSize(12);
                    doc.setFont("helvetica", "normal");
                    doc.text('Número de Boleto: ' + data.idBoleto, 130, 40);
                    doc.text('Fecha de apartado: ' + data.fecha_Compra, 130, 47);
                    doc.text('Fecha límite de pago: ' + data.fecha_Limite, 130, 54);
                    doc.text('Nombre del Boleto: ' + data.nombre, 10, 80);
                    doc.setFont("helvetica", "bold");
                    doc.text('Formas de pago: ', 10, 95);
                    doc.text('1. Directamente en la Santa Iglesia Catedral', 10, 105);
                    doc.setFont("helvetica", "normal");
                    doc.setFillColor(32, 77, 12); // RGB para verde
                    doc.rect(20, 108, 150, 17);//Rectangulo principal
                    doc.rect(20, 108, 150, 8, 'F');

                    doc.setTextColor(255, 255, 255); // RGB para blanco
                    doc.text('Número de boleto',20,113);
                    doc.text('Fecha Límite',80,113);
                    doc.text('Costo',120,113);
                    doc.setTextColor(0, 0, 0); // RGB para negro
                    doc.text(data.idBoleto,20,120);
                    doc.text(data.fecha_Limite,80,120);
                    doc.text('$170',120,120);

                    doc.setFont("helvetica", "bold");
                    doc.text('Instrucciones de pago: ', 20, 130);
                    doc.setFont("helvetica", "normal");
                    doc.text('Acude a rectoría de catedral en los horarios de atención.', 20, 137);

                    //Pago en BBVA Bancomer
                    doc.setFont("helvetica", "bold");
                    // Las coordenadas iniciales son 10, 15 y el rectángulo tiene un ancho de 180 y un alto de 130
                    doc.rect(20, 146, 150, 17);
                    doc.setFillColor(255, 255, 0); // RGB para amarillo
                    doc.rect(20, 146, 150, 7,'F');
                    // Mover los siguientes campos hacia donde estaban los primeros campos
                    doc.text('2. BBVA Bancomer', 10, 144);
                    doc.text('Concepto',20,151);
                    doc.text('Fecha Límite',80,151);
                    doc.text('Costo',120,151);
                    doc.setFont("helvetica", "normal");
                    doc.text(''+data.idBoleto+'-Mayordomia2024',20,158);
                    doc.text(''+data.fecha_Limite,80,158);
                    doc.text('$170',120,158);

                    doc.text('En caja o practicaja del banco BBVA Bancomer ó bien, transferencia bancaria UNICAMENTE',20,168);
                    doc.text('de BBVA Bancomer a BBVA Bancomer.',20,175);
                    doc.text('El concepto de pago será el número de boleto seguido de un "-" y la Frase "Mayordomia2024"',20,182);
                    doc.text('Como se muestra en la tabla de arriba',20,189);
                    doc.text('Los datos de la cuenta son:',20,196);
                    doc.text('Nombre: Diócesis de Ciudad Guzmán A.R.',20,203);
                    doc.text('Cuenta: 0110330213',20,210);
                    doc.text('Clave interbancaria: 01 2320 0011 0330 2136',20,217);

                    doc.setFont("helvetica", "bold");
                    doc.text('IMPORTANTE.',10,234);
                    doc.setFont("helvetica", "normal");
                    doc.text('Presenta en catedral tu comprobante de depósito o captura de pantalla en caso de haber',10,241);
                    doc.text('realizado transferencia, antes del '+ fechaFormateada+'.',10,248);

                    // Mover los campos "Lunes a domingo" al final del documento
                    doc.setFont("helvetica", "bold");
                    doc.text('Horarios de atención:',10,258);
                    doc.setFont("helvetica", "normal");
                    doc.text('Lunes a domingo', 10, 265);
                    doc.text('11:30 hrs a 14:00 hrs', 10, 272);
                    doc.text('17:30 hrs a 20:00 hrs', 10, 279);

                    doc.text('Catedral De Ciudad Guzmán', 70, 265);
                    doc.text('Prisciliano Sánchez #19', 70, 272);
                    doc.text('Teléfono: 341-412-0132', 70, 279);
    
                    doc.save('OrdenPago_' + data.idBoleto + '.pdf');
                }
            })
            .catch(error => {
                console.error('Hubo un problema con la petición Fetch:', error);
            });
        }

        function formatDateToSpanish(dateString) {
            const date = new Date(dateString);
            date.setDate(date.getDate() + 1); // Suma un día a la fecha

            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            return date.toLocaleDateString('es-MX', options);
        }
    </script> 
</body>
</html>