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
    FROM Historico
    GROUP BY calle, numero;";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $duplicatedRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // Función para truncar el campo nombres
    function truncarNombres($nombre, $maxLength = 52) {
        if (mb_strlen($nombre) > $maxLength) {
            return mb_substr($nombre, 0, $maxLength) . '...';
        }
        return $nombre;
    }

    // Consulta para obtener rutas
    $queryRutasTable = "
        SELECT
            idRutas, 
            ruta,
            recorrido,
            nombres,
            domicilio,
            numeroBoletos
        FROM Rutas";
    
    $stmtRutasTable = $conn->prepare($queryRutasTable);
    $stmtRutasTable->execute();
    $rutasTable = $stmtRutasTable->fetchAll(PDO::FETCH_ASSOC);


    // Aplicar la función de truncado a cada nombre en los resultados
    $rutasTable = array_map(function($row) {
        $row['nombres'] = truncarNombres($row['nombres']);
        return $row;
    }, $rutasTable);

    // Consulta para obtener las rutas
    //$queryRutasExisten = "SELECT ruta FROM Rutas";
    $queryRutasExisten = "SELECT DISTINCT ruta FROM Rutas ORDER BY ruta ASC";
    $stmtRutasExisten = $conn->prepare($queryRutasExisten);
    $stmtRutasExisten->execute();
    $rutas = $stmtRutasExisten->fetchAll(PDO::FETCH_COLUMN);
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
    <link rel="stylesheet" href="/pruebas/menuAdministrador/ajustes/estilosAjustes.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <!----===== Boxicons CSS ===== -->
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <!-- Agrega un elemento canvas para el gráfico -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

    <!-- Tabla de rutas -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

    <!-- Creadores del pdf con jspdf -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>

    <title>Ticket-Mayordomía®/Datos</title> 
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
                    <span class="profession" id="proceso-span">PROCESO</span>
                </div>
            </div>

            <i class='bx bx-chevron-right toggle'></i>
        </header>

        <div id="confirmationModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center;">
            <div style="background-color: white; padding: 20px; width: 300px; border-radius: 10px;">
                <h3>Confirmar Acción</h3>
                <label>Usuario:</label>
                <input type="text" id="userInput">
                <label>Contraseña:</label>
                <input type="password" id="passwordInput">
                <div id="procesoField" style="display: none;">
                    <label>Proceso:</label>
                    <input type="number" id="procesoInput">
                </div>
                <button onclick="confirmDelete()">Confirmar</button>
                <button onclick="closeModal()">Cancelar</button>
            </div>
        </div>

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
                        <a href="estadisticas.php">
                            <i class='bx bx-bar-chart-square icon'></i>
                            <span class="text nav-text">Estadisticas</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="/pruebas/menuAdministrador/cartas/cartas.php">
                            <i class='bx bx-globe icon' ></i>
                            <span class="text nav-text">Logistica</span>
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
            <h1>Administraci&oacute;n de Cartas y log&iacute;stica</h1>
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
                <!--<h2>Fechas de operación</h2>
                <p>El sistema Mayordomía Tickets está programado para iniciar operaciones el día domingo 22 de septiembre en su fase de preventa para todos aquellos feligreses que compraron boletos en “Octubre Proceso 2024”. La fase de implementación se extenderá hasta el martes 1 de octubre, comenzando así la venta al público en general.</p>
                <p>Quedamos a su disposición para cualquier duda, aclaración o información adicional que pueda requerir.</p>
                <p>Agradecemos de antemano su atención y esperamos verlos pronto en el “Proceso octubre 2025”.</p>
                <br>
                <p><strong>Atentamente,</strong></p>
                <p>Con cariño y muchas ganas de ver lo que viene,<br>
                El Equipo de Mayordomía Tickets.</p>
                <p>31 de agosto 2024 en Ciudad Guzmán, Mpio Zapotlán El Grande, Jalisco.</p>-->
            </div>
            <div class="buttons">
                <button class="pre-invitacion" id="preInvitacion" name="preInvitacion" onclick="generarPreInvitacion()">Imprimir Pre-invitaciónes</button>
                <button class="editar">Editar</button>
                <button class="guardar">Guardar</button>
            </div>
            <br>
            <br>
            <section class="logistica">
                <h1>Logística de entrega de cartas y décimas</h1>
                <br>
            </section>
                <div class="container">
                    <h2>Edita la tabla según tus consideraciones</h2>
                    <br>
                    <h3>Crear conjuncion de datos para usarla en la creación de las rutas</h3>
                    <button class="conjuncion" onclick="showConfirmationModal('conjuncion')">Crear conjunción de datos</button><br><br>

                    <!-- Nuevo select y botón -->
                    <div class="form-group-select">
                        <label for="rutaSelect">Selecciona una ruta:</label>
                        <select id="rutaSelect" class="form-control">
                            <option value="">Selecciona una ruta</option>
                            <?php foreach ($rutas as $ruta): ?>
                                <option value="<?= htmlspecialchars($ruta) ?>"><?= htmlspecialchars($ruta) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button id="printRutaButton" class="buttonSelectImprimir" onclick="ImprimirRuta()">Imprimir ruta</button>
                    </div>


                    <table id="rutasTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="display:none;">idRutas</th>
                                <th>RUTAS<i class='bx bx-cycling icon'></i></th>
                                <th>RECORRIDO <i class='bx bx-trip icon'></th>
                                <th>NOMBRES</th>
                                <th>DOMICILIO</th>
                                <th>N° BOLETOS COMPRADOS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rutasTable as $ruta): ?>
                                <tr>
                                    <td class="idRutas" style="display:none;"><?= htmlspecialchars($ruta['idRutas']) ?></td>
                                    <td><input type="text" name="ruta" value="<?= htmlspecialchars($ruta['ruta']) ?>" style="width: 100px;" disabled></td>
                                    <td><input type="text" name="recorrido" value="<?= htmlspecialchars($ruta['recorrido']) ?>" style="width: 100px;" disabled></td>
                                    <td><?= htmlspecialchars($ruta['nombres']) ?></td>
                                    <td><?= htmlspecialchars($ruta['domicilio']) ?></td>
                                    <td><?= htmlspecialchars($ruta['numeroBoletos']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        
                    </table>
                    <div class="botones">
                        <button class="rutas" onclick="habilitarEdicion('ruta')">Editar Rutas</button>
                        <button class="recorrido" onclick="habilitarEdicion('recorrido')">Editar Recorridos</button>
                        <button class="guardarRuta" onclick="guardarCambios()">Guardar</button>
                    </div>
                </div>

        </div>
    </section>
    <script src="/pruebas/menuUsuario/script.js"></script>
    <script>
        function showConfirmationModal(action) {
            document.getElementById("confirmationModal").setAttribute("data-action", action);
            const procesoField = document.getElementById("procesoField");
            if (action === 'conjuncion') {
                procesoField.style.display = "block";
            } else {
                procesoField.style.display = "none";
            }
            document.getElementById("confirmationModal").style.display = "flex";
        }

        function closeModal() {
            document.getElementById("confirmationModal").style.display = "none";
        }

        function confirmDelete() {
        const user = document.getElementById("userInput").value;
        const password = document.getElementById("passwordInput").value;
        const action = document.getElementById("confirmationModal").getAttribute("data-action");
        const proceso = action === 'conjuncion' ? document.getElementById("procesoInput").value : null;

        if (action === 'conjuncion' && proceso) {
            // Validar si el proceso ya existe
            fetch('checkProcesoRutas.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `proceso=${proceso}`
            })
            .then(response => response.text())
            .then(data => {
                if (data.includes("El proceso ya se encuentra registrado")) {
                    alert(data);
                } else {
                    // Si el proceso es válido, continuar con la solicitud de traspaso
                    fetch('conjuncion.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `user=${user}&password=${password}&proceso=${proceso}`
                    })
                    .then(response => response.text())
                    .then(data => {
                        alert(data);
                        closeModal();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Hubo un error al intentar realizar la acción.');
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Hubo un error al validar el proceso.');
            });
        }
    }

    </script>
    
    <script>
        async function generarPreInvitacion() {
            const { jsPDF } = window.jspdf;
            const registrosDuplicados = <?php echo json_encode($duplicatedRecords); ?>;
            const mensaje = `Es un honor para nosotros extenderle una cordial bienvenida e invitarlo a participar en la rifa para la "Mayordomía a Señor San José Octubre 2025".

Octubre 2025 tiene como objetivo principal presentar y difundir los datos técnicos del sistema Mayordomía Tickets así como su uso y las fechas de operación previstas.`;

            const img = new Image();
            const imgDM = new Image();
            const imgTM = new Image();
            img.src = '/pruebas/menuAdministrador/cartas/Back-HojaMembretada.jpg'; // Back
            imgDM.src = '/pruebas/menuAdministrador/cartas/diocesisCatedral.jpg'; // DC
            imgTM.src = '/pruebas/menuAdministrador/cartas/ticketMayordomia.png'; // TM

            img.onload = async function() {
                for (let i = 0; i < registrosDuplicados.length; i++) {
                    const doc = new jsPDF();
                    const registro = registrosDuplicados[i];

                    doc.setFontSize(12);
                    doc.addImage(img, 'PNG', 1, 1, 207, 295);
                    doc.addImage(imgDM, 'PNG', 15, 3, 75, 30);
                    doc.addImage(imgTM, 'PNG', 130, 7, 70, 25);

                    doc.setFontSize(14);
                    doc.setFont("times", "bold");
                    doc.setTextColor(41, 74, 48);
                    doc.text('Estimado Devoto', 15, 45);
                    doc.setFontSize(12);
                    doc.setFont("helvetica", "normal");
                    doc.setTextColor(0, 0, 0);
                    doc.text(`${registro.nombres}`, 15, 52);
                    doc.text(`${registro.calle} ${registro.numero}`, 15, 57);

                    doc.setFontSize(14);
                    doc.setFont("times", "bold");
                    doc.setTextColor(41, 74, 48);
                    doc.text('Mensaje de bienvenida', 15, 62);

                    doc.setFontSize(12);
                    doc.setTextColor(0, 0, 0);
                    doc.setFont("helvetica", "normal");
                    doc.text(mensaje, 15, 68, { maxWidth: 185 });

                    doc.setFontSize(14);
                    doc.setFont("times", "bold");
                    doc.setTextColor(41, 74, 48);
                    doc.text('Fechas importantes del sistema', 15, 98);

                    doc.setFontSize(12);
                    doc.setTextColor(0, 0, 0);
                    const parrafos = [
                        'Inicio de Preventa:',
                        'Venta Público en General:',
                        'Fecha de finalización:',
                        'Periodo de apartado de boletos:'
                    ];
                    let yPosition = 105;
                    parrafos.forEach(parrafo => {
                        doc.text(parrafo, 15, yPosition);
                        yPosition += 8;
                    });

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

                    doc.setFont("times", "bold");
                    doc.setFontSize(12);
                    doc.text('Lugar y venta de boletos:', 15, yPosition + 3);
                    yPosition += 10;
                    doc.setFontSize(12);
                    doc.setFont("helvetica", "normal");
                    doc.text('• Notaría de la Santa Iglesia Catedral.', 15, yPosition);
                    yPosition += 7;

                    const linkBoletos = 'https://boletos.mayordomiatickets.com';
                    const linkTextBoletos = 'https://boletos.mayordomiatickets.com';
                    doc.textWithLink(linkTextBoletos, 55, yPosition, { url: linkBoletos });
                    doc.text('• A través del portal: ', 15, yPosition);
                    yPosition += 7;

                    doc.setFontSize(14);
                    doc.setFont("times", "bold");
                    doc.setTextColor(41, 74, 48);
                    doc.text('Fechas de operación', 15, yPosition + 3);
                    yPosition += 10;
                    doc.setFontSize(12);
                    doc.setFont("helvetica", "normal");
                    doc.setTextColor(0, 0, 0);
                    const operacion = `El sistema Mayordomía Tickets está programado para iniciar operaciones el día domingo 22 de septiembre en su fase de preventa para todos aquellos feligreses que compraron boletos en "Octubre 2024". La etapa de preventa terminará el martes 01 de octubre, comenzando así la venta al público en general.

    Agradecemos de antemano su atención y esperamos verlos pronto en el "Proceso Octubre 2025". Quedamos a su disposición para cualquier duda, aclaración o información adicional que pueda requerir. en los siguientes medios de comunicación.`;
                    doc.text(operacion, 15, yPosition, { maxWidth: 180 });
                    yPosition += 45;

                    const linkFacebook = 'https://www.facebook.com/MayordomiaTickets';
                    const linkTextFacebook = 'Mayordomía Tickets';
                    doc.textWithLink(linkTextFacebook, 40, yPosition, { url: linkFacebook });
                    doc.setFont("helvetica", "bold");
                    doc.text('Facebook: ', 15, yPosition);
                    yPosition += 6;

                    const linkTwitter = 'https://www.whatsapp.com/channel/0029VajTTeO3QxS62tm1e02w';
                    doc.setFont("helvetica", "normal");
                    const linkTextTwitter = 'Canal Mayordomía Tickets';
                    doc.textWithLink(linkTextTwitter, 40, yPosition, { url: linkTwitter });
                    doc.setFont("helvetica", "bold");
                    doc.text('WhatsApp: ', 15, yPosition);
                    yPosition += 6;

                    const linkInstagram = 'https://www.instagram.com/mayordomia.tickets/';
                    doc.setFont("helvetica", "normal");
                    const linkTextInstagram = 'Mayordomía Tickets';
                    doc.textWithLink(linkTextInstagram, 40, yPosition, { url: linkInstagram });
                    doc.setFont("helvetica", "bold");
                    doc.text('Instagram: ', 15, yPosition);
                    yPosition += 6;

                    const linkCorreo = 'mailto:soporte.boletos@mayordomiatickets.com';
                    doc.setFont("helvetica", "normal");
                    const linkTextCorreo = 'soporte.boletos@mayordomiatickets.com';
                    doc.textWithLink(linkTextCorreo, 40, yPosition, { url: linkCorreo });
                    doc.setFont("helvetica", "bold");
                    doc.text('Correo: ', 15, yPosition);
                    yPosition += 6;

                    // Despedida
                    doc.setFont("times", "bold");
                    doc.setFontSize(14);
                    doc.setTextColor(41, 74, 48); // Color verde olivo RGB
                    doc.text('Atentamente,', 98, yPosition+10);
                    doc.setTextColor(0, 0, 0); // Color negro RGB
                    doc.setFont("helvetica", "normal");
                    doc.setFontSize(12);
                    yPosition += 16;
                    doc.text('El Equipo de Mayordomía Tickets.', 81, yPosition+3);
                    yPosition += 10;
                    doc.text('31 de Julio 2024 en Ciudad Guzmán, Mpio Zapotlán El Grande, Jalisco.', 45, yPosition+3);

                    doc.save(`preinvitacion_${registro.calle}_${registro.numero}.pdf`);
                }
            };
        }

        //Configuración de la tabla de rutas
        $(document).ready(function() {
            
            // Configuración de DataTables para la tabla rutasTable
            var rutasTable = $('#rutasTable').DataTable({
                "searching": true,
                "searchMinLength": 1,
            });
        
            // Agregar funcionalidad de búsqueda personalizada para rutasTable
            $('#searchVendidos').on('keyup', function() {
                rutasTable.search(this.value).draw();
            });
        });
    </script>

<script>
        function habilitarEdicion(campo) {
    // Seleccionar todas las filas
    $('#rutasTable tbody tr').each(function() {
        $(this).find('input[name="' + campo + '"]').prop('disabled', false);
    });
}

    function guardarCambios() {
        const rutas = [];
        $('#rutasTable tbody tr').each(function() {
            const idRutas = $(this).find('.idRutas').text().trim();
            const ruta = $(this).find('input[name="ruta"]').val().trim();
            const recorrido = $(this).find('input[name="recorrido"]').val().trim();

            rutas.push({ idRutas, ruta, recorrido });
        });

        $.ajax({
            type: "POST",
            url: "guardarCambios.php",
            data: { rutas },
            success: function(response) {
                alert(response);
            },
            error: function() {
                alert("Error al guardar los cambios.");
            }
        });
    }

    // script de actualizacion de año automatico
    function actualizarProceso() {
        const spanProceso = document.getElementById('proceso-span');
        const añoActual = new Date().getFullYear();
        spanProceso.textContent = `PROCESO ${añoActual + 1}`;
    }

    // Llama a la función cuando la página se carga
    document.addEventListener('DOMContentLoaded', actualizarProceso);

    // script imprimir las rutas
    function ImprimirRuta() {
    var rutaSeleccionada = document.getElementById("rutaSelect").value;

    if (rutaSeleccionada === "") {
        alert("Por favor selecciona una ruta.");
        return;
    }

    // Realizar la solicitud AJAX
    $.ajax({
        url: 'obtenerRutas.php', // El archivo PHP que manejará la solicitud
        type: 'POST',
        data: { ruta: rutaSeleccionada },
        success: function(data) {
            var registros = JSON.parse(data);

            // Función para extraer el texto antes de la primera coma
            function extraerDomicilio(domicilio) {
                const indexComa = domicilio.indexOf(',');
                return indexComa !== -1 ? domicilio.substring(0, indexComa) : domicilio;
            }

            // Función para dividir el domicilio en dos líneas
            function dividirDomicilio(domicilio) {
                const primeraParte = domicilio.substring(0, 29);
                const segundaParte = domicilio.substring(29);
                return { primeraParte, segundaParte };
            }

            function maximoNombre(nombres) {
                const corto = nombres.substring(0, 49);
                return corto ;
            }

            // Generar PDF con los registros
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            const imgParteA = new Image();
            const imgParteB = new Image();
            imgParteA.src = '/pruebas/menuAdministrador/cartas/reciboParteA.png'; // Parte A
            imgParteB.src = '/pruebas/menuAdministrador/cartas/reciboParteB.png'; // Parte B

            registros.forEach(function(registro, index) {
                if (index > 0 && index % 3 === 0) {
                    doc.addPage();
                }

                const offsetY = (index % 3) * 90; // Espacio entre recibos

                // Dibujar el borde principal
                doc.setLineWidth(1.5);
                doc.rect(5, 5 + offsetY, 200, 80); // Rectángulo alrededor de todo el diseño

                // Dibujar la línea de separación en el centro
                doc.setLineWidth(0.5);
                doc.line(120, 5 + offsetY, 120, 85 + offsetY); // Línea en el medio

                // Primer ticket (izquierda)
                doc.setFontSize(12);
                doc.addImage(imgParteA, 'PNG', 10, 10 + offsetY, 70, 17);
                doc.text("Decima(s):", 90, 15 + offsetY);
                doc.text(`${registro.numeroBoletos}`, 113, 15 + offsetY);
                doc.line(112, 16 + offsetY, 117, 16 + offsetY); // Línea para decimas
                doc.text("Carta(s):", 90, 24 + offsetY);
                doc.text(`${registro.ruta}`, 113, 24 + offsetY);


                doc.text("Domicilio:", 10, 35 + offsetY);
                doc.text(`${extraerDomicilio(registro.domicilio)}`, 30, 35 + offsetY);
                doc.line(30, 36 + offsetY, 110, 36 + offsetY); // Línea para el domicilio

                doc.text("Nombre(s) de boleto(s):", 10, 40 + offsetY);
                const corto = maximoNombre(registro.nombres)
                doc.text(corto + "...", 10, 45 + offsetY);
                //doc.text(`${registro.nombres}`, 10, 45 + offsetY);
                doc.line(10, 46 + offsetY, 110, 46 + offsetY); // Línea para los nombres

                doc.text("Recibe:", 10, 53 + offsetY);
                doc.line(25, 53 + offsetY, 110, 53 + offsetY); // Línea para el domicilio
                doc.text("Parentesco: [Hij@] [Madre] [Padre] [Niet@] [Espos@]", 10, 58 + offsetY);
                doc.text("[Otro]:", 10, 65 + offsetY);
                doc.line(23, 65 + offsetY, 80, 65 + offsetY); // Línea para especificar otro

                doc.text("Especifique", 40, 70 + offsetY);

                doc.line(70, 75 + offsetY, 110, 75 + offsetY); // Línea para la firma
                doc.text("Firma recibe", 78, 80 + offsetY);

                // Segundo ticket (derecha)
                const { primeraParte, segundaParte } = dividirDomicilio(registro.domicilio);

                doc.setFontSize(12);
                doc.addImage(imgParteB, 'PNG', 125, 10 + offsetY, 50, 17);
                doc.text("Decima(s):", 175, 15 + offsetY);
                doc.text(`${registro.numeroBoletos}`, 198, 15 + offsetY);
                doc.line(197, 16 + offsetY, 202, 16 + offsetY); // Línea para decimas
                doc.text("Carta(s)", 175, 24 + offsetY);

                doc.text("Domicilio:", 125, 35 + offsetY);
                doc.text(primeraParte, 145, 35 + offsetY);
                doc.text(segundaParte, 125, 42 + offsetY); // Segunda línea del domicilio
                doc.line(145, 36 + offsetY, 200, 36 + offsetY); // Línea para el domicilio
                doc.line(125, 43 + offsetY, 200, 43 + offsetY); // Línea para el domicilio 2

                doc.text("Nombre(s) de boleto(s):", 125, 50 + offsetY);
                doc.text(`${registro.nombres}`, 125, 55 + offsetY);
                doc.line(125, 56 + offsetY, 200, 56 + offsetY); // Línea para los nombres

                doc.text("Entrego:", 125, 65 + offsetY);
                doc.line(145, 65 + offsetY, 200, 65 + offsetY); // Línea para la firma

                doc.line(160, 75 + offsetY, 200, 75 + offsetY); // Línea para la firma entrega
                doc.text("Firma entrega", 167, 80 + offsetY);
            });

            doc.save(`Ruta_${rutaSeleccionada}.pdf`);
        },
        error: function(error) {
            console.log(error);
            alert("Ocurrió un error al obtener los datos.");
        }
    });
}

    </script>
</body>
</html>