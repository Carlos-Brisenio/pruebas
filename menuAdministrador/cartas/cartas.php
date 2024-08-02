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

    // Consulta SQL para encontrar los registros duplicados
    /*$query = "SELECT nombre, calle, numero, COUNT(*) AS repetidos
    FROM InfoBoletos
    GROUP BY nombre, calle, numero
    HAVING COUNT(*) > 1";*/

    $query = "SELECT calle, numero,
        GROUP_CONCAT(DISTINCT nombre ORDER BY nombre SEPARATOR ', ') AS nombres,
        GROUP_CONCAT(DISTINCT ciudad ORDER BY ciudad SEPARATOR ', ') AS ciudades,
        GROUP_CONCAT(DISTINCT colonia ORDER BY colonia SEPARATOR ', ') AS colonias,
        GROUP_CONCAT(DISTINCT colinda1 ORDER BY colinda1 SEPARATOR ', ') AS colindas1,
        GROUP_CONCAT(DISTINCT colinda2 ORDER BY colinda2 SEPARATOR ', ') AS colindas2,
        GROUP_CONCAT(DISTINCT referencia ORDER BY referencia SEPARATOR ', ') AS referencias,
        GROUP_CONCAT(DISTINCT telefono1 ORDER BY telefono1 SEPARATOR ', ') AS telefonos1,
        GROUP_CONCAT(DISTINCT telefono2 ORDER BY telefono2 SEPARATOR ', ') AS telefonos2,
        GROUP_CONCAT(DISTINCT correo_Electronico ORDER BY correo_Electronico SEPARATOR ', ') AS correos_Electronicos
    FROM InfoBoletos
    GROUP BY calle, numero;";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $duplicatedRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    
    

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
        function generarPreInvitacion() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const registrosDuplicados = <?php echo json_encode($duplicatedRecords); ?>;

            const mensaje = `Es un honor para nosotros extenderle una cordial bienvenida e invitarlo a participar en la rifa para la "Mayordomía a Señor San José Octubre 2025".

Octubre 2025 tiene como objetivo principal presentar y difundir los datos técnicos del sistema Mayordomía Tickets así como su uso y las fechas de operación previstas.`;

            const img = new Image();
            const imgDM = new Image();
            const imgTM = new Image();
            img.src = '/pruebas/menuAdministrador/cartas/Back-HojaMembretada.jpg';//Back
            imgDM.src = '/pruebas/menuAdministrador/cartas/diocesisCatedral.jpg';//DC
            imgTM.src = '/pruebas/menuAdministrador/cartas/ticketMayordomia.png';//TM

            

            img.onload = function() {

                registrosDuplicados.forEach((registro, index) => {
                if (index > 0) {
                    doc.addPage();
                }
                doc.setFontSize(12);
                

                doc.addImage(img, 'PNG', 1, 1, 207, 295);
                doc.addImage(imgDM, 'PNG', 15, 3, 75, 30);
                doc.addImage(imgTM, 'PNG', 130, 7, 70, 25);
                // Línea de saludo
                doc.setFontSize(14);
                doc.setFont("times", "bold");
                doc.setTextColor(41, 74, 48); // Color verde olivo RGB
                doc.text('Estimado Devoto', 15, 45);
                doc.setFontSize(12);
                doc.setFont("helvetica", "normal");
                doc.setTextColor(0, 0, 0); // Color negro RGB
                doc.text(`${registro.nombres}`, 15, 52);
                doc.text(`${registro.calle}`+` `+`${registro.numero}`, 15, 57);
                //doc.text('Familia Fuentes Martinez', 15, 52);

                // Mensaje de bienvenida
                doc.setFontSize(14);
                doc.setFont("times", "bold");
                doc.setTextColor(41, 74, 48); // Color verde olivo RGB
                doc.text('Mensaje de bienvenida', 15, 62);

                // Cuerpo del mensaje
                doc.setFontSize(12);
                doc.setTextColor(0, 0, 0); // Color negro RGB
                doc.setFont("helvetica", "normal");
                doc.text(mensaje, 15, 68, { maxWidth: 185 });

                // Fechas importantes del sistema
                doc.setFontSize(14);
                doc.setFont("times", "bold");
                doc.setTextColor(41, 74, 48); // Color verde olivo RGB
                doc.text('Fechas importantes del sistema', 15, 98);

                // Parrafos
                doc.setFontSize(12);
                doc.setTextColor(0, 0, 0); // Color negro RGB
                const parrafos = [
                    'Inicio de Preventa:',
                    'Venta Público en General:',
                    'Fecha de finalización:',
                    'Periodo de apartado de boletos:'
                ];
                let yPosition = 105;
                parrafos.forEach(parrafos => {
                    doc.text(parrafos, 15, yPosition);
                    yPosition += 8;
                });

                // Fechas en lista
                doc.setFont("helvetica", "normal");
                doc.setFontSize(12);
                const fechas = [
                    '22/Septiembre/2024',
                    '01/Octubre/2024',
                    '24/Octubre/2024',
                    '22/Septiembre/2024 - 17/Octubre/2024'
                ];
                let yxPosition = 105;
                fechas.forEach(fecha => {
                    doc.text(fecha, 100, yxPosition);
                    yxPosition += 8;
                });

                // Lugar y venta de boletos
                doc.setFont("times", "bold");
                doc.setFontSize(12);
                doc.text('Lugar y venta de boletos:', 15, yPosition+3);
                yPosition += 10;
                doc.setFontSize(12);
                doc.setFont("helvetica", "normal");
                doc.text('• Notaría de la Santa Iglesia Catedral.', 15, yPosition);
                yPosition += 7;

                // Vínculo de Boletos con link y linkText
                const linkBoletos = 'https://boletos.mayordomiatickets.com';
                const linkTextBoletos = 'https://boletos.mayordomiatickets.com';
                doc.textWithLink(linkTextBoletos, 55, yPosition, { url: linkBoletos });//Área del enlace a Boletos
                doc.text('• A través del portal: ', 15, yPosition);
                yPosition += 7;

                // Fechas de operación
                doc.setFontSize(14);
                doc.setFont("times", "bold");
                doc.setTextColor(41, 74, 48); // Color verde olivo RGB
                doc.text('Fechas de operación', 15, yPosition+3);
                yPosition += 10;
                doc.setFontSize(12);
                doc.setFont("helvetica", "normal");
                doc.setTextColor(0, 0, 0); // Color negro RGB
                const operacion = `El sistema Mayordomía Tickets está programado para iniciar operaciones el día domingo 22 de septiembre en su fase de preventa para todos aquellos feligreses que compraron boletos en "Octubre 2024". La etapa de preventa terminará el martes 01 de octubre, comenzando así la venta al público en general.

Agradecemos de antemano su atención y esperamos verlos pronto en el "Proceso Octubre 2025". Quedamos a su disposición para cualquier duda, aclaración o información adicional que pueda requerir. en los siguientes medios de comunicación.`;
                doc.text(operacion, 15, yPosition, { maxWidth: 180 });
                yPosition += 45;

                // Redes sociales
                // Vínculo de facebook con link y linkText
                const linkFacebook = 'https://www.facebook.com/MayordomiaTickets';
                const linkTextFacebook = 'Mayordomía Tickets';
                doc.textWithLink(linkTextFacebook, 37, yPosition, { url: linkFacebook });//Área del enlace
                doc.setFont("helvetica", "bold");
                doc.text('Facebook: ', 15, yPosition);
                doc.setFont("helvetica", "normal");
                yPosition += 5;

                // Vínculo de WhatsApp con link y linkText
                const linkWhatsApp = 'https://whatsapp.com/channel/0029VajTTeO3QxS62tm1e02w';
                const linkTextWhatsApp = 'Canal Mayordomía Tickets';
                doc.textWithLink(linkTextWhatsApp, 39, yPosition, { url: linkWhatsApp });//Área del enlace WhatsApp
                doc.setFont("helvetica", "bold");
                doc.text('WhatsApp: ', 15, yPosition);
                doc.setFont("helvetica", "normal");
                yPosition += 20;

                // Despedida
                doc.setFont("times", "bold");
                doc.setFontSize(14);
                doc.setTextColor(41, 74, 48); // Color verde olivo RGB
                doc.text('Atentamente,', 98, yPosition+2);
                doc.setTextColor(0, 0, 0); // Color negro RGB
                doc.setFont("helvetica", "normal");
                doc.setFontSize(12);
                yPosition += 8;
                doc.text('El Equipo de Mayordomía Tickets.', 81, yPosition+3);
                yPosition += 8;
                doc.text('31 de Julio 2024 en Ciudad Guzmán, Mpio Zapotlán El Grande, Jalisco.', 45, yPosition+3);

            });

                
                doc.save('Pre-Invitacion.pdf');
            };
        }            
    </script>

</body>
</html>