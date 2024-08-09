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
    
    // Consulta para obtener boletos vendidos
    $queryBoletosVendidos = "
        SELECT 
            Boletos.numero_boleto, InfoBoletos.nombre, InfoBoletos.telefono1, InfoBoletos.telefono2, InfoBoletos.colonia, InfoBoletos.calle, InfoBoletos.numero, 
            Boletos.fecha_Compra, Boletos.fecha_Limite, Ventas.idVenta, Ventas.idUsuario, Ventas.fecha_venta
        FROM Boletos 
        INNER JOIN InfoBoletos ON Boletos.numero_boleto = InfoBoletos.idBoleto
        INNER JOIN Ventas ON Boletos.numero_boleto = Ventas.idBoletos
        WHERE Boletos.status = 3";
    $stmtBoletosVendidos = $conn->prepare($queryBoletosVendidos);
    $stmtBoletosVendidos->execute();
    $boletosVendidos = $stmtBoletosVendidos->fetchAll(PDO::FETCH_ASSOC);

    
    function formatPhoneNumber($number) {
        if (strlen($number) == 10) {
            return substr($number, 0, 3) . '-' . substr($number, 3, 3) . '-' . substr($number, 6, 4);
        }
        return $number;
    }
    
    function formatearFecha($fechaOriginal) {
    // Convierte la fecha de string a objeto DateTime
        $fechaObjeto = DateTime::createFromFormat('Y-m-d H:i:s', $fechaOriginal);
    
        // Si la fecha no es válida, retorna un mensaje de error
        if (!$fechaObjeto) {
            return "Fecha inválida";
        }
    
        // Formatea la fecha en el formato deseado
        return $fechaObjeto->format('d-m-Y H:i');
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
    <link rel="stylesheet" href="estiloboletos.css">
    
    <!----===== Boxicons CSS ===== -->
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>


    <!-- Asegúrate de que la ruta de la imagen sea la correcta -->
    <img id="imagenParaPdf" src="/pruebas/bannerV2.png" style="display: none;">
    
    <title>Mayordomía Tickets®/Admin Boletos</title> 
</head>
<body>
    <style>
        .ocultar-columna {
            display: none;
        }
    </style>
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
                        <a href="adminboletos.php">
                            <i class='bx bx-purchase-tag-alt icon'></i>
                            <span class="text nav-text">Boletos</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="/pruebas/menuAdministrador/estadisticas/estadisticas.php">
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
                    <a href="/pruebas/menuAdministrador/logout.php">
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
            
            <h2>Boletos Apartados</h2>
            <table id="boletosTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="ocultar-columna">Fecha de Apartado</th>
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
                            <td class="ocultar-columna"><?= formatearFecha($boleto['fecha_Compra']) ?></td>
                            <td><?= $boleto['numero_boleto'] ?></td>
                            <td><?= $boleto['nombre'] ?></td>
                            <td><?= formatearFecha($boleto['fecha_Compra']) ?></td>
                            <td><?= formatearFecha($boleto['fecha_Limite']) ?></td>
                            <td>
                                <div class="boton-pagar-eliminar">
                                    <button class="btn-pagar" onclick="payTicket(<?= $boleto['numero_boleto'] ?>)">Pagar</button>
                                    <button class="btn-eliminar" onclick="deleteTicket(<?= $boleto['numero_boleto'] ?>)">Eliminar</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <h2>Boletos Vendidos</h2>
        
                <table id="boletosVendidosTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="ocultar-columna">Fecha de compra</th>
                            <th>Número de Boleto</th>
                            <th>Nombre del Boleto</th>
                            <th>Fecha de compra</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($boletosVendidos as $boleto): ?>
                            <tr>
                                <td class="ocultar-columna"><?= formatearFecha($boleto['fecha_venta']) ?></td>
                                <td><?= $boleto['numero_boleto'] ?></td>
                                <td><?= $boleto['nombre'] ?></td>
                                <td><?= formatearFecha($boleto['fecha_venta']) ?></td>
                                <td>
                                    <button class="button" id="generarBoletos" name="generarBoletos" onclick="imprimirBoletos(<?= $boleto['numero_boleto'] ?>)">Imprimir</button>
                                    <button class="btn-devolver" onclick="devolverBoleto(<?= $boleto['numero_boleto'] ?>)">Devolver</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
        </div>

    </section>
    
    <div id="devolverModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Autenticación</h2>
            <p>Ingrese usuario y contraseña para confirmar:</p>
            <input type="text" id="user" placeholder="Usuario">
            <input type="password" id="pass" placeholder="Contraseña">
            <input type="hidden" id="numeroBoletoDevolver" value="">
            <button onclick="confirmarDevolver()">Aceptar</button>
            <button onclick="cerrarModal()">Cancelar</button>
        </div>
    </div>

    
<script>
    var modal = document.getElementById("devolverModal");
    var span = document.getElementsByClassName("close")[0];
    
    function abrirModal(numero_boleto) {
        document.getElementById("numeroBoletoDevolver").value = numero_boleto;
        modal.style.display = "block";
    }
    
    span.onclick = function() {
        modal.style.display = "none";
    }
    
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    
    function confirmarDevolver() {
        let user = document.getElementById("user").value;
        let pass = document.getElementById("pass").value;
        let numero_boleto = document.getElementById("numeroBoletoDevolver").value;
    
        $.ajax({
            url: 'validar_usuario.php',
            type: 'POST',
            data: {
                user: user,
                pass: pass,
                numero_boleto: numero_boleto
            },
            success: function(response) {
                if (response == "success") {
                    devolverBoletoFuncion(numero_boleto);
                } else {
                    alert('Usuario o contraseña incorrectos.');
                }
            },
            error: function(error) {
                alert('Error al validar el usuario.');
            }
        });
    }
    
    function cerrarModal() {
        modal.style.display = "none";
    }
    



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
    
    $(document).ready(function() {
        
            // Configuración de DataTables para la tabla de boletos vendidos
            var boletosVendidosTable = $('#boletosVendidosTable').DataTable({
                "searching": true,
                "searchMinLength": 1,
            });
        
            // Agregar funcionalidad de búsqueda personalizada para boletos vendidos
            $('#searchVendidos').on('keyup', function() {
                boletosVendidosTable.search(this.value).draw();
            });
        });

    function payTicket(numero_boleto) {
        var confirmation = confirm("¿Realmente deseas pagar el boleto "+numero_boleto+"?");
        if (confirmation) {
            $.ajax({
                url: 'pagar_boleto.php',
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
    }

    function deleteTicket(numero_boleto) {
        var confirmation = confirm("¿Realmente deseas eliminar el boleto "+numero_boleto+"?");
    
        if (confirmation) {
            $.ajax({
                url: 'eliminar_boleto.php',
                type: 'POST',
                data: { numero_boleto: numero_boleto },
                success: function(response) {
                    alert('Boleto eliminado correctamente.');
                    location.reload(); // Recargar la página para actualizar la lista de boletos
                },
                error: function(error) {
                    alert('Error al eliminar el boleto.');
                }
            });
        } else {
            // El usuario decidió no continuar con la eliminación
            console.log("Eliminación del boleto " + numero_boleto + " cancelada por el usuario.");
        }
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
                        doc.text(20, 105, 'Telefono 1: ' + detallesBoleto.telefono1);
                        doc.text(120, 105, 'Telefono 2: ' + detallesBoleto.telefono2);

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
                        doc.text(20, 255, 'Telefono 1: ' + detallesBoleto.telefono1);
                        doc.text(120, 255, 'Telefono 2: ' + detallesBoleto.telefono2); 

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
        
        function devolverBoleto(numero_boleto) {
            abrirModal(numero_boleto);
        }
        
        function devolverBoletoFuncion(numero_boleto) {
                $.ajax({
                    url: 'devolver_boleto.php',
                    type: 'POST',
                    data: { numero_boleto: numero_boleto },
                    success: function(response) {
                        alert('Boleto devuelto correctamente.');
                        location.reload(); // Recargar la página para actualizar la lista de boletos
                    },
                    error: function(error) {
                        alert('Error al devolver el boleto.');
                    }
                });
        }



</script>


    <script src="/pruebas/menuUsuario/script.js"></script>
    <script src="/pruebas/menuAdministrador/autologout.js"></script>
</body>
</html>