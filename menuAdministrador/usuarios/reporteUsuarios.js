function toggleAllCheckboxes(checkbox) {
    var checkboxes = document.getElementsByClassName("column-checkbox");

    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = checkbox.checked;
    }
}

function generateReport() {
    // Obtener todas las filas de la tabla
    var table = document.getElementsByClassName("user-table")[0];
    var rows = table.getElementsByTagName("tr");

    // Crear un arreglo para almacenar las columnas seleccionadas
    var selectedColumns = [];

    // Recorrer todas las filas
    for (var i = 0; i < rows.length; i++) {
        var row = rows[i];
        var cells = row.getElementsByTagName("td");

        // Crear un objeto para almacenar los valores de las columnas seleccionadas en la fila actual
        var selectedData = {};

        // Recorrer todas las celdas de la fila actual
        for (var j = 0; j < cells.length; j++) {
            var cell = cells[j];
            var checkbox = cell.getElementsByTagName("input")[0];

            // Verificar si el checkbox está marcado
            if (checkbox.checked) {
                // Obtener el encabezado de la columna correspondiente
                var header = table.getElementsByTagName("th")[j + 1]; // Sumar 1 para omitir la primera columna vacía
                var columnName = header.innerText;

                // Almacenar el valor de la celda en el objeto de datos seleccionados
                selectedData[columnName] = cell.innerText;
            }
        }

        // Agregar el objeto de datos seleccionados al arreglo de columnas seleccionadas
        selectedColumns.push(selectedData);
    }

    // Generar el reporte en la consola (puedes cambiar esto para adaptarlo a tu requerimiento)
    console.log(selectedColumns);
}