<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Punto de Venta - Mayordomía Tickets</title>
  <link rel="stylesheet" href="/pruebas/menuUsuario/styleMenu.css">
  <link rel="stylesheet" href="styleVenta.css">
  <link href="https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css" rel="stylesheet">
  <!-- jsPDF & jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

  <!-- Asegúrate de que la ruta de la imagen sea la correcta -->
    <img id="imagenParaPdf" src="/pruebas/bannerV2.png" style="display: none;">
        
</head>
<body>
  <nav class="sidebar close">
    <header>
      <div class="image-text">
        <span class="image">
          <img src="/pruebas/menuUsuario/logoTM.png" alt="">
        </span>
        <div class="text logo-text">
          <span class="name">CAJERO</span>
          <span class="profession">M-2025</span>
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
              <i class='bx bx-home-alt icon'></i>
              <span class="text nav-text">Inicio</span>
            </a>
          </li>
          <li class="nav-link">
            <a href="#">
              <i class='bx bx-cart-alt icon'></i>
              <span class="text nav-text">Punto de venta</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="bottom-content">
        <li>
          <a href="/pruebas/principal-Cajeros.php">
            <i class='bx bx-log-out icon'></i>
            <span class="text nav-text">Salir</span>
          </a>
        </li>
      </div>
    </div>
  </nav>

  <section class="home">
    <h2>PUNTO DE VENTA - MAYORDOMÍA TICKETS</h2>

    <div class="venta-container">
      <!-- RESUMEN DE VENTA -->
      <div class="resumen">
        <h3>RESUMEN DE VENTA</h3>
        <div id="detalleVenta" class="detalle-venta">
          <b>NÚMERO DE BOLETO:</b> — <span>$0.00</span><br>
          <b>NOMBRE DEL BOLETO:</b> —<br>
          <b>DOMICILIO:</b> —<br>
        </div>
      </div>

      <!-- PANEL DERECHO -->
      <div class="panel-derecho">
        <div>
          <h4>ÚLTIMOS BOLETOS REGISTRADOS</h4>
          <div class="fecha" id="fechaActual"></div>

          <label>BUSCAR BOLETO</label>
          <input type="number" id="buscarBoleto" placeholder="Número de boleto">

          <label>BOLETOS:</label>
          <input type="text" id="boletosCantidad" value="1" readonly>

          <label>TOTAL:</label>
          <div id="totalVenta" class="total">$0.00</div>
        </div>

        <div class="botones">
          <button class="boton-pagar" id="btnPagar">PAGAR</button>
          <button class="boton-cancelar" id="btnCancelar">CANCELAR</button>
        </div>
      </div>
    </div>
  </section>

  <!-- imagen usada para agregar al PDF (oculto) 
  <img id="imagenParaPdf" src="/pruebas/menuUsuario/logoTM.png" alt="logo" style="display:none;">-->

<script src="/pruebas/menuUsuario/script.js"></script>

<script>
/* ---------- CONFIG ---------- */
const PRECIO_BOLETO = 180;
let boletosVenta = []; // cada elemento: { idBoleto, numero_boleto, nombre, domicilio, calle, numero, colonia, ciudad, telefono1, telefono2, precio }
let lastPrintedDetails = null; // guardará el array de detalles usado para imprimir (para reimpresiones)

/* Fecha actual (para el panel derecho) */
const fecha = new Date();
const opcionesFecha = { day: '2-digit', month: 'long', year: 'numeric' };
document.getElementById('fechaActual').innerText = fecha.toLocaleDateString('es-MX', opcionesFecha).toUpperCase();

/* ---------- FUNCIONES BUSCAR Y RESUMEN ---------- */
document.getElementById('buscarBoleto').addEventListener('keyup', function(e) {
  if (e.key === 'Enter') {
    const numero = this.value.trim();
    if (!numero) return;

    fetch('buscarBoleto.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ numero_boleto: numero })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const b = data.data;

        if (boletosVenta.some(x => x.numero_boleto === b.numero_boleto)) {
          alert("El boleto ya está agregado al resumen.");
          this.value = "";
          return;
        }

        // Añadir campos adicionales (para imprimir)
        b.precio = PRECIO_BOLETO;
        boletosVenta.push(b);
        actualizarResumen();
        this.value = "";
      } else {
        alert(data.message || "Boleto no encontrado o no disponible (status ≠ 2).");
      }
    })
    .catch(err => {
      console.error("Error al buscar boleto:", err);
      alert("Error en la conexión con el servidor.");
    });
  }
});

