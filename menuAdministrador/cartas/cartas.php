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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!----======== CSS ======== -->
    <link rel="stylesheet" href="/pruebas/menuUsuario/styleMenu.css">
    <link rel="stylesheet" href="stylesCar.css">
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
        <section class="cartas">
            <h1>Cartas</h1>
            <h3>Impresi&oacute;n de cartas</h3>
        </section>
        <div class="section">
            <div class="card">
                <h2>Mensaje de bienvenida</h2>
                <p>Es un honor para nosotros extenderle una cordial bienvenida e invitarlo a participar en la Mayordomía Señor San José “Octubre Proceso 2025”.</p>
                <p>Octubre Proceso 2025 tiene como objetivo principal presentar y difundir los datos técnicos del sistema Mayordomía Tickets así como su uso y las fechas de operación previstas.</p>
                <br>
                <h2>Fechas importantes</h2>
                <ul>
                    <li><strong>Fecha de Inicio:</strong> 22/Septiembre/2024 (Preventa)</li>
                    <li><strong>Fecha de Finalización:</strong> 24/Octubre/2024</li>
                    <li><strong>Fecha de apartado de boletos:</strong> 22/Septiembre/2024 – 17/Octubre/2024</li>
                    <li><strong>Lugar y venta de boletos:</strong></li>
                    <ul>
                        <li class="lugares">Rectoría de la Santa Iglesia Catedral con domicilio en Prisciliano Sánchez #19.</li>
                        <li class="lugares">A través del portal: <a href="https://boletos.mayordomiatickets.com">https://boletos.mayordomiatickets.com</a></li>
                    </ul>
                </ul>
                <br>
                <h2>Fechas de operación</h2>
                <p>El sistema Mayordomía Tickets está programado para iniciar operaciones el día domingo 22 de septiembre en su fase de preventa para todos aquellos feligreses que compraron boletos en “Octubre Proceso 2024”. La fase de implementación se extenderá hasta el martes 1 de octubre, comenzando así la venta al público en general.</p>
                <p>Quedamos a su disposición para cualquier duda, aclaración o información adicional que pueda requerir.</p>
                <p>Agradecemos de antemano su atención y esperamos verlos pronto en el “Proceso octubre 2025”.</p>
                <br>
                <p><strong>Atentamente,</strong></p>
                <p>Con cariño y muchas ganas de ver lo que viene,<br>
                El Equipo de Mayordomía Tickets.</p>
                <p>31 de agosto 2024 en Ciudad Guzmán, Mpio Zapotlán El Grande, Jalisco.</p>
            </div>
            <div class="buttons">
                <button class="pre-invitacion" id="preInvitacion" name="preInvitacion" onclick="generarPreInvitacion()">Imprimir Pre-invitaciónes</button>
                <button class="editar">Editar</button>
                <button class="guardar">Guardar</button>
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