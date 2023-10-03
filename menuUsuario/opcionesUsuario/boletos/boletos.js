var currentBoard = 1;
var totalBoards = 15;  // Cambiar esto para agregar más tableros

function showAlert(buttonNumber) {
    // Realizar solicitud AJAX para cambiar el estado en la base de datos
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/pruebas/menuCajero/opcionesCajero/boletos/cambiarEstado.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.responseText === "success") {
                // Cambiar el color del botón seleccionado a amarillo
                var selectedButton = document.getElementById('button-' + buttonNumber);
                selectedButton.style.backgroundColor = 'yellow';

                // Redirigir a boletosRegistro.php con el número de boleto
                setTimeout(function() {
                    var encodedBoleto = btoa(buttonNumber);
                    window.location.href = '/pruebas/menuUsuario/opcionesUsuario/boletos/boletosRegistro.php?token=' + encodedBoleto;
                }, 1000);
            } else {
                alert("Ocurrió un error al cambiar el estado del boleto. Inténtelo nuevamente.");
            }
        }
    };
        
    // Enviar el número de boleto como parámetro
    var params = "numero_boleto=" + buttonNumber;
    xhr.send(params);
}

function previous() {
    if (currentBoard > 1) {
        currentBoard--;
        updateBoard();
    }
}

function next() {
    if (currentBoard < totalBoards) {
        currentBoard++;
        updateBoard();
    }
}

function updateBoard() {
    var boardDiv = document.getElementById("board");
    boardDiv.innerHTML = "";
    
    for (var i = 1; i <= 10; i++) {
        for (var j = 1; j <= 10; j++) {
            var buttonNumber = (currentBoard - 1) * 100 + (i - 1) * 10 + j;

            if (boletosStatus2.includes(buttonNumber.toString())) {
                boardDiv.innerHTML += '<button id="button-' + buttonNumber + '" class="button" style="background-color: yellow;" onclick="showAlert(' + buttonNumber + ')" disabled>' + buttonNumber + '</button>';
            } else if (boletosStatus3.includes(buttonNumber.toString())) {
                        boardDiv.innerHTML += '<button id="button-' + buttonNumber + '" class="button" style="background-color: red;" onclick="showAlert(' + buttonNumber + ')" disabled>' + buttonNumber + '</button>';
            } else if (boletosStatus4.includes(buttonNumber.toString())) {
                        boardDiv.innerHTML += '<button id="button-' + buttonNumber + '" class="button" style="background-color: #2e055d;" onclick="showAlert(' + buttonNumber + ')" disabled>' + buttonNumber + '</button>';
            } else{
                boardDiv.innerHTML += '<button id="button-' + buttonNumber + '" class="button" onclick="showAlert(' + buttonNumber + ')">' + buttonNumber + '</button>';
            }
        }
        boardDiv.innerHTML += '<br/>';
    }
}

// Llamar a updateBoard para generar el tablero inicial cuando se carga la página
window.onload = updateBoard;

function fetchAndUpdateBoard() {
    fetch('/pruebas/menuCajero/opcionesCajero/boletos/getBoletoStatus.php')
    .then(response => response.json())
    .then(data => {
        // Reinicia las listas de status
        boletosStatus2 = [];
        boletosStatus3 = [];
        boletosStatus4 = [];

        for (let numeroBoleto in data) {
            let status = data[numeroBoleto];

            switch (status) {
                case '2':
                    boletosStatus2.push(numeroBoleto);
                    break;
                case '3':
                    boletosStatus3.push(numeroBoleto);
                    break;
                case '4':
                    boletosStatus4.push(numeroBoleto);
                    break;
            }
        }

        // Actualiza el tablero con los nuevos datos
        updateBoard();
    })
    .catch(error => {
        console.error('Error fetching boleto status:', error);
    });
}

// Llamar a fetchAndUpdateBoard() cada 5 segundos
setInterval(fetchAndUpdateBoard, 5000);

// Llamar a fetchAndUpdateBoard para generar el tablero inicial cuando se carga la página
window.onload = fetchAndUpdateBoard;