function actualizarResumen() {
  const detalle = document.getElementById('detalleVenta');
  detalle.innerHTML = "";

  boletosVenta.forEach((b, i) => {
    const div = document.createElement('div');
    div.classList.add('boleto-item');
    div.innerHTML = `
      <b>NÚMERO DE BOLETO:</b> ${escapeHtml(b.numero_boleto)} <span>$${b.precio.toFixed(2)}</span><br>
      <b>NOMBRE:</b> ${escapeHtml(b.nombre ?? '—')}<br>
      <b>DOMICILIO:</b> ${escapeHtml(b.domicilio ?? '—')}<br>
      <button class="btnEliminar" data-index="${i}">❌ Eliminar</button>
      <hr>
    `;
    detalle.appendChild(div);
  });

  const total = boletosVenta.reduce((sum, b) => sum + b.precio, 0);
  document.getElementById('totalVenta').innerText = "$" + total.toFixed(2);
  document.getElementById('boletosCantidad').value = boletosVenta.length;

  document.querySelectorAll('.btnEliminar').forEach(btn => {
    btn.addEventListener('click', function() {
      const index = parseInt(this.getAttribute('data-index'), 10);
      boletosVenta.splice(index, 1);
      actualizarResumen();
    });
  });
}

/* Cancelar venta (limpiar) */
document.getElementById('btnCancelar').addEventListener('click', () => {
  boletosVenta = [];
  actualizarResumen();
});

/* ---------- PROCESAR PAGO ---------- */
document.getElementById('btnPagar').addEventListener('click', () => {
  if (boletosVenta.length === 0) {
    alert("No hay boletos en la venta.");
    return;
  }

  const total = boletosVenta.reduce((sum, b) => sum + b.precio, 0);

  // Crear modal
  const modal = document.createElement('div');
  modal.classList.add('modalPago');
  modal.innerHTML = `
    <div class="modalContenido">
      <h3>Confirmar Pago</h3>
      <div style="text-align:left; margin-bottom:8px;">
        <p><b>Boletos:</b> ${boletosVenta.length}</p>
        <p><b>Total a pagar:</b> $${total.toFixed(2)}</p>
      </div>

      <label>Forma de pago:</label>
      <select id="formaPago" style="width:100%; padding:8px; margin-bottom:8px;">
        <option value="1">Efectivo</option>
        <option value="2">Tarjeta</option>
        <option value="3">Transferencia</option>
      </select>

      <label>Cantidad recibida:</label>
      <input type="number" id="montoRecibido" min="0" step="0.01" placeholder="Monto recibido" style="width:100%; padding:8px; margin:8px 0;">

      <div class="modalBotones">
        <button id="confirmarPago">💰 Confirmar Pago</button>
        <button id="cancelarPago">❌ Cancelar</button>
      </div>
    </div>
  `;
  document.body.appendChild(modal);

  // cancelar
  document.getElementById('cancelarPago').addEventListener('click', () => modal.remove());

  // confirmar
  document.getElementById('confirmarPago').addEventListener('click', async () => {
    const forma_pago = parseInt(document.getElementById('formaPago').value, 10);
    const montoRecibido = parseFloat(document.getElementById('montoRecibido').value);

    if (isNaN(montoRecibido) || montoRecibido < total) {
      alert("La cantidad recibida es insuficiente para cubrir el total.");
      return;
    }

    // payload (enviamos array de idBoleto)
    const payload = {
      boletos: boletosVenta.map(b => ({ idBoleto: b.idBoleto })),
      forma_pago: forma_pago,
      recibe: montoRecibido,
      idUsuario: 1
    };

    try {
      const res = await fetch('procesarPago.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json; charset=utf-8' },
        body: JSON.stringify(payload)
      });
      const resp = await res.json();

      if (!resp.success) {
        alert(resp.message || "Error al registrar la venta.");
        return;
      }

      modal.remove();

      // obtener detalles para los boletos procesados (ordenados)
      const idsProcesados = resp.procesados; // array de idBoleto
      const detalles = await obtenerDetallesMultiples(idsProcesados);

      // guardar detalles para reimpresión si se necesita
      lastPrintedDetails = detalles;

      // generar único PDF con N hojas (cada hoja: ORIGINAL + COPIA)
      await imprimirBoletosFinal(detalles);

      // mostrar modal resumen final (con botón para reimprimir si hace falta)
      const modalCambio = document.createElement('div');
      modalCambio.classList.add('modalPago');
      modalCambio.innerHTML = `
        <div class="modalContenido">
          <h3>💵 Pago Completado</h3>
          <p><b>Boletos pagados:</b> ${resp.procesados.length}</p>
          <p><b>Total:</b> $${resp.recibo.total.toFixed(2)}</p>
          <p><b>Recibido:</b> $${resp.recibo.recibe.toFixed(2)}</p>
          <p><b>Cambio:</b> $${resp.recibo.cambio.toFixed(2)}</p>
          <div style="margin-top:12px;">
            <button id="imprimirNuevamente">🖨️ Imprimir boletos nuevamente</button>
            <button id="cerrarCambio">Cerrar</button>
          </div>
        </div>
      `;
      document.body.appendChild(modalCambio);

      // reimprimir si es necesario
      document.getElementById('imprimirNuevamente').addEventListener('click', async () => {
        if (!lastPrintedDetails || !Array.isArray(lastPrintedDetails) || lastPrintedDetails.length === 0) {
          alert("No hay detalles disponibles para reimprimir.");
          return;
        }
        try {
          await imprimirBoletosFinal(lastPrintedDetails);
        } catch (err) {
          console.error('Error reimprimiendo:', err);
          alert('Ocurrió un error al intentar reimprimir.');
        }
      });

      document.getElementById('cerrarCambio').addEventListener('click', () => modalCambio.remove());

      // limpiar venta
      boletosVenta = [];
      actualizarResumen();

    } catch (err) {
      console.error("Error en procesarPago o impresión:", err);
      alert("Pago registrado pero ocurrió un error al generar el PDF o al comunicarse con el servidor.");
      // Nota: el pago ya se registró si procesarPago respondió success
      boletosVenta = [];
      actualizarResumen();
    }
  });
});

/* ---------- FUNCIONES AUXILIARES: obtener detalles y generar PDF ---------- */

// Llama a obtenerDetallesMultiples.php y devuelve array ordenado de detalles
function obtenerDetallesMultiples(ids) {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: 'obtenerDetallesMultiples.php',
      type: 'POST',
      data: JSON.stringify({ boletos: ids }),
      contentType: 'application/json; charset=utf-8',
      success: function(response) {
        try {
          const data = (typeof response === 'string') ? JSON.parse(response) : response;
          if (data.success) {
            resolve(data.data);
          } else {
            reject(new Error(data.message || 'No se obtuvieron detalles.'));
          }
        } catch (err) {
          reject(err);
        }
      },
      error: function(xhr, status, err) {
        reject(err || status);
      }
    });
  });
}

// Generar UN solo PDF con jsPDF: N hojas (1 hoja por boleto), cada hoja contiene ORIGINAL + COPIA
async function imprimirBoletosFinal(detallesArray) {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();
  const imgElem = document.getElementById('imagenParaPdf');

  // Esperar imagen lista
  await new Promise(res => {
    if (imgElem.complete) return res();
    imgElem.onload = res;
    imgElem.onerror = res;
  });

  // Convertir imagen a dataURL
  const canvas = document.createElement('canvas');
  canvas.width = imgElem.naturalWidth || 200;
  canvas.height = imgElem.naturalHeight || 200;
  const ctx = canvas.getContext('2d');
  ctx.drawImage(imgElem, 0, 0, canvas.width, canvas.height);
  const imgData = canvas.toDataURL('image/png');

  // Año dinámico: año actual + 1
  const anio = (new Date()).getFullYear() + 1;
  const meses = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
  const fechaNow = new Date();
  const fechaFormateada = `${fechaNow.getDate()} de ${meses[fechaNow.getMonth()]} de ${fechaNow.getFullYear()}`;

  detallesArray.forEach((det, idx) => {
    // Limpiar fuentes por página
    doc.setFont("helvetica", "normal");

    // ORIGINAL (arriba)
    doc.setFontSize(12);
    doc.text(`Mayordomía Señor San José ${anio}`, 78, 15);
    doc.text('Mayordomía Tickets', 92, 21);
    doc.text('Comprobante de pago', 90, 27);

    // Imagen (izq)
    try { doc.addImage(imgData, 'PNG', 20, 15, 55, 50); } catch(e){}

    // Número y fecha (derecha)
    doc.setFontSize(12);
    doc.text(`Número de boleto: ${det.numero_boleto ?? '-'}`, 130, 45);
    doc.text(`Fecha: ${fechaFormateada}`, 130, 52);

    // Datos (lado izquierdo)
    doc.setFontSize(12);
    doc.text(`Nombre: ${det.nombre ?? '-'}`, 20, 74);
    doc.text(`Calle: ${det.calle ?? '-'}`, 20, 81);
    doc.text(`Ciudad: ${det.ciudad ?? '-'}`, 20, 88);
    doc.text(`Teléfono 1: ${det.telefono1 ?? '0'}`, 20, 95);

    // Datos (lado derecho)
    doc.text(`Número: ${det.numero ?? '-'}`, 120, 81);
    doc.text(`Colonia: ${det.colonia ?? '-'}`, 120, 88);
    doc.text(`Teléfono 2: ${det.telefono2 ?? '0'}`, 120, 95);

    // Precio y textos centrales
    doc.text(`$${PRECIO_BOLETO.toFixed(2)} (ciento ochenta pesos 00/100 m.n.)`, 20, 106);
    doc.text('50% para el culto de Señor San José', 70, 116);
    doc.text('50% para gastos de la mayordomía', 72, 124);

    // Pie
    doc.text('boletos.mayordomiatickets.com', 77, 135);
    doc.text('Original', 180, 135);

    // Marco
    doc.rect(10, 6, 190, 135);

    // Separador (guiones)
    doc.setFontSize(12);
    doc.text('_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _', 0, 148);

    // COPIA (misma hoja, debajo)
    //const offsetY = 155;
    doc.setFontSize(12);
    doc.text(`Mayordomía Señor San José ${anio}`, 78, 165);
    doc.setFontSize(12);
    doc.text('Mayordomía Tickets', 92, 171);
    doc.text('Comprobante de pago', 90, 177);

    try { doc.addImage(imgData, 'PNG', 20, 165, 55, 50); } catch(e){}

    doc.setFontSize(12);
    doc.text(`Número de boleto: ${det.numero_boleto ?? '-'}`, 130, 195);
    doc.text(`Fecha: ${fechaFormateada}`, 130, 202);

    doc.setFontSize(12);
    doc.text(`Nombre: ${det.nombre ?? '-'}`, 20, 225);
    doc.text(`Calle: ${det.calle ?? '-'}`, 20, 232);
    doc.text(`Ciudad: ${det.ciudad ?? '-'}`, 20, 239);
    doc.text(`Teléfono 1: ${det.telefono1 ?? '0'}`, 20,246);

    doc.text(`Número: ${det.numero ?? '-'}`, 120, 232);
    doc.text(`Colonia: ${det.colonia ?? '-'}`, 120, 239);
    doc.text(`Teléfono 2: ${det.telefono2 ?? '0'}`, 120, 246);

    doc.text(`$${PRECIO_BOLETO.toFixed(2)} (ciento ochenta pesos 00/100 m.n.)`, 20, 257);
    doc.text('50% para el culto de Señor San José', 70, 267);
    doc.text('50% para gastos de la mayordomía', 72, 275);

    doc.text('boletos.mayordomiatickets.com', 77, 286);
    doc.text('Copia', 180, 285);

    doc.setFont("helvetica", "bold");
    doc.text('Nota:', 9, 295);
    doc.setFont("helvetica", "normal");
    doc.text('Recuerda que puedes comprar tus boletos a través de internet. Facebook: Mayordomía Tickets', 21, 295);

    doc.rect(10, 155, 190, 135);

    // Si no es el último, nueva página
    if (idx < detallesArray.length - 1) doc.addPage();
  });

  // Descargar automáticamente
  const filename = `Boletos_Pagados_${(new Date()).toISOString().slice(0,19).replace(/[:T]/g,'_')}.pdf`;
  doc.save(filename);
}

/* escape simple para evitar XSS en DOM */
function escapeHtml(str) {
  return String(str || '').replace(/[&<>"'`=\/]/g, function(s) {
    return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;', '/': '&#x2F;', '`': '&#x60;', '=': '&#x3D;' })[s];
  });
}
</script>

<style>
.modalPago {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}
.modalContenido {
  background: #fff;
  padding: 25px;
  border-radius: 12px;
  text-align: center;
  width: 400px;
}
.modalContenido input, .modalContenido select {
  width: 100%;
  padding: 8px;
  margin: 5px 0 10px;
}
.modalBotones button {
  margin: 5px;
  padding: 8px 12px;
  cursor: pointer;
}
.boleto-item { margin-bottom: 10px; }
</style>

</body>
</html>