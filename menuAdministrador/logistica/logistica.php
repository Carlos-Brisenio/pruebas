<?php

    //session_start();
    /*if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["isLoggedIn"] !== true) {
        header("Location: /pruebas/principal-Logistica.php"); // Reemplaza 'login.php' con el nombre de tu archivo de inicio de sesión si es diferente
        exit;
    }*/

// Conexión a la base de datos
    $host = "localhost";
    $db_name = "dbMayordomia";
    $username = "root";
    $password = "";

//try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
  /*  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}*/

// Función para truncar el campo nombres
    function truncarNombres($nombre, $maxLength = 45) {
        if (mb_strlen($nombre) > $maxLength) {
            return mb_substr($nombre, 0, $maxLength) . '...';
        }
        return $nombre;
    }

    // Consulta para obtener rutas SOLO del proceso = año actual
    $queryRutasTable = "
        SELECT
            idRutas, 
            ruta,
            recorrido,
            nombres,
            domicilio,
            numeroBoletos,
            proceso
        FROM Rutas
        WHERE proceso = YEAR(CURDATE())
    ";

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

    // Consulta para obtener usuarios tipo 3
    $queryUsuarios = "SELECT idUsuario, nombre FROM Usuarios WHERE idTipoUsuario = 3";
    $stmtUsuarios = $conn->prepare($queryUsuarios);
    $stmtUsuarios->execute();
    $usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);


    // Consulta para obtener rutas SOLO del proceso = año actual para infoLogistica
    $queryInfoLogisticaTable = "
        SELECT
            idRutas, 
            ruta,
            recorrido,
            nombres,
            domicilio,
            numeroBoletos,
            proceso
        FROM Rutas
        WHERE proceso = YEAR(CURDATE())
    ";

    $stmtInfoLogisticaTable = $conn->prepare($queryInfoLogisticaTable);
    $stmtInfoLogisticaTable->execute();
    $infoLogisticaTable = $stmtInfoLogisticaTable->fetchAll(PDO::FETCH_ASSOC);

    // Aplicar truncado también
    $infoLogisticaTable = array_map(function($row) {
        $row['nombres'] = truncarNombres($row['nombres']);
        return $row;
    }, $infoLogisticaTable);

    // Consulta de resumen de entregas
    $queryResumen = "
        SELECT 
            status,
            SUM(numeroBoletos) AS total_boletos
        FROM Rutas
        WHERE proceso = YEAR(CURDATE())
        GROUP BY status
    ";
    $stmtResumen = $conn->prepare($queryResumen);
    $stmtResumen->execute();
    $resumen = $stmtResumen->fetchAll(PDO::FETCH_ASSOC);

    $noEntregados = 0;
    $entregados = 0;

    foreach ($resumen as $row) {
        if ($row['status'] == 0) {
            $noEntregados = $row['total_boletos'];
        } elseif ($row['status'] == 1) {
            $entregados = $row['total_boletos'];
        }
    }
    $total = $noEntregados + $entregados;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!----======== CSS ======== -->
    <link rel="stylesheet" href="/pruebas/menuUsuario/styleMenu.css">
    <link rel="stylesheet" href="stylesLog.css">
    
    <!----===== Boxicons CSS ===== -->
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    
    <!----===== Actualizador de procesos ===== -->
    <script src="/pruebas/menuCajero/actualizadorProceso.js"></script>
    
    <!-- Tabla de rutas -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    
    <!-- Agrega un elemento canvas para el gráfico -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

 
    <title>Mayordomía Tickets®/Log&iacute;stica</title> 
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
                            <i class='bx bx-envelope icon'></i>
                            <span class="text nav-text">Cartas<br>Decimas</span>
                        </a>
                    </li>
                    <li class="nav-link">
                            <a href="/pruebas/menuAdministrador/logistica/logistica.php">
                            <i class='bx bxs-truck icon'></i>
                            <span class="text nav-text">log&iacute;stica</span>
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
        <div class="section">

        <div id="resumenRutas">
            <h2>Resumen de Entregas</h2>
            <table class="resumen-table">
                <tr>
                    <th>Estado</th>
                    <th>Total Décimas</th>
                </tr>
                <tr>
                    <td>No entregadas</td>
                    <td id="tdNoEntregadas"><?= $noEntregados ?></td>
                </tr>
                <tr>
                    <td>Entregadas</td>
                    <td id="tdEntregadas"><?= $entregados ?></td>
                </tr>
                <tr>
                    <th>Total</th>
                    <th id="tdTotal"><?= $total ?></th>
                </tr>
            </table>
        </div>

            <section class="logistica">
                <h1>Logística de entrega de cartas y décimas</h1>
                <br>
            </section>
                <div class="container">
                    
                    <table id="rutasTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="display:none;">idRutas</th>
                                <th style="display:none;">RUTAS<i class='bx bx-cycling icon'></i></th>
                                <th style="display:none;">RECORRIDO <i class='bx bx-trip icon'></th>
                                <th>NOMBRES</th>
                                <th>DOMICILIO</th>
                                <th>N° DE DÉCIMAS A ENTREGAR</th>
                                <th>PROCESO</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rutasTable as $ruta): ?>
                                <tr>
                                    <td class="idRutas" style="display:none;"><?= htmlspecialchars($ruta['idRutas']) ?></td>
                                    <td style="display:none;"><input type="text" name="ruta" value="<?= htmlspecialchars($ruta['ruta']) ?>" style="width: 100px;" disabled></td>
                                    <td style="display:none;"><input type="text" name="recorrido" value="<?= htmlspecialchars($ruta['recorrido']) ?>" style="width: 100px;" disabled></td>
                                    <td><?= htmlspecialchars($ruta['nombres']) ?></td>
                                    <td><?= htmlspecialchars($ruta['domicilio']) ?></td>
                                    <td><?= htmlspecialchars($ruta['numeroBoletos']) ?></td>
                                    <td><?= htmlspecialchars($ruta['proceso']) ?></td>
                                    <td><button class="btnCheck"><i class='bx bx-check'></i></button></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>  
                    </table>
                </div>
                <br><br>

                <div class="container">
                <h2 class="infoLogistica">Información de entrega de cartas y décimas</h2>
                <table id="infoLogisticaTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th style="display:none;">idRutas</th>
                            <th style="display:none;">RUTAS</th>
                            <th style="display:none;">RECORRIDO</th>
                            <th style="display:none;">NOMBRES</th>
                            <th>DOMICILIO</th>
                            <th>N° BOLETOS DECIMAS ENTREGADAS</th>
                            <th>ENTREGO</th>
                            <th>PROCESO</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($infoLogisticaTable as $ruta): ?>
                            <tr>
                                <td class="idRutas" style="display:none;"><?= htmlspecialchars($ruta['idRutas']) ?></td>
                                <td style="display:none;"><?= htmlspecialchars($ruta['ruta']) ?></td>
                                <td style="display:none;"><?= htmlspecialchars($ruta['recorrido']) ?></td>
                                <td style="display:none;"><?= htmlspecialchars($ruta['nombres']) ?></td>
                                <td><?= htmlspecialchars($ruta['domicilio']) ?></td>
                                <td><?= htmlspecialchars($ruta['numeroBoletos']) ?></td>
                                <td>Persona</td>
                                <td><?= htmlspecialchars($ruta['proceso']) ?></td>
                                <td><button class="btnCancelar"><i class='bx bx-x'></i></button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>  
                </table>
            </div>

        </div>
    </section>


    <script src="/pruebas/menuUsuario/script.js"></script>
    <script src="/pruebas/menuAdministrador/autologout.js"></script>
    <script>
    $(document).ready(function () {
        // ✅ Inicializar la tabla SOLO una vez
        var rutasTable;
        if (!$.fn.DataTable.isDataTable('#rutasTable')) {
            rutasTable = $('#rutasTable').DataTable({
                "searching": true,
                "searchMinLength": 1
            });
        } else {
            rutasTable = $('#rutasTable').DataTable();
        }

        // -------------------------
        // FILTROS Y BÚSQUEDAS
        // -------------------------
        $('#searchVendidos').on('keyup', function() {
            rutasTable.search(this.value).draw();
        });

        $('#rutaSelect').on('change', function() {
            var ruta = this.value;
            rutasTable.column(1).search(ruta ? ruta : '').draw();
        });

        $('#procesoSelect').on('change', function() {
            var proceso = this.value;
            rutasTable.column(6).search(proceso ? proceso : '').draw();
        });

        // -------------------------
        // MODAL (abrir/cerrar)
        // -------------------------
        $('#rutasTable').on('click', '.btnCheck', function () {
            var fila = $(this).closest('tr');

            var nombres = fila.find('td:eq(3)').text();
            var domicilio = fila.find('td:eq(4)').text();
            var boletos = fila.find('td:eq(5)').text();

            $('#modalNombres').text(nombres);
            $('#modalDomicilio').text(domicilio);
            $('#modalBoletos').text(boletos);

            $('#modalInfo').fadeIn();
        });

        // Botón Cancelar
        $('#btnCancelar').on('click', function () {
            $('#modalMessage')
                .text("Operación cancelada")
                .css("color", "red")
                .show();
                $('#modalInfo').fadeOut();

        });

        // Cerrar modal
        $('.close').on('click', function () {
            $('#modalInfo').fadeOut();
        });

        // Cerrar al hacer click fuera del modal
        $(window).on('click', function (event) {
            if ($(event.target).is('#modalInfo')) {
                $('#modalInfo').fadeOut();
            }
        });



        // Botón Entregar (ejemplo, lo puedes personalizar para enviar AJAX)
        $('#btnEntregar').on('click', function () {
            var usuario = $('#usuarioSelect').val();
            if (!usuario) {
                $('#modalMessage')
                    .text("Debe seleccionar un usuario antes de entregar.")
                    .css("color", "orange")
                    .show();
                return;
            }

            $('#modalMessage')
                .text("Operación realizada por el usuario seleccionado.")
                .css("color", "green")
                .show();

            // Aquí podrías hacer un $.ajax() para registrar en la BD la entrega
        });

    });


    $(document).ready(function () {
    // Tabla 1: rutasTable
    var rutasTable;
    if (!$.fn.DataTable.isDataTable('#rutasTable')) {
        rutasTable = $('#rutasTable').DataTable({
            "searching": true,
            "searchMinLength": 1
        });
    } else {
        rutasTable = $('#rutasTable').DataTable();
    }

    // Tabla 2: infoLogisticaTable
    var infoLogisticaTable;
    if (!$.fn.DataTable.isDataTable('#infoLogisticaTable')) {
        infoLogisticaTable = $('#infoLogisticaTable').DataTable({
            "searching": true,
            "searchMinLength": 1
        });
    } else {
        infoLogisticaTable = $('#infoLogisticaTable').DataTable();
    }

    // -------------------------
    // EVENTOS MODAL para rutasTable
    // -------------------------
    $('#rutasTable').on('click', '.btnCheck', function () {
        var fila = $(this).closest('tr');
        var nombres = fila.find('td:eq(3)').text();
        var domicilio = fila.find('td:eq(4)').text();
        var boletos = fila.find('td:eq(5)').text();

        $('#modalNombres').text(nombres);
        $('#modalDomicilio').text(domicilio);
        $('#modalBoletos').text(boletos);

        $('#modalInfo').fadeIn();
    });

    // -------------------------
    // EVENTOS MODAL para infoLogisticaTable
    // -------------------------
    $('#infoLogisticaTable').on('click', '.btnCheckInfo', function () {
        var fila = $(this).closest('tr');
        var nombres = fila.find('td:eq(3)').text();
        var domicilio = fila.find('td:eq(4)').text();
        var boletos = fila.find('td:eq(5)').text();

        $('#modalNombres').text(nombres);
        $('#modalDomicilio').text(domicilio);
        $('#modalBoletos').text(boletos);

        $('#modalInfo').fadeIn();
    });

    // -------------------------
    // BOTONES MODAL
    // -------------------------
    $('#btnCancelar').on('click', function () {
        $('#modalMessage')
            .text("Operación cancelada")
            .css("color", "red")
            .show();
        $('#modalInfo').fadeOut();
    });

    $('#btnEntregar').on('click', function () {
        var usuario = $('#usuarioSelect').val();
        if (!usuario) {
            $('#modalMessage')
                .text("Debe seleccionar un usuario antes de entregar.")
                .css("color", "orange")
                .show();
            return;
        }
        $('#modalMessage')
            .text("Operación realizada por el usuario seleccionado.")
            .css("color", "green")
            .show();
    });

    // Cerrar modal
    $('.close').on('click', function () {
        $('#modalInfo').fadeOut();
    });
    $(window).on('click', function (event) {
        if ($(event.target).is('#modalInfo')) {
            $('#modalInfo').fadeOut();
        }
    });
});

// ✅ Función para refrescar el resumen con AJAX
function cargarResumen() {
    $.ajax({
        url: 'logistica.php?resumen=1', // recarga solo el resumen
        type: 'GET',
        success: function (data) {
            var parser = new DOMParser();
            var doc = parser.parseFromString(data, 'text/html');

            // Extraer los valores de la nueva respuesta
            var noEntregadas = doc.querySelector('#tdNoEntregadas').innerText;
            var entregadas = doc.querySelector('#tdEntregadas').innerText;
            var total = doc.querySelector('#tdTotal').innerText;

            // Actualizar en la tabla actual
            $('#tdNoEntregadas').text(noEntregadas);
            $('#tdEntregadas').text(entregadas);
            $('#tdTotal').text(total);
        },
        error: function () {
            alert('Error al actualizar el resumen');
        }
    });
}

// ✅ Actualizar resumen cuando se entregue o cancele
$('#btnEntregar, #btnCancelar').on('click', function () {
    setTimeout(cargarResumen, 500);
});
    </script>
</body>

<!-- Modal -->
<div id="modalInfo" class="modal" style="display:none;">
  <div class="modal-content">
    <span class="close">&times;</span>

    <h3>Entregar Decíma(s) y Carta</h3>
    <p><b>Nombre:</b> <span id="modalNombres"></span></p>
    <p><b>Domicilio:</b> <span id="modalDomicilio"></span></p>
    <p><b>Decímas a entregar:  </b> <span id="modalBoletos"></span></p>
    <p><b>Cartas a entregar:   1</b></span></p>


    <!-- Select con usuarios -->
    <label for="usuarioSelect"><b>Usuario responsable:</b></label>
    <select id="usuarioSelect" name="usuarioSelect">
      <option value="">Seleccione un usuario</option>
      <?php foreach ($usuarios as $usuario): ?>
        <option value="<?= htmlspecialchars($usuario['idUsuario']) ?>">
          <?= htmlspecialchars($usuario['nombre']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <br><br>
    <!-- Botones -->
    <button id="btnEntregar">Entregar</button>
    <button id="btnCancelar">Cancelar</button>

    <!-- Mensaje de resultado -->
    <p id="modalMessage" style="color:red; font-weight:bold; display:none;"></p>
  </div>
</div>


<style>
/* Estilos del modal */
.modal {
  display: none; 
  position: fixed; 
  z-index: 9999; 
  padding-top: 100px; 
  left: 0; top: 0; width: 100%; height: 100%;
  overflow: auto; background-color: rgba(0,0,0,0.6);
}
.modal-content {
  background-color: #fff; margin: auto; padding: 20px; border: 1px solid #888;
  width: 400px; border-radius: 12px; text-align: left;
}
.close {
  color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;
}
.close:hover { color: black; }
</style>

    <style>
    .resumen-table {
        width: 50%;
        margin: 20px auto;
        border-collapse: collapse;
        text-align: center;
        font-family: Arial, sans-serif;
        box-shadow: 0px 3px 8px rgba(0,0,0,0.2);
    }
    .resumen-table th, .resumen-table td {
        padding: 10px;
        border: 1px solid #ccc;
    }
    .resumen-table th {
        background-color: #2c3e50;
        color: #fff;
    }
    .resumen-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    </style>

</html>