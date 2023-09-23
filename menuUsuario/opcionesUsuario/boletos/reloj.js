let timeLeft = 120; // 4 minutos = 240 segundos

function startTimer() {
    let timerElement = document.getElementById('timer');
    let interval = setInterval(function() {
        let minutes = Math.floor(timeLeft / 60);
        let seconds = timeLeft % 60;

        if (seconds < 10) {
            seconds = '0' + seconds;
        }

        timerElement.textContent = minutes + ':' + seconds +'\nTiempo Restante';

        timeLeft--;

        if (timeLeft < 0) {
            clearInterval(interval);
            alert('El tiempo ha finalizado\nlos boletos no han sido seleccionados');
            window.location.href = '/pruebas/index.html'; // Redirige al inicio
        }
    }, 1000);
}

startTimer();
