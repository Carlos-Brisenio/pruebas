<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!----======== CSS ======== -->
    <link rel="stylesheet" href="/pruebas/menuUsuario/styleMenu.css">
    <link rel="stylesheet" href="estilosAjustes.css">
    
    <!----===== Boxicons CSS ===== -->
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>

    <title>Mayordomía Tickets®/Ajustes</title> 
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
        
        <div id="confirmationModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center;">
            <div style="background-color: white; padding: 20px; width: 300px; border-radius: 10px;">
                <h3>Confirmar Acción</h3>
                <label>Usuario:</label>
                <input type="text" id="userInput">
                <label>Contraseña:</label>
                <input type="password" id="passwordInput">
                <div id="procesoField" style="display: none;">
                    <label>Proceso:</label>
                    <input type="number" id="procesoInput">
                </div>
                <button onclick="confirmDelete()">Confirmar</button>
                <button onclick="closeModal()">Cancelar</button>
            </div>
        </div>

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
                        <a href="/pruebas/menuAdministrador/estadisticas/estadisticas.php">
                            <i class='bx bx-bar-chart-square icon'></i>
                            <span class="text nav-text">Estadisticas</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="moduloAjustes.php">
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
        <div class="text">Ajustes</div>
        
        <div class="settings">
            <p>Advertencia: Eliminará todos los registros de la tabla Boletos.</p>
            <button onclick="showConfirmationModal('boletos')">Eliminar registros de la tabla Boletos</button>
            <br>
            <br>
            <p>Advertencia: Eliminará todos los registros de la tabla InfoBoletos.</p>
            <button onclick="showConfirmationModal('infoboletos')">Eliminar registros de la tabla InfoBoletos</button>
            <br>
            <br>
            <p>Advertencia: Eliminará todos los registros de la tabla Ventas.</p>
            <button onclick="showConfirmationModal('ventas')">Eliminar registros de la tabla Ventas</button>
            <br>
            <br>
            <p>Advertencia: Eliminará los registros de la tabla Boletos que se encuenten en 2 y las fechas sean nulas.</p>
            <button onclick="showConfirmationModal('cambio')">Eliminar registros de Boletos 2 y nulos</button>
            <br>
            <br>
            <br>
            <br>
            <p>Advertencia: Traspasará todos los datos de tabla InfoBoletos que pasarán al Histórico.</p>
            <button onclick="showConfirmationModal('traspasar')">Traspasar a histórico todos los registros</button>
        </div>
    </section>

    <script src="/pruebas/menuUsuario/script.js"></script>
    <script>
    function showConfirmationModal(action) {
        document.getElementById("confirmationModal").setAttribute("data-action", action);
        const procesoField = document.getElementById("procesoField");
        if (action === 'traspasar') {
            procesoField.style.display = "block";
        } else {
            procesoField.style.display = "none";
        }
        document.getElementById("confirmationModal").style.display = "flex";
    }

    function closeModal() {
        document.getElementById("confirmationModal").style.display = "none";
    }

    function confirmDelete() {
        const user = document.getElementById("userInput").value;
        const password = document.getElementById("passwordInput").value;
        const action = document.getElementById("confirmationModal").getAttribute("data-action");
        const proceso = action === 'traspasar' ? document.getElementById("procesoInput").value : null;

        if (action === 'traspasar' && proceso) {
            // Validar si el proceso ya existe
            fetch('checkProceso.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `proceso=${proceso}`
            })
            .then(response => response.text())
            .then(data => {
                if (data.includes("El proceso ya se encuentra registrado")) {
                    alert(data);
                } else {
                    // Si el proceso es válido, continuar con la solicitud de traspaso
                    fetch('traspasarInfoBoletos.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `user=${user}&password=${password}&proceso=${proceso}`
                    })
                    .then(response => response.text())
                    .then(data => {
                        alert(data);
                        closeModal();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Hubo un error al intentar realizar la acción.');
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Hubo un error al validar el proceso.');
            });
        } else {
            let endpoint;
            let body = `user=${user}&password=${password}`;

            switch (action) {
                case 'boletos':
                    endpoint = 'eliminarRegistros.php';
                    break;
                case 'infoboletos':
                    endpoint = 'eliminarInfoBoletos.php';
                    break;
                case 'ventas':
                    endpoint = 'eliminarVentas.php';
                    break;
                case 'cambio':
                    endpoint = 'cambioStatus.php';
                    break;
            }

            fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: body
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                closeModal();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Hubo un error al intentar realizar la acción.');
            });
        }
    }
    </script>
    <script src="/pruebas/menuAdministrador/autologout.js"></script>
</body>
</html>