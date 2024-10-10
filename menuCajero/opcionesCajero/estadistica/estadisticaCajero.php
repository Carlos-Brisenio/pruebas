<?php
// Conexión a la base de datos
    $host = "localhost";
    $db_name = "dbMayordomia";
    $username = "root";
    $password = "";
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);

        // Consulta para obtener boletos apartados
        $queryBoletosApartados = "
        SELECT Boletos.numero_boleto, InfoBoletos.nombre, InfoBoletos.telefono1, Boletos.fecha_Compra, Boletos.fecha_Limite 
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

        function formatPhoneNumber($number) {
            if (strlen($number) == 10) {
                return substr($number, 0, 3) . '-' . substr($number, 3, 3) . '-' . substr($number, 6, 4);
            }
            return $number;
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
                            <th>Télefono</th>
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
                                <td><?= formatPhoneNumber($boleto['telefono1']) ?></td>
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
    
        /*function payTicket(numero_boleto) {
            var confirmation = confirm("¿Realmente deseas pagar el boleto "+numero_boleto+"?");
            if (confirmation) {
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
            } else {
                // El usuario decidió no continuar con el pago
                console.log("Pago del boleto " + numero_boleto + " cancelado por el usuario.");
            }
        }*/
       // Abrir el modal de pago
function payTicket(numero_boleto) {
    // Guardamos el número de boleto en un atributo data para utilizarlo luego
    $('#modalPago').data('boleto', numero_boleto);
    $('#modalPago').show();
}

// Cerrar modales
function closeModal(modalId) {
    // Cerrar el modal ocultándolo
    $('#' + modalId).hide();

    // Recargar la página después de cerrar el modal
    if (modalId === 'modalCambio') {
        location.reload();  // Esto recargará la página después de cerrar el modal de cambio
    }
}


function confirmarPago() {
    var numero_boleto = $('#modalPago').data('boleto');
    var recibe = parseFloat($('#recibe').val());
    var precio = 180;
    var formaPago = $('#formaPago').val();
    var cambio = recibe - precio;

    // Cerrar el modal de pago antes de realizar la solicitud
    closeModal('modalPago');

    // Llamada AJAX para procesar el pago en el servidor
    $.ajax({
        url: 'pagarBoleto.php',
        type: 'POST',
        data: {
            numero_boleto: numero_boleto,
            precio: precio,
            forma_pago: formaPago
        },
        success: function(response) {
            // Mostrar el modal de cambio con el resumen del pago una vez que el pago ha sido procesado
            $('#detallePago').html('Número de boleto: ' + numero_boleto + '<br>Precio: $180<br>Forma de Pago: ' + formaPago);
            $('#cambioMonto').html('Cambio: $' + cambio.toFixed(2));

            // Mostrar el modal de cambio
            $('#modalCambio').show();
            
            // Ya no hay límite de tiempo para cerrar el modal, se cierra con el botón "Cerrar"
        },
        error: function(error) {
            alert('Error al procesar el pago.');
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
                        doc.text(78, 15, 'Mayordomía Señor San José 2025');
                        doc.text(92, 21, 'Mayordomía Tickets');     
                        doc.text(90, 27, 'Comprobante de pago');  

                        const img = document.getElementById('imagenParaPdf');
                        const canvas = document.createElement('canvas');
                        canvas.width = img.width;
                        canvas.height = img.height;

                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0);

                        const imgData = canvas.toDataURL('image/png', 1.0);
                        doc.addImage(imgData, 'JPEG', 20, 15, 55, 50);  

                        doc.text(130, 45, 'Número de boleto: ' + numero_boleto);  
                        doc.text(130, 52, 'Fecha: ' + fechaFormateada);  
                        doc.text(20, 74, 'Nombre: ' + detallesBoleto.nombre);  
                        doc.text(20, 81, 'Calle: ' + detallesBoleto.calle);  
                        doc.text(120, 81, 'Número: ' + detallesBoleto.numero);  
                        doc.text(20, 88, 'Ciudad: ' + detallesBoleto.ciudad);  
                        doc.text(120, 88, 'Colonia: ' + detallesBoleto.colonia);  
                        doc.text(20, 95, 'Télefono: ' + detallesBoleto.telefono1);
                        doc.text(120, 95, 'Télefono 2: ' + detallesBoleto.telefono2);  

                        doc.text(20, 106, '$180.00 (ciento ochenta pesos 00/100 m.n.)');  
                        doc.text(70, 116, '50% para el culto de Señor San José');  
                        doc.text(72, 124, '50% para gastos de la mayordomía');
                        doc.text(77, 135, 'boletos.mayordomiatickets.com');  
                        doc.text(180, 135, 'Original');  
                        doc.rect(10, 5, 190, 135);  
                //--> Aqui termina boleto Original
                        doc.text(0,148,'_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _');  
                //--> Aqui inicia boleto Copia
                        doc.rect(10, 155, 190, 135);  
                        doc.addImage(imgData, 'JPEG', 20, 165, 55, 50);  

                        doc.text(78, 165, 'Mayordomía Señor San José 2025'); 
                        doc.text(92, 171, 'Mayordomía Tickets');   
                        doc.text(90, 177, 'Comprobante de pago');  

                        doc.text(130, 195, 'Número de boleto: ' + numero_boleto);  
                        doc.text(130, 202, 'Fecha: ' + fechaFormateada);  
                        doc.text(20, 225, 'Nombre: ' + detallesBoleto.nombre);  
                        doc.text(20, 232, 'Calle: ' + detallesBoleto.calle);  
                        doc.text(120, 232, 'Número: ' + detallesBoleto.numero);  
                        doc.text(20, 239, 'Ciudad: ' + detallesBoleto.ciudad);  
                        doc.text(120, 239, 'Colonia: ' + detallesBoleto.colonia);  
                        doc.text(20, 246, 'Télefono: ' + detallesBoleto.telefono1);
                        doc.text(120, 246, 'Télefono 2: ' + detallesBoleto.telefono2);  
  
                        doc.text(20, 257, '$180.00 (ciento ochenta pesos 00/100 m.n.)');  
                        doc.text(70, 267, '50% para el culto de Señor San José');  
                        doc.text(72, 275, '50% para gastos de la mayordomía');
                        doc.text(77, 286, 'boletos.mayordomiatickets.com');    
                        doc.text(180, 285, 'Copia');
                        doc.setFont("helvetica", "bold");
                        doc.text(15,295, 'Nota:')
                        doc.setFont("helvetica", "normal");
                        doc.text(27,295, 'Conserva este boleto y cámbialo por una décima en las próximas fiestas en Octubre 2025.')

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

    <!-- Modal de Pago -->
<div id="modalPago" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal('modalPago')">&times;</span>
        <h2>Confirmar Pago</h2>
        <form id="pagoForm">
            <label for="cantidad">Cantidad:</label>
            <input type="text" id="cantidad" name="cantidad" value="1" readonly><br><br>

            <label for="precio">Precio:</label>
            <input type="text" id="precio" name="precio" value="180" readonly><br><br>

            <label for="formaPago">Forma de Pago:</label>
            <select id="formaPago" name="formaPago">
                <option value="1">Efectivo</option>
                <option value="2">Transferencia/Banco</option>
                <option value="3">Tarjeta</option>
            </select><br><br>

            <label for="recibe">Recibe:</label>
            <input type="number" id="recibe" name="recibe" required><br><br>

            <button type="button" onclick="confirmarPago()">Pagar</button>
            <button type="button" onclick="closeModal('modalPago')">Cancelar</button>
        </form>
    </div>
</div>

<!-- Modal de Cambio -->
<div id="modalCambio" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal('modalCambio')">&times;</span>
        <h2>Pago Realizado</h2>
        <p>Se ha realizado el pago correctamente.</p>
        <p id="detallePago"></p>
        <p id="cambioMonto"></p>
        <!-- El botón de cerrar será el único medio para cerrar el modal -->
        <button type="button" onclick="closeModal('modalCambio')">Cerrar</button>
    </div>
</div>

<!-- Estilos para el modal -->
<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
    }
    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 30%;
    }
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }
    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>
