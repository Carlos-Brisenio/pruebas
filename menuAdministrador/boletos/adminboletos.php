<?php
// Conexión a la base de datos
    $host = "localhost";
    $db_name = "u833492021_dbMayordomia";
    $username = "u833492021_root";
    $password = "#kDbV9r>9UJ5";
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!----======== CSS ======== -->
    <link rel="stylesheet" href="/menuUsuario/styleMenu.css">
    <link rel="stylesheet" href="estiloboletos.css">
    
    <!----===== Boxicons CSS ===== -->
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

    
    
    <title>Ticket-Mayordomía®/Boletos</title> 
</head>
<body>
    <nav class="sidebar close">
        <header>
            <div class="image-text">
                <span class="image">
                    <img src="/menuUsuario/logoTM.png" alt="">
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
                        <a href="/menuAdministrador/indexAdministrador.php">
                            <i class='bx bx-home-alt icon' ></i>
                            <span class="text nav-text">Inicio</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="/menuAdministrador/datos/misDatos.html">
                            <i class='bx bx-data icon'></i>
                            <span class="text nav-text">Mis datos</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="/menuAdministrador/usuarios/Usuarios.php">
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
                        <a href="/menuAdministrador/estadisticas/estadisticas.php">
                            <i class='bx bx-bar-chart-square icon'></i>
                            <span class="text nav-text">Estadisticas</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="/menuAdministrador/ajustes/moduloAjustes.html">
                            <i class='bx bx-cog icon' ></i>
                            <span class="text nav-text">Ajustes</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="bottom-content">
                <li class="">
                    <a href="/principal-Administracion.php">
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
                                <button onclick="deleteTicket(<?= $boleto['numero_boleto'] ?>)">Eliminar</button>
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
    }

    function deleteTicket(numero_boleto) {
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
    }

</script>


    <script src="/menuUsuario/script.js"></script>

</body>
</html>