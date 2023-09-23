<?php
// Conexión a la base de datos
    $host = "localhost";
    $db_name = "dbMayordomia";
    $username = "root";
    $password = "";
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);

    $queryBoletosApartados = "
    SELECT Boletos.numero_boleto, InfoBoletos.nombre, Boletos.fecha_Compra, Boletos.fecha_Limite 
    FROM Boletos 
    INNER JOIN InfoBoletos ON Boletos.numero_boleto = InfoBoletos.idBoleto
    WHERE Boletos.status = 2";
    $stmtBoletosApartados = $conn->prepare($queryBoletosApartados);
    $stmtBoletosApartados->execute();
    $boletosApartados = $stmtBoletosApartados->fetchAll(PDO::FETCH_ASSOC);
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
                        <a href="/pruebas/menuAdministrador/indexAdministrador.html">
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
                        <a href="/pruebas/menuAdministrador/usuarios/moduloUsers.html">
                            <i class='bx bx-user icon'></i>
                            <span class="text nav-text">Usuarios</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="moduloBoletos.html">
                            <i class='bx bx-purchase-tag-alt icon'></i>
                            <span class="text nav-text">Boletos</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="/pruebas/menuAdministrador/estadisticas/estadisticas.html">
                            <i class='bx bx-bar-chart-square icon'></i>
                            <span class="text nav-text">Estadisticas</span>
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
                    <a href="/pruebas/principal-Administracion.html">
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
            <h2>Boletos Vendidos</h2>
            <table>
                <thead>
                    <tr>
                        <th>Número de Boleto</th>
                        <th>Cliente</th>
                        <th>Fecha de Venta</th>
                        <th>Fecha de Pago</th>
                    </tr>
                </thead>
                <tbody id="sold-tickets-list">
                    <!-- Los boletos vendidos se mostrarán aquí -->
                </tbody>
            </table>

            <h2>Boletos Apartados</h2>
            <table>
                <thead>
                    <tr>
                        <th>Número de Boleto</th>
                        <th>Nombre del Boleto</th>
                        <th>Fecha de Apartado</th>
                        <th>Fecha limite de Pago</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="reserved-tickets-list">
                    <!-- Los boletos apartados se mostrarán aquí -->
                    <?php foreach($boletosApartados as $boleto): ?>
                    <tr>
                        <td><?= $boleto['numero_boleto'] ?></td>
                        <td><?= $boleto['nombre'] ?></td>
                        <td><?= $boleto['fecha_Compra'] ?></td>
                        <td><?= $boleto['fecha_Limite'] ?></td>
                        <td>
                            <!-- Botones para Pagar y Eliminar -->
                            <button onclick="payTicket(<?= $boleto['numero_boleto'] ?>)">Pagar</button>
                            <button onclick="deleteTicket(<?= $boleto['numero_boleto'] ?>)">Eliminar</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h2>Boletos Disponibles</h2>
            <table>
                <thead>
                    <tr>
                        <th>Número de Boleto</th>
                    </tr>
                </thead>
                <tbody id="available-tickets-list">
                    <!-- Los boletos disponibles se mostrarán aquí -->
                </tbody>
            </table>

            <button class="print-button" onclick="printReport()">Imprimir Reporte</button>
        </div>
    </section>

    <script>
        function printReport() {
            window.print();
        }
    </script>

    <script src="/pruebas/menuUsuario/script.js"></script>

</body>
</html>