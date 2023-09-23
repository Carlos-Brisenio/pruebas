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
    if(isset($_GET['numero-boleto'])) {
        $numeroBoleto = $_GET['numero-boleto'];
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
        header("Location: /pruebas/menuCajero/opcionesCajero/boletos/boletosPagoCajero.html");
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
                        <a href="/pruebas/menuUsuario/opcionesUsuario/boletos/boletosUsuario.html">
                            <i class='bx bx-purchase-tag-alt icon'></i>
                            <span class="text nav-text">Boletos</span>
                        </a>
                    </li>

                </ul>
            </div>

            <div class="bottom-content">
                <li class="">
                    <a href="/pruebas/index.html">
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
            <h2 class="section-title">Datos del Boleto</h2>
            <div class="form-row">
              <div class="form-group">
                <label for="numero-boleto">Número(s) de Boleto(s):</label>
                <input type="text" id="numero-boleto" name="numero-boleto" value="<?php echo $numeroBoleto; ?>">

              </div>
              <div class="form-group">
                <label for="nombre_boleto">Nombre del Boleto:</label>
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
                    <input type="text" id="ciudad" name="ciudad" value="Ciudad Guzmán" required>
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
                    <input type="text" id="referencia" name="referencia" placeholder="Ejemplo: A una calle esta un oxxo" required>
                </div>
            </div>
          </div>
          
          <div class="section">
            <h2 class="section-title">Datos de Contacto</h2>
            <div class="form-row">
                <div class="form-group">
                    <label for="telefono1">Teléfono de Casa:</label>
                    <input type="text" id="telefono1" name="telefono1" placeholder="Ejemplo: 341-41-2-0000" required>
                </div>
        
                <div class="form-group">
                    <label for="telefono2">Celular:</label>
                    <input type="text" id="telefono2" name="telefono2" placeholder="Ejemplo: 341-101-0000" required>
                </div>
                <div class="form-group">
                    <label for="correo">Correo Electrónico:</label>
                    <input type="email" id="correo" name="correo" placeholder="Ejemplo: correo@correo.com"required>
                </div>
          </div>
        </div>
          
        <div class="button-container">
            <button class="button" type="submit" name="apartar_boleto">Apartar Boleto</button>
        </div>

          <div class="button-container">
            <button class="button" onclick="window.location.href='/pruebas/index.html'">Cancelar</button>
          </div>
    </form>
    </section>
    

    

    <script src="/pruebas/menuUsuario/script.js"></script>
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
    </script>

</body>
</html>