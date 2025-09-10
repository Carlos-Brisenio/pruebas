<?php

    session_start();
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

    // Consulta para obtener rutas SOLO del proceso = año actual y con status = 0
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
        WHERE proceso = YEAR(CURDATE()) AND status = 0
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
            proceso,
            entrego,
            fechaEntrega
        FROM Rutas
        WHERE proceso = YEAR(CURDATE()) AND status = 1
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

    $anioActual = date('Y');

// Total de décimas en el proceso actual
$queryTotalDecimas = "
    SELECT SUM(numeroBoletos) as total
    FROM Rutas
    WHERE proceso = :anio
";
$stmtTotalDecimas = $conn->prepare($queryTotalDecimas);
$stmtTotalDecimas->bindParam(':anio', $anioActual, PDO::PARAM_INT);
$stmtTotalDecimas->execute();
$totalDecimas = $stmtTotalDecimas->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// Décimas entregadas por fecha
$queryDecimasEntregadas = "
    SELECT DATE(fechaEntrega) as fecha, SUM(numeroBoletos) as total_entregadas
    FROM Rutas
    WHERE status = 1 AND proceso = :anio
    GROUP BY DATE(fechaEntrega)
    ORDER BY fechaEntrega ASC
";
$stmtDecimasEntregadas = $conn->prepare($queryDecimasEntregadas);
$stmtDecimasEntregadas->bindParam(':anio', $anioActual, PDO::PARAM_INT);
$stmtDecimasEntregadas->execute();
$decimasEntregadasRaw = $stmtDecimasEntregadas->fetchAll(PDO::FETCH_ASSOC);

// Preparar datos acumulativos y por día
$labels = [];
$entregadas = [];
$noEntregadas = [];
$entregadasDia = [];

$acumuladas = 0;
foreach ($decimasEntregadasRaw as $row) {
    $labels[] = $row['fecha'];
    $entregadasDia[] = $row['total_entregadas']; // entregas de ese día
    $acumuladas += $row['total_entregadas'];
    $entregadas[] = $acumuladas; // acumuladas
    $noEntregadas[] = $totalDecimas - $acumuladas; // por entregar
}

// Si no hay entregas aún
if (empty($labels)) {
    $labels[] = 'Aún no hay entregas';
    $entregadas[] = 0;
    $noEntregadas[] = $totalDecimas;
    $entregadasDia[] = 0;
}

if (isset($_POST['entregarRuta'])) {
    $idRutas = $_POST['idRutas'];
    $idUsuario = $_POST['idUsuario'];
    $fechaEntrega = $_POST['fechaEntrega'];

    // 1️⃣ Obtener el nombre del usuario
    $stmtUsuario = $conn->prepare("SELECT nombre FROM Usuarios WHERE idUsuario = :idUsuario");
    $stmtUsuario->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
    $stmtUsuario->execute();
    $nombreUsuario = $stmtUsuario->fetchColumn();

    // 2️⃣ Actualizar registro
    $sql = "UPDATE Rutas SET status = 1, entrego = :nombreUsuario, fechaEntrega = :fechaEntrega WHERE idRutas = :idRutas";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':idRutas', $idRutas, PDO::PARAM_INT);
    $stmt->bindParam(':nombreUsuario', $nombreUsuario, PDO::PARAM_STR);
    $stmt->bindParam(':fechaEntrega', $fechaEntrega);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit;
}

if (isset($_POST['cancelarEntrega'])) {
    $idRutas = $_POST['idRutas'];

    // Consulta para revertir la entrega
    $sql = "UPDATE Rutas 
            SET status = 0, 
                entrego = '', 
                fechaEntrega = NULL
            WHERE idRutas = :idRutas";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':idRutas', $idRutas, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se pudo cancelar la entrega']);
    }
    exit;
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
    <link rel="stylesheet" href="stylesLog.css">
    
    <!----===== Boxicons CSS ===== -->
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    
    <!----===== Actualizador de procesos ===== -->
    <script src="/pruebas/menuUsuario/actualizadorProceso.js"></script>
    
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
    <!-- Tarjeta usuario activo -->
    <div class="usuario-activo">
        <i class='bx bx-user-circle'></i>
        <?= isset($_SESSION['usuario']) ? htmlspecialchars($_SESSION['usuario']) : 'Invitado' ?>
    </div>
    <nav class="sidebar close">
        <header>
            <div class="image-text">
                <span class="image">
                    <img src="/pruebas/menuUsuario/logoTM.png" alt="">
                </span>

                <div class="text logo-text">
                	<span class="name">ADMINISTRADOR</span>
                 	<span class="profession" id="proceso-span">PROCESO</span>
                    <span class="usuario">
                        A: 
                        <?php
                        if (isset($_SESSION['usuario'])) {
                            $usuario = htmlspecialchars($_SESSION['usuario']);
                            $partes = explode(' ', $usuario);

                            if (count($partes) > 2) {
                                // Une las dos primeras palabras en la primera línea
                                echo $partes[0] . ' ' . $partes[1] . '<br>' . implode(' ', array_slice($partes, 2));
                            } else {
                                // Si solo hay 1 o 2 palabras, imprime normal
                                echo $usuario;
                            }
                        } else {
                            echo 'Invitado';
                        }
                        ?>
                    </span>
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
                <h1>Resumen estadisticas de Logística</h1>
                <table class="resumen-table">
                    <tr>
                        <th>Estado</th>
                        <th>Total Décimas</th>
                    </tr>
                    <tr>
                        <td>No entregadas (NO)</td>
                        <td id="tdNoEntregadas"><?= $noEntregados ?></td>
                    </tr>
                    <tr>
                        <td>Entregadas (EN)</td>
                        <td id="tdEntregadas"><?= $entregados ?></td>
                    </tr>
                    <tr>
                        <th>Total = (NO + EN)</th>
                        <th id="tdTotal"><?= $total ?></th>
                    </tr>
                </table>
            </div>

        <section class="logistica">
            <h1>Logística de entrega de cartas y décimas</h1>
            <br>
        </section>
            <div class="container">
                <h2 class="infoLogistica">Décimas por entregar</h2>
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
                <h2 class="infoLogistica">Información de Décimas entregadas</h2>
                <table id="infoLogisticaTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th style="display:none;">idRutas</th>
                            <th style="display:none;">RUTAS</th>
                            <th style="display:none;">RECORRIDO</th>
                            <th>NOMBRES</th>
                            <th>DOMICILIO</th>
                            <th>N° DECIMAS ENTREGADAS</th>
                            <th>ENTREGO</th>
                            <th>FECHA</th>
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
                                <td><?= htmlspecialchars($ruta['nombres']) ?></td>
                                <td><?= htmlspecialchars($ruta['domicilio']) ?></td>
                                <td><?= htmlspecialchars($ruta['numeroBoletos']) ?></td>
                                <td><?= htmlspecialchars($ruta['entrego']) ?></td>
                                <td><?= htmlspecialchars($ruta['fechaEntrega']) ?></td>
                                <td><?= htmlspecialchars($ruta['proceso']) ?></td>
                                <td><button style="background-color:red" class="btnCancelar"><i class='bx bx-x'></i></button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>  
                </table>
            </div>

        </div>
            <!--Aqui debe de ir el llamado para mostrar la grafica-->
            <div class="container">
                <h2 style="text-align: center">Décimas Por Entregar vs Entregadas(Día) vs Entregadas(Acumulativo) <br>(Proceso <?= $anioActual ?>)</h2>
                <canvas id="decimasChart" width="800" height="400"></canvas>
            </div>
    </section>


    <!-- =========================
     MODAL PARA TABLA RUTAS
========================= -->
<div id="modalInfo" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close closeModalInfo">&times;</span>
        <h2>Entregar Décima(s) y Carta</h2>

        <p><strong>Nombre:</strong> <span id="modalNombres"></span></p>
        <p><strong>Domicilio:</strong> <span id="modalDomicilio"></span></p>
        <p><strong>Décimas a entregar:</strong> <span id="modalBoletos"></span></p>
        <p><strong>Cartas a entregar:</strong> 1</p>

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

        <!-- Botones -->
        <div class="modal-actions">
            <button id="btnEntregarRuta">Entregar</button>
            <button id="btnCancelarRuta">Cancelar</button>
        </div>

        <!-- Mensaje -->
        <p id="modalMessageRuta" style="color:red; font-weight:bold; display:none;"></p>
    </div>
</div>

<!-- ==============================
     MODAL PARA TABLA LOGÍSTICA
============================== -->
<div id="modalInfoLogistica" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close closeModalInfoLogistica">&times;</span>
        <h2>Cancelar entrega</h2>

        <p><strong>Domicilio:</strong> <span id="modalLogisticaDomicilio"></span></p>
        <p><strong>Décimas entregadas:</strong> <span id="modalLogisticaBoletos"></span></p>
        <p><strong>Cartas entregadas:</strong> 1</p>

        <!-- Botones -->
        <div class="modal-actions">
            <button id="btnEliminarEntrega">Cancelar Entrega</button>
            <button id="btnCancelarLogistica">Cerrar</button>
        </div>

        <!-- Mensaje -->
        <p id="modalMessageLogistica" style="color:red; font-weight:bold; display:none;"></p>
    </div>
</div>


    <script src="/pruebas/menuUsuario/script.js"></script>
    <script src="/pruebas/menuAdministrador/autologout.js"></script>
<script>
$(document).ready(function () {
    // -------------------------
    // TABLA RUTAS
    // -------------------------
    var rutasTable = $('#rutasTable').DataTable({
        searching: true,
        searchMinLength: 1
    });

    // -------------------------
    // TABLA LOGÍSTICA
    // -------------------------
    var infoLogisticaTable = $('#infoLogisticaTable').DataTable({
        searching: true,
        searchMinLength: 1
    });

    // -------------------------
    // FILTROS
    // -------------------------
    $('#searchVendidos').on('keyup', function() {
        rutasTable.search(this.value).draw();
    });

    $('#rutaSelect').on('change', function() {
        rutasTable.column(1).search(this.value || '').draw();
    });

    $('#procesoSelect').on('change', function() {
        rutasTable.column(6).search(this.value || '').draw();
    });

    // -------------------------
    // MODAL RUTAS
    // -------------------------
    $('#rutasTable').on('click', '.btnCheck', function () {
        var fila = $(this).closest('tr');
        fila.addClass('selected'); // marcar fila
        $('#modalNombres').text(fila.find('td:eq(3)').text());
        $('#modalDomicilio').text(fila.find('td:eq(4)').text());
        $('#modalBoletos').text(fila.find('td:eq(5)').text());
        $('#modalInfo').fadeIn();
    });

    $('.closeModalInfo, #btnCancelarRuta').on('click', function () {
        $('#modalInfo').fadeOut();
    });

    // -------------------------
    // MODAL LOGÍSTICA
    // -------------------------
    $('#infoLogisticaTable').on('click', '.btnCancelar', function () {
        $('#infoLogisticaTable tr').removeClass('selected');
        var fila = $(this).closest('tr');
        fila.addClass('selected');

        $('#modalLogisticaDomicilio').text(fila.find('td:eq(4)').text());
        $('#modalLogisticaBoletos').text(fila.find('td:eq(5)').text());
        $('#modalInfoLogistica').fadeIn();
    });

    $('.closeModalInfoLogistica, #btnCancelarLogistica').on('click', function () {
        $('#modalInfoLogistica').fadeOut();
    });

    // -------------------------
    // RESUMEN AJAX
    // -------------------------
    /*function cargarResumen() {
        $.ajax({
            url: 'logistica.php?resumen=1',
            type: 'GET',
            success: function (data) {
                var doc = new DOMParser().parseFromString(data, 'text/html');
                $('#tdNoEntregadas').text(doc.querySelector('#tdNoEntregadas').innerText);
                $('#tdEntregadas').text(doc.querySelector('#tdEntregadas').innerText);
                $('#tdTotal').text(doc.querySelector('#tdTotal').innerText);
            },
            error: function () {
                alert('Error al actualizar el resumen');
            }
        });
    }*/

    // ===================== ENTREGAR DÉCIMA =====================
        $('#btnEntregarRuta').on('click', function () {
            var fila = $('#rutasTable').find('tr.selected');
            if (!fila.length) {
                fila = $('#rutasTable').find('tr').filter(function() {
                    return $(this).find('td:eq(3)').text() === $('#modalNombres').text() &&
                        $(this).find('td:eq(4)').text() === $('#modalDomicilio').text();
                });
            }

            if (!fila.length) return alert('No se pudo identificar la fila seleccionada.');

            var idRutas   = fila.find('td:eq(0)').text();
            var idUsuario = $('#usuarioSelect').val(); // ahora mandamos el idUsuario
            if (!idUsuario) return alert('Por favor seleccione un usuario responsable.');

            if (!confirm('¿Está seguro de marcar esta entrega como completada?')) {
                return;
            }

            var fechaEntrega = new Date().toISOString().slice(0, 10); // YYYY-MM-DD

            $.ajax({
                url: 'logistica.php',
                type: 'POST',
                data: {
                    entregarRuta: 1,
                    idRutas: idRutas,
                    idUsuario: idUsuario,
                    fechaEntrega: fechaEntrega
                },
                success: function (response) {
                    var res = JSON.parse(response);
                    if(res.status === 'success'){
                        $('#modalInfo').fadeOut();
                        $('#modalMessageRuta').text('Entrega registrada correctamente.')
                            .css('color','green').show().fadeOut(3000);

                        // 🔄 Recargar ambas tablas por AJAX
                        location.reload();

                        // 🔄 Actualizar resumen
                        //cargarResumen();
                    } else {
                        alert('Error al registrar entrega: ' + res.message);
                    }
                },
                error: function () {
                    alert('Error al registrar la entrega.');
                }
            });
        });

    // -------------------------
    // CANCELAR ENTREGA
    // -------------------------
    $('#btnEliminarEntrega').on('click', function () {
        var fila = $('#infoLogisticaTable tr.selected');
        if(fila.length === 0) return alert("Selecciona una fila primero");

        var idRutas = fila.find('td:eq(0)').text();
        if(!confirm("¿Cancelar entrega?")) return;

        $.ajax({
            url: 'logistica.php',
            type: 'POST',
            data: { cancelarEntrega: 1, idRutas: idRutas },
            success: function(response) {
                var res = JSON.parse(response);
                if(res.status === 'success') {
                    $('#modalInfoLogistica').fadeOut();
                    $('#modalMessageLogistica')
                        .text("Entrega cancelada correctamente")
                        .css("color", "green")
                        .show().fadeOut(3000);

                    // 🔄 Recargar ambas tablas por AJAX
                    location.reload();


                    // 🔄 Actualizar resumen
                    //cargarResumen();
                } else {
                    alert('Error al cancelar entrega: ' + res.message);
                }
            },
            error: function() {
                alert("Error en la conexión con el servidor");
            }
        });
    });
});
</script>

<script>
// Datos desde PHP
var labels = <?php echo json_encode($labels); ?>;
var entregadas = <?php echo json_encode($entregadas); ?>;
var noEntregadas = <?php echo json_encode($noEntregadas); ?>;
var entregadasDia = <?php echo json_encode($entregadasDia); ?>;

var ctxDecimas = document.getElementById('decimasChart').getContext('2d');
var decimasChart = new Chart(ctxDecimas, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Décimas Por Entregar',
                data: noEntregadas,
                backgroundColor: 'rgba(255, 99, 132, 0.6)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            },
            {
                label: 'Décimas Entregadas del Día',
                data: entregadasDia,
                backgroundColor: 'rgba(255, 206, 86, 0.6)',
                borderColor: 'rgba(255, 206, 86, 1)',
                borderWidth: 1
            },
            {
                label: 'Décimas Entregadas (Acumuladas)',
                data: entregadas,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                title: { display: true, text: 'Número de Décimas' }
            },
            x: {
                title: { display: true, text: 'Fecha de Entrega' }
            }
        }
    }
});
</script>

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
    .close:hover { 
        color: black; 
    }
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

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 9999;
    padding-top: 100px;
    left: 0; top: 0;
    width: 100%; height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background: #fff;
    margin: auto;
    padding: 20px;
    border-radius: 10px;
    width: 400px;
    text-align: center;
}

.close {
    float: right;
    font-size: 22px;
    cursor: pointer;
}

.modal-actions {
    margin-top: 20px;
    display: flex;
    justify-content: space-around;
}

.modal-actions button {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

#btnEntregarRuta, #btnEntregarLogistica {
    background-color: #4CAF50;
    color: white;
}

#btnCancelarRuta, #btnCancelarLogistica {
    background-color: #f44336;
    color: white;
}
</style>
<style>
        .usuario-activo {
        position: fixed;
        top: 15px;
        right: 50px;
        background: #2c3e50;
        color: #fff;
        padding: 10px 18px;
        border-radius: 12px;
        font-size: 14px;
        font-family: Arial, sans-serif;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0px 4px 10px rgba(0,0,0,0.25);
        z-index: 10000;
    }

    .usuario-activo i {
        font-size: 18px;
    }
    </style>

</body>
</html>