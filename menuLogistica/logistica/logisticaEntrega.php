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

    // Consulta para obtener usuarios tipo 3
    $queryUsuarios = "SELECT idUsuario, nombre FROM Usuarios WHERE idTipoUsuario = 3";
    $stmtUsuarios = $conn->prepare($queryUsuarios);
    $stmtUsuarios->execute();
    $usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);

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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!----======== CSS ======== -->
    <link rel="stylesheet" href="/pruebas/menuUsuario/styleMenu.css">
    <link rel="stylesheet" href="stylesLogEnt.css">
    
    <!----===== Boxicons CSS ===== -->
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    
    <!----===== Actualizador de procesos ===== -->
    <script src="/pruebas/menuCajero/actualizadorProceso.js"></script>
    
    <!-- Tabla de rutas -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
 
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
                	<span class="name">LOGÍSTICA</span>
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
                        <a href="/pruebas/menuLogistica/indexLogistica.php">
                            <i class='bx bx-home-alt icon' ></i>
                            <span class="text nav-text">Inicio</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="/pruebas/menuLogistica/logistica/logisticaEntrega.php">
                            <i class='bx bx-select-multiple icon'></i>
                            <span class="text nav-text">Entregar Decima</span>
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
        <section class="logistica">
            <h1>Entregar décimas y cartas</h1>
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

            // ✅ Fecha local correcta
            var hoy = new Date();
            var fechaEntrega = hoy.getFullYear() + '-' +
                            String(hoy.getMonth() + 1).padStart(2, '0') + '-' +
                            String(hoy.getDate()).padStart(2, '0');

            $.ajax({
                url: 'logisticaEntrega.php',
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

                        // 🔄 Recargar AJAX
                        location.reload();
                    } else {
                        alert('Error al registrar entrega: ' + res.message);
                    }
                },
                error: function () {
                    alert('Error al registrar la entrega.');
                }
            });
        });

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

</body>
</html>