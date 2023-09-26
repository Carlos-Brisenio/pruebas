var currentBoard = 1;
var totalBoards = 15;  // Cambiar esto para agregar más tableros

function showAlert(buttonNumber) {
    // Cambiar el color del botón seleccionado a amarillo
    var selectedButton = document.getElementById('button-' + buttonNumber);
    selectedButton.style.backgroundColor = 'yellow';

    // Redirigir a boletosRegistro.php con el número de boleto
    setTimeout(function () {
        window.location.href = '/pruebas/menuUsuario/opcionesUsuario/boletos/boletosRegistro.php?numero-boleto=' + buttonNumber;
    }, 1000);
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
            } else if (boletosStatus4.includes(buttonNumber.toString())) {
                        boardDiv.innerHTML += '<button id="button-' + buttonNumber + '" class="button" style="background-color: #2e055d;" onclick="showAlert(' + buttonNumber + ')" disabled>' + buttonNumber + '</button>';
            }else{
                boardDiv.innerHTML += '<button id="button-' + buttonNumber + '" class="button" onclick="showAlert(' + buttonNumber + ')">' + buttonNumber + '</button>';
            }
        }
        boardDiv.innerHTML += '<br/>';
    }
}

// Llamar a updateBoard para generar el tablero inicial cuando se carga la página
window.onload = updateBoard;