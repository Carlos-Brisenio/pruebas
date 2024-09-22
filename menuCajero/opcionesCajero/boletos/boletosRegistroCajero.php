<!DOCTYPE html>
<html lang="es">
    <?php
    // Conexión a la base de datos
    $host = "localhost";
    $db_name = "dbMayordomia";
    $username = "root";
    $password = "";
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);

    // Consulta para obtener los códigos postales
    $queryCP = "SELECT DISTINCT codigo_postal FROM Colonias";
    $stmtCP = $conn->prepare($queryCP);
    $stmtCP->execute();

    // Consulta para obtener las colonias
    $queryColonia = "SELECT idColonia, nombre FROM Colonias";
    $stmtColonia = $conn->prepare($queryColonia);
    $stmtColonia->execute();

    $numeroBoleto = "";
    if (isset($_GET['token'])) {
        // Verifica si el token contiene más de un boleto (separado por comas)
        $decodedToken = base64_decode($_GET['token']);
        $tokens = explode(',', $decodedToken);
        $decodedTokens = array_map('base64_decode', $tokens);  // decodifica cada token
        $numeroBoleto = implode(', ', $decodedTokens);  // une los boletos con comas
    }

    if(isset($_POST['apartar_boleto'])) {
        $idBoleto = $_POST['numero-boleto'];
        $nombre = $_POST['nombre_boleto'];
        $ciudad = $_POST['ciudad'];
        $colonia = $_POST['colonia'];
        $calle = $_POST['calle'];
        $numero = $_POST['numero'];
        $colinda1 = $_POST['colinda1'];
        $colinda2 = $_POST['colinda2'];
        $referencia = isset($_POST['referencia']) ? $_POST['referencia'] : null;
        $telefono1 = isset($_POST['telefono1']) ? $_POST['telefono1'] : null;
        $telefono2 = isset($_POST['telefono2']) ? $_POST['telefono2'] : null;
        $correo = isset($_POST['correo']) ? $_POST['correo'] : null;
    
        // Insertar en la base de datos
        $stmt = $conn->prepare("INSERT INTO InfoBoletos (idBoleto, nombre, ciudad, colonia, calle, numero, colinda1, colinda2, referencia, telefono1, telefono2, correo_Electronico) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([$idBoleto, $nombre, $ciudad, $colonia, $calle, $numero, $colinda1, $colinda2, $referencia, $telefono1, $telefono2, $correo]);
        
        // Actualizar la tabla Boletos
        $fechaCompra = date('Y-m-d'); // Fecha actual
        $fechaLimite = date('Y-m-d', strtotime($fechaCompra. ' + 4 days')); // Sumar 4 días a la fecha actual
        
        $stmtBoleto = $conn->prepare("UPDATE Boletos SET status = 2, fecha_Compra = ?, fecha_Limite = ? WHERE idBoleto = ?");
        $stmtBoleto->execute([$fechaCompra, $fechaLimite, $idBoleto]);
        
        // Redireccionar después de insertar
        header("Location: /pruebas/menuCajero/opcionesCajero/boletos/boletosPagoCajero.php?token=".base64_encode($idBoleto));

    }
?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!----======== CSS ======== -->
    <link rel="stylesheet" href="/pruebas/menuUsuario/styleMenu.css">
    <link rel="stylesheet" href="/pruebas/menuUsuario/opcionesUsuario/boletos/resgistro.css">
    
    <!----===== Boxicons CSS ===== -->
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>

    <!-- Link para los jQuerys de la funcion autocompletar-->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    
    <title>Ticket-Mayordomía®</title> 
</head>
<body>
    <nav class="sidebar close">
        <header>
            <div class="image-text">
                <span class="image">
                    <img src="/pruebas/menuUsuario/logoTM.png" alt="">
                </span>

                <div class="text logo-text">
                    <span class="name">USUARIOS</span>
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
                        <a href="#">
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
                <li class="">
                    <a href="javascript:void(0);" onclick="exitAndUpdateStatus();">
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
    <form action="" method="POST">
        <div class="text"> Registro de boleto</div>
        <div class="section">
            <div class="form-row" style="display: flex; justify-content: space-between; align-items: flex-start;">
                <h2 class="section-title">Datos del Boleto</h2>
                <div class="form-group" style="display: flex; align-items: center; text-align: right;">
                    <label for="BuscarRegistro" style="font-weight: bold; margin-right: 10px;">Buscar registro:</label>
                    <input type="text" id="BuscarRegistro" name="BuscarRegistro" placeholder="Ejemplos: C. Federico Del Toro">
                </div>
            </div>
            <p style="font-size: 0.9em; margin-top: 5px; text-align: right; magin-rigth: 20px;">
                <strong>Nota:</strong> La búsqueda se basa en la información recopilada<br> de la venta del año pasado, mediante el domicilio.
            </p>

            <div class="form-row">
                <div class="form-group">
                    <label for="numero-boleto" style="font-weight: bold;">Número(s) de Boleto(s):</label>
                    <input type="text" id="numero-boleto" name="numero-boleto" value="<?php echo $numeroBoleto; ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="nombre_boleto" style="font-weight: bold;">Nombre del Boleto:</label>
                    <input type="text" id="nombre_boleto" name="nombre_boleto" placeholder="Ejemplos: Rafael Martinez, Familia Díaz" required>
                </div>
            </div>
        </div>

          <div class="section">
            <h2 class="section-title">Datos Domiciliarios</h2>
            <div class="form-row">
                <!-- Ciudad -->
                <div class="form-group">
                    <label for="ciudad">Ciudad:</label>
                    <input type="text" id="ciudad" name="ciudad" value="Ciudad Guzmán" readonly>
                </div>

                <!-- Código Postal -->
                <div class="form-group">
                    <label for="codigo_postal">Código Postal:</label>
                    <input type="text" id="codigo_postal" placeholder="Ejemplo: 49000" required oninput="updateColonias()">
                </div>


                <!-- Colonia -->
                <div class="form-group">
                    <label for="colonia">Colonia:</label>
                    <select id="colonia" name="colonia" required>
                        <?php while($colonia = $stmtColonia->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?= $colonia['idColonia'] ?>"><?= $colonia['nombre'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="calle">Calle:</label>
                    <input type="text" id="calle" name="calle" placeholder="Ejemplo: 1ro de mayo" required>
                </div>
                <div class="form-group">
                    <label for="numero">Número:</label>
                    <input type="text" id="numero" name="numero" placeholder="Ejemplo: 32-A" required>
                </div>
                <div class="form-group">
                    <label for="colinda1">Calle Colinda 1:</label>
                    <input type="text" id="colinda1" name="colinda1" placeholder="Ejemplo: San Pedro" required>
                </div>
            </div>
        
            <div class="form-row">
                <div class="form-group">
                    <label for="colinda2">Calle Colinda 2:</label>
                    <input type="text" id="colinda2" name="colinda2" placeholder="Ejemplo: Medellín" required>
                </div>
                <div class="form-group">
                    <label for="referencia">Referencia:</label>
                    <input type="text" id="referencia" name="referencia" placeholder="Ejemplo: A una calle esta un oxxo">
                </div>
            </div>
          </div>
          
          <div class="section">
            <h2 class="section-title">Datos de Contacto</h2>
            <div class="form-row">
                <div class="form-group">
                    <label for="telefono1">Teléfono de Casa:</label>
                    <input type="text" id="telefono1" name="telefono1" placeholder="Ejemplo: 341-41-2-0000">
                </div>
        
                <div class="form-group">
                    <label for="telefono2">Celular:</label>
                    <input type="text" id="telefono2" name="telefono2" placeholder="Ejemplo: 341-101-0000">
                </div>
                <div class="form-group">
                    <label for="correo">Correo Electrónico:</label>
                    <input type="email" id="correo" name="correo" placeholder="Ejemplo: correo@correo.com">
                </div>
          </div>
        </div>
          
        <div class="button-container">
            <button class="button" type="submit" name="apartar_boleto">Apartar Boleto</button>
        </div>
    </form>

    <div class="button-container">
        <button class="button" onclick="updateStatus()">Cancelar</button>
    </div>
    </section>
    
    <script src="/pruebas/menuUsuario/script.js"></script>
    
    <!-- Links para los jQuerys de la funcion autocompletar-->
    <!-- Incluye jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Incluye jQuery UI -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    <script>
        function updateColonias() {
            var cp = document.getElementById("codigo_postal").value;

            if (cp.length == 5) {  // Asume que los códigos postales tienen 5 dígitos
                fetch('/pruebas/menuUsuario/opcionesUsuario/boletos/getColonias.php?codigo_postal=' + cp)
                    .then(response => response.json())
                    .then(data => {
                        var coloniaSelect = document.getElementById("colonia");
                        coloniaSelect.innerHTML = "";  // Limpiar las opciones existentes
                        
                        data.forEach(function(colonia) {
                            var option = document.createElement("option");
                            option.value = colonia;
                            option.text = colonia;
                            coloniaSelect.appendChild(option);
                        });
                    });
            }
        }

        function updateStatus() {
            var numeroBoleto = "<?php echo $numeroBoleto; ?>"; // Obtén el número de boleto desde PHP
            
            // Realiza una solicitud AJAX para actualizar el estado del boleto
            fetch('/pruebas/menuCajero/opcionesCajero/boletos/update2a1.php?numero_boleto=' + numeroBoleto, {
                method: 'POST',
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    //alert('Boleto cancelado exitosamente.');
                    window.location.href = '/pruebas/index.html'; // Redirecciona al usuario a la página principal
                } else {
                    alert('Error al cancelar el boleto.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cancelar el boleto.');
            });
        }

        function exitAndUpdateStatus() {
            var numeroBoleto = "<?php echo $numeroBoleto; ?>"; // Obtén el número de boleto desde PHP

            // Realiza una solicitud AJAX para actualizar el estado del boleto
            fetch('/pruebas/menuCajero/opcionesCajero/boletos/update2a1.php?numero_boleto=' + numeroBoleto, {
                method: 'POST',
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    //alert('Cancelado...');
                } else {
                    alert('Error al cancelar el boleto.');
                }
                window.location.href = '/pruebas/index.html'; // Redirecciona al usuario a la página principal
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cancelar el boleto.');
                window.location.href = '/pruebas/index.html'; // Redirecciona al usuario a la página principal
            });
        }

        //autocomplete rellena los capos de clientes registrados anteriormente
        $(function() {
            $("#nombre_boleto").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "/pruebas/menuCajero/opcionesCajero/boletos/historialClientes.php", // apunta hacia un archivo php para hacer la consulta de datos
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                minLength: 2,
                select: function(event, ui) {
                    if (ui.item.data) {
                        var data = ui.item.data;
                        $("#colonia").val(data.colonia);
                        $("#calle").val(data.calle);
                        $("#numero").val(data.numero);
                        $("#colinda1").val(data.colinda1);
                        $("#colinda2").val(data.colinda2);
                        $("#referencia").val(data.referencia);
                        $("#telefono1").val(data.telefono1);
                        $("#telefono2").val(data.telefono2);
                        $("#correo").val(data.correo_Electronico);
                    }
                }
            });
        });


        //autocomplete rellena los capos de clientes registrados anteriormente mediante domicilio
        $(function() {
            $("#BuscarRegistro").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "/pruebas/menuCajero/opcionesCajero/boletos/historialClientesDomicilio.php", // apunta hacia un archivo php para hacer la consulta de datos
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                minLength: 2,
                select: function(event, ui) {
                    if (ui.item.data) {
                        var data = ui.item.data;
                        $("#nombre_boleto").val(data.nombre);
                        $("#colonia").val(data.colonia);
                        $("#calle").val(data.calle);
                        $("#numero").val(data.numero);
                        $("#colinda1").val(data.colinda1);
                        $("#colinda2").val(data.colinda2);
                        $("#referencia").val(data.referencia);
                        $("#telefono1").val(data.telefono1);
                        $("#telefono2").val(data.telefono2);
                        $("#correo").val(data.correo_Electronico);
                    }
                }
            });
        });
    </script>

</body>
</html>