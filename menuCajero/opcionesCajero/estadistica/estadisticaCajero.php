<?php
// Conexión a la base de datos
    $host = "localhost";
    $db_name = "dbMayordomia";
    $username = "root";
    $password = "";
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);

        // Consulta para obtener boletos apartados
        $queryBoletosApartados = "
        SELECT Boletos.numero_boleto, InfoBoletos.nombre, Boletos.fecha_Compra, Boletos.fecha_Limite 
        FROM Boletos 
        INNER JOIN InfoBoletos ON Boletos.numero_boleto = InfoBoletos.idBoleto
        WHERE Boletos.status = 2";
        $stmtBoletosApartados = $conn->prepare($queryBoletosApartados);
        $stmtBoletosApartados->execute();
        $boletosApartados = $stmtBoletosApartados->fetchAll(PDO::FETCH_ASSOC);

        // Consulta para obtener boletos vendidos
        $queryBoletosVendidos = "
        SELECT Boletos.numero_boleto, InfoBoletos.nombre, Boletos.fecha_Compra, Boletos.fecha_Limite 
        FROM Boletos 
        INNER JOIN InfoBoletos ON Boletos.numero_boleto = InfoBoletos.idBoleto
        WHERE Boletos.status = 3";
        $stmtBoletosVendidos = $conn->prepare($queryBoletosVendidos);
        $stmtBoletosVendidos->execute();
        $boletosVendidos = $stmtBoletosVendidos->fetchAll(PDO::FETCH_ASSOC);
    
        // Consulta para obtener boletos disponibles
        $queryBoletosDisponibles = "
        SELECT numero_boleto
        FROM Boletos
        WHERE status = 1"; // Cambia el valor 1 según corresponda al estado de boletos disponibles en tu base de datos
        $stmtBoletosDisponibles = $conn->prepare($queryBoletosDisponibles);
        $stmtBoletosDisponibles->execute();
        $boletosDisponibles = $stmtBoletosDisponibles->fetchAll(PDO::FETCH_ASSOC);
    
        // Consulta para obtener el total de boletos apartados
        $queryTotalApartados = "
        SELECT COUNT(*) as total_apartados
        FROM Boletos
        WHERE status = 2"; // Suponemos que 2 significa apartado
        $stmtTotalApartados = $conn->prepare($queryTotalApartados);
        $stmtTotalApartados->execute();
        $totalApartados = $stmtTotalApartados->fetch(PDO::FETCH_ASSOC);
    
        // Consulta para obtener el total de boletos disponibles
        $queryTotalDisponibles = "
        SELECT COUNT(*) as total_disponibles
        FROM Boletos
        WHERE status = 1"; // Suponemos que 1 significa disponible
        $stmtTotalDisponibles = $conn->prepare($queryTotalDisponibles);
        $stmtTotalDisponibles->execute();
        $totalDisponibles = $stmtTotalDisponibles->fetch(PDO::FETCH_ASSOC);
    
        // Consulta para obtener el total de boletos vendidos
        $queryTotalVendidos = "
        SELECT COUNT(*) as total_vendidos
        FROM Boletos
        WHERE status = 3"; // Suponemos que 3 significa vendido
        $stmtTotalVendidos = $conn->prepare($queryTotalVendidos);
        $stmtTotalVendidos->execute();
        $totalVendidos = $stmtTotalVendidos->fetch(PDO::FETCH_ASSOC);
    ?>
    
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <!----======== CSS ======== -->
        <link rel="stylesheet" href="/pruebas/menuUsuario/styleMenu.css">
        <link rel="stylesheet" href="estilosBoletos.css">
        
        <!----===== Boxicons CSS ===== -->
        <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

        <!-- Asegúrate de que la ruta de la imagen sea la correcta -->
        <img id="imagenParaPdf" src="/pruebas/bannerV2.png" style="display: none;">
        
        
        <title>Ticket-Mayordomía®/Boletos</title> 
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
                    <a href="/pruebas/menuCajero/indexCajero.html">
                        <i class='bx bx-home-alt icon' ></i>
                        <span class="text nav-text">Inicio</span>
                    </a>
                </li>

                <li class="nav-link">
                    <a href="/pruebas/menuCajero/opcionesCajero/estadistica/estadisticaCajero.php">
                        <i class='bx bx-purchase-tag-alt icon'></i>
                        <span class="text nav-text">Boletos</span>
                    </a>
                </li>

                <li class="nav-link">
                    <a href="/pruebas/menuCajero/opcionesCajero/boletos/boletosCajero.php">
                        <i class='bx bx-cart-alt icon' ></i>
                        <span class="text nav-text">Vender boletos</span>
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
                    <li class="nav-link">
                        <a href="/pruebas/principal-Cajeros.php">
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
            <div class="tickets-container">
                <h2>Boletos Apartados</h2>
        
                <table id="boletosTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Número de Boleto</th>
                            <th>Nombre del Boleto</th>
                            <th>Fecha de Apartado</th>
                            <th>Fecha límite de Pago</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($boletosApartados as $boleto): ?>
                            <tr>
                                <td><?= $boleto['numero_boleto'] ?></td>
                                <td><?= $boleto['nombre'] ?></td>
                                <td><?= $boleto['fecha_Compra'] ?></td>
                                <td><?= $boleto['fecha_Limite'] ?></td>
                                <td>
                                    <button onclick="payTicket(<?= $boleto['numero_boleto'] ?>)">Pagar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <br>
                <h2>Boletos Disponibles</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Boletos Apartados</th>
                            <th>Boletos disponibles</th>
                            <th>Boletos vendidos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $totalApartados['total_apartados'] ?></td>
                            <td><?= $totalDisponibles['total_disponibles'] ?></td>
                            <td><?= $totalVendidos['total_vendidos'] ?></td>
                        </tr>
                    </tbody>
                </table>

                <h2>Boletos Vendidos</h2>
        
                <table id="boletosVendidosTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Número de Boleto</th>
                            <th>Nombre del Boleto</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($boletosVendidos as $boleto): ?>
                            <tr>
                                <td><?= $boleto['numero_boleto'] ?></td>
                                <td><?= $boleto['nombre'] ?></td>
                                <td>
                                    <button class="button" id="generarBoletos" name="generarBoletos" onclick="imprimirBoletos(<?= $boleto['numero_boleto'] ?>)">Imprimir boleto</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
    
        </section>
        
    <script>
        $(document).ready(function() {
                $("#menu-toggle").click(function() {
                    $("#sidebar").toggleClass("active");
                });
                var boletosTable = $('#boletosTable').DataTable({
                    "searching": true,
                    "searchMinLength": 1,
                });
    
                var boletosDisponiblesTable = $('#boletosDisponiblesTable').DataTable({
                    "searching": false, // Deshabilita la búsqueda en esta tabla
                });
    
                // Agregar funcionalidad de búsqueda personalizada para boletos apartados
                $('#search').on('keyup', function() {
                    boletosTable.search(this.value).draw();
             });
        });
    
        function payTicket(numero_boleto) {
            $.ajax({
                url: 'pagarBoleto.php',
                type: 'POST',
                data: { numero_boleto: numero_boleto },
                success: function(response) {
                    alert('Boleto pagado correctamente.');
                    location.reload(); // Recargar la página para actualizar la lista de boletos
                },
                error: function(error) {
                    alert('Error al pagar el boleto.');
                }
            });
        }

        function imprimirBoletos(numero_boleto) {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // Realizar una nueva consulta para obtener los detalles del boleto desde la tabla InfoBoletos
            $.ajax({
                url: 'obtenerDetallesBoleto.php', // Crea este archivo para realizar la consulta SQL
                type: 'POST',
                data: { numero_boleto: numero_boleto },
                success: function(response) {
                    const detallesBoleto = JSON.parse(response);
                    const meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
                    const fechaActual = new Date();
                    const nombreMes = meses[fechaActual.getMonth()];
                    const fechaFormateada = fechaActual.getDate() + " de " + nombreMes + " de " + fechaActual.getFullYear();

                    if (detallesBoleto) {
                //--> Aqui inicia boleto Original
                        doc.setFontSize(12);
                        doc.text(75, 15, 'Mayordomía Señor San José 2024');  
                        doc.text(90, 25, 'Comprobante de pago');  

                        const img = document.getElementById('imagenParaPdf');
                        const canvas = document.createElement('canvas');
                        canvas.width = img.width;
                        canvas.height = img.height;

                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0);

                        const imgData = canvas.toDataURL('image/png', 1.0);
                        doc.addImage(imgData, 'JPEG', 20, 15, 55, 50);  

                        doc.text(130, 45, 'Número de boleto: ' + numero_boleto);  
                        doc.text(130, 55, 'Fecha: ' + fechaFormateada);  
                        doc.text(20, 75, 'Nombre: ' + detallesBoleto.nombre);  
                        doc.text(20, 85, 'Calle: ' + detallesBoleto.calle);  
                        doc.text(120, 85, 'Número: ' + detallesBoleto.numero);  
                        doc.text(20, 95, 'Ciudad: ' + detallesBoleto.ciudad);  
                        doc.text(120, 95, 'Colonia: ' + detallesBoleto.colonia);  
                        doc.text(20, 105, 'Telefono: ' + detallesBoleto.telefono1);  

                        doc.text(20, 115, '$170.00 (ciento setenta pesos 00/100 m.n.)');  
                        doc.text(70, 125, '50% para el culto de Señor San José');  
                        doc.text(72, 135, '50% para gastos de la mayordomía');  
                        doc.text(180, 135, 'Original');  
                        doc.rect(10, 5, 190, 135);  
                //--> Aqui termina boleto Original
                        doc.text(0,148,'_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _');  
                //--> Aqui inicia boleto Copia
                        doc.rect(10, 155, 190, 135);  
                        doc.addImage(imgData, 'JPEG', 20, 165, 55, 50);  

                        doc.text(75, 165, 'Mayordomía Señor San José 2024');  
                        doc.text(90, 175, 'Comprobante de pago');  

                        doc.text(130, 195, 'Número de boleto: ' + numero_boleto);  
                        doc.text(130, 205, 'Fecha: ' + fechaFormateada);  
                        doc.text(20, 225, 'Nombre: ' + detallesBoleto.nombre);  
                        doc.text(20, 235, 'Calle: ' + detallesBoleto.calle);  
                        doc.text(120, 235, 'Número: ' + detallesBoleto.numero);  
                        doc.text(20, 245, 'Ciudad: ' + detallesBoleto.ciudad);  
                        doc.text(120, 245, 'Colonia: ' + detallesBoleto.colonia);  
                        doc.text(20, 255, 'Telefono: ' + detallesBoleto.telefono1);  

                        doc.text(20, 265, '$170.00 (ciento setenta pesos 00/100 m.n.)');  
                        doc.text(70, 275, '50% para el culto de Señor San José');  
                        doc.text(72, 285, '50% para gastos de la mayordomía');  
                        doc.text(180, 285, 'Copia');  

                //--> Aqui termina boleto Copia

                        // Guardar el PDF con el nombre que quieras
                        doc.save('Boleto_' + numero_boleto + '.pdf');
                    } else {
                        alert('No se encontraron detalles para el boleto.');
                    }
                },
                error: function(error) {
                    alert('Error al obtener los detalles del boleto.');
                }
            });
        }


    </script>
        <script src="/pruebas/menuUsuario/script.js"></script>
    
    </body>
    </html>