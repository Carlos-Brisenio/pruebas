<?php
// Conexión a la base de datos
    $host = "localhost";
    $db_name = "dbMayordomia";
    $username = "root";
    $password = "";
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);

    // Consulta para obtener boletos vendidos
    $queryBoletosVendidos = "
        SELECT 
            Boletos.numero_boleto, InfoBoletos.nombre, InfoBoletos.telefono1, InfoBoletos.telefono2, InfoBoletos.colonia, InfoBoletos.calle, InfoBoletos.numero, 
            Boletos.fecha_Compra, Boletos.fecha_Limite, Ventas.idVenta,Ventas.idUsuario,Ventas.fecha_venta
        FROM Boletos 
        INNER JOIN InfoBoletos ON Boletos.numero_boleto = InfoBoletos.idBoleto
        INNER JOIN Ventas ON Boletos.numero_boleto = Ventas.idBoletos
        WHERE Boletos.status = 3";
    $stmtBoletosVendidos = $conn->prepare($queryBoletosVendidos);
    $stmtBoletosVendidos->execute();
    $boletosVendidos = $stmtBoletosVendidos->fetchAll(PDO::FETCH_ASSOC);

    // Consulta para obtener boletos apartados
    $queryBoletosApartados = "
    SELECT Boletos.numero_boleto, InfoBoletos.nombre, InfoBoletos.telefono1, InfoBoletos.telefono2, InfoBoletos.colonia, InfoBoletos.calle, InfoBoletos.numero, Boletos.fecha_Compra, Boletos.fecha_Limite 
    FROM Boletos 
    INNER JOIN InfoBoletos ON Boletos.numero_boleto = InfoBoletos.idBoleto
    WHERE Boletos.status = 2";
    $stmtBoletosApartados = $conn->prepare($queryBoletosApartados);
    $stmtBoletosApartados->execute();
    $boletosApartados = $stmtBoletosApartados->fetchAll(PDO::FETCH_ASSOC);

    
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
    
    if (isset($_POST['startDate']) && isset($_POST['endDate'])) {
        $startDate = $_POST['startDate'];
        $endDate = $_POST['endDate'];
        $queryBoletosVendidos .= " AND Ventas.fecha_venta BETWEEN :startDate AND :endDate";
    }
    $stmtBoletosVendidos = $conn->prepare($queryBoletosVendidos);
    if (isset($startDate) && isset($endDate)) {
        $stmtBoletosVendidos->bindParam(':startDate', $startDate);
        $stmtBoletosVendidos->bindParam(':endDate', $endDate);
    }
    $stmtBoletosVendidos->execute();


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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>

    
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
                        <a href="/pruebas/menuAdministrador/ajustes/moduloAjustes.html">
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
        <div class="tickets-container">
                <h2>Reporte de boletos Vendidos</h2>
                <div class="report-container">
                    <label for="startDate">Fecha inicio:</label>
                    <input type="date" id="startDate">
                
                    <label for="endDate">Fecha fin:</label>
                    <input type="date" id="endDate">
                    <br><br>
                    <button id="generateReport">Generar Reporte</button>
                </div>
                <h2>Información boletos Vendidos</h2>
        
                <table id="boletosVendidosTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Número de Boleto</th>
                            <th>Nombre del Boleto</th>
                            <th>Fecha de compra</th>
                            <th>Teléfono 1</th>
                            <th>Teléfono 2</th>
                            <th>Domicilio</th>
                            <th>Colonia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($boletosVendidos as $boleto): ?>
                            <tr>
                                <td><?= $boleto['numero_boleto'] ?></td>
                                <td><?= $boleto['nombre'] ?></td>
                                <td><?= formatearFecha($boleto['fecha_venta']) ?></td>
                                <td><?= formatPhoneNumber($boleto['telefono1']) ?></td>
                                <td><?= formatPhoneNumber($boleto['telefono2']) ?></td>
                                <td><?= $boleto['calle'] ?> #<?= $boleto['numero'] ?></td>
                                <td><?= $boleto['colonia'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <br>
                <h2>Información boletos Apartados</h2>
        
                <table id="boletosTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="ocultar-columna">Fecha de apartado</th>
                            <th>Número de Boleto</th>
                            <th>Nombre del Boleto</th>
                            <th>Fecha de apartado</th>
                            <th>Teléfono 1</th>
                            <th>Teléfono 2</th>
                            <th>Domicilio</th>
                            <th>Colonia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($boletosApartados as $boleto): ?>
                            <tr>
                                <td class="ocultar-columna"><?= formatearFecha($boleto['fecha_Compra']) ?></td>
                                <td><?= $boleto['numero_boleto'] ?></td>
                                <td><?= $boleto['nombre'] ?></td>
                                <td><?= formatearFecha($boleto['fecha_Compra']) ?></td>
                                <td><?= formatPhoneNumber($boleto['telefono1']) ?></td>
                                <td><?= formatPhoneNumber($boleto['telefono2']) ?></td>
                                <td><?= $boleto['calle'] ?> #<?= $boleto['numero'] ?></td>
                                <td><?= $boleto['colonia'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
        </div>

    </section>
    
    <script>
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
            
            document.getElementById('generateReport').addEventListener('click', function() {
                let startDate = document.getElementById('startDate').value;
                let endDate = document.getElementById('endDate').value;
            
                fetch('getFilteredTickets.php', {
                    method: 'POST',
                    body: new URLSearchParams(`startDate=${startDate}&endDate=${endDate}`),
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF();
                    doc.text("Reporte de Boletos Vendidos", 70, 10);
                    doc.text(`Periodo: ${formatDate(addOneDay(startDate))} a ${formatDate(addOneDay(endDate))}`, 63, 20); 
            
                    // Usamos autoTable para dibujar una tabla en el PDF
                    doc.autoTable({
                        startY: 30,
                        head: [['Número', 'Nombre', 'Domicilio', 'Referencia']], 
                        body: data.map(boleto => [
                            boleto.numero_boleto, 
                            boleto.nombre, 
                            `${boleto.calle} #${boleto.numero}, ${boleto.colonia}`, // Concatena la calle, "#" y el número
                            boleto.referencia
                        ]), 
                        styles: { fillColor: [255, 255, 255] }, 
                        headStyles: { fillColor: [0, 128, 0] }  
                    });
                    doc.save('reporte_boletos.pdf');
                })
                .catch(error => console.error('Error:', error));
            });

            
            function formatDate(dateString) {
                const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
                return new Date(dateString).toLocaleDateString(undefined, options);
            }
            function addOneDay(dateString) {
                let date = new Date(dateString);
                date.setDate(date.getDate() + 1);
                return date.toISOString().slice(0, 10); // Retorna la fecha en formato YYYY-MM-DD
            }


    </script>



    <script src="/pruebas/menuUsuario/script.js"></script>

</body>
</html>