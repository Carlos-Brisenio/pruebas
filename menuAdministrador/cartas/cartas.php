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
                <button class="pre-invitacion">Imprimir Pre-invitaciónes</button>
                <button class="editar">Editar</button>
                <button class="guardar">Guardar</button>
            </div>

        </div>
    </section>
    <script src="/pruebas/menuUsuario/script.js"></script>

</body>
</html>