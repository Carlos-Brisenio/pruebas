// script de actualizacion de año automatico
    function actualizarProceso() {
        const spanProceso = document.getElementById('proceso-span');
        const añoActual = new Date().getFullYear();
        spanProceso.textContent = `PROCESO ${añoActual + 1}`;
    }

    // Llama a la función cuando la página se carga
    document.addEventListener('DOMContentLoaded', actualizarProceso);