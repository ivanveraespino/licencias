// Importaciones desde CDN oficial apto para CORS y módulos ES6

document.addEventListener('DOMContentLoaded', function () {
    const fechaFinInput = document.getElementById('fecha-fin');
    const fechaIniInput = document.getElementById('fecha-ini');

    // 1. OBTENER Y FORMATEAR FECHAS
    const hoy = new Date();
    const haceUnMes = new Date();
    haceUnMes.setMonth(hoy.getMonth() - 1);

    const formatear = (date) => date.toISOString().split('T')[0];

    // 2. ASIGNAR VALORES INICIALES
    fechaFinInput.value = formatear(hoy);
    fechaIniInput.value = formatear(haceUnMes);

    // 3. ESTABLECER LÍMITES INICIALES (Mínimos y Máximos)
    fechaFinInput.min = fechaIniInput.value;
    fechaIniInput.max = fechaFinInput.value;

    // 4. VALIDACIÓN DINÁMICA (Lo que preguntaste)
    // Cuando cambies la fecha inicial, la fecha final no puede ser menor a esa
    fechaIniInput.addEventListener('change', () => {
        fechaFinInput.min = fechaIniInput.value;
    });

    // Cuando cambies la fecha final, la fecha inicial no puede ser mayor a esa
    fechaFinInput.addEventListener('change', () => {
        fechaIniInput.max = fechaFinInput.value;
    });
});

document.getElementById('btn-consultar').addEventListener('click', function () {
    const fechaIni = document.getElementById('fecha-ini').value;
    const fechaFin = document.getElementById('fecha-fin').value;

    if (!fechaIni || !fechaFin) {
        alert("Por favor, seleccione el rango de fechas.");
        return;
    }

    const params = new URLSearchParams({
        'fecha-ini': fechaIni,
        'fecha-fin': fechaFin,
        'giro': document.getElementById('giro').value,
        'tipo': document.getElementById('tipo').value,
        'licencia': document.getElementById('licencia').value
    });

    const contenedor = document.getElementById('respuesta');
    contenedor.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-info" role="status"></div><p class="mt-2">Buscando registros...</p></div>';

    // Deshabilitamos botones de exportación al iniciar una nueva búsqueda
    document.getElementById('btn-exportar-excel').disabled = true;
    document.getElementById('btn-exportar-pdf').disabled = true;

    fetch('/consulta?' + params.toString())
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            return response.text();
        })
        .then(html => {
            contenedor.innerHTML = html;

            const tabla = document.getElementById('tabla-licencias');

            if (tabla) {
                // Habilitamos los botones ya que la tabla existe con datos
                document.getElementById('btn-exportar-excel').disabled = false;
                document.getElementById('btn-exportar-pdf').disabled = false;

                if ($.fn.DataTable.isDataTable(tabla)) {
                    $(tabla).DataTable().destroy();
                }

                // Inicialización simple de DataTable (Sin extensiones de botones conflictivas)
                $(tabla).DataTable({
                    language: {
                        search: "Buscar:",
                        lengthMenu: "Mostrar _MENU_ registros",
                        info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                        zeroRecords: "No se encontraron resultados",
                        paginate: {
                            previous: "Anterior",
                            next: "Siguiente"
                        }
                    },
                    // Corrección sintáctica: el 0 indica la primera columna
                    columnDefs: [{
                        visible: true,
                        searchable: true
                    }],
                    pageLength: 10,
                    responsive: true,
                    createdRow: function (row, data, dataIndex) {
                        $(row).css('font-size', '13px');
                    }
                });


            }
        })
        .catch(error => {
            contenedor.innerHTML = '<div class="alert alert-danger m-3">Error: ' + error.message + '</div>';
            console.error(error);
        });
});

// LOGICA ALTERNATIVA DE EXPORTACIÓN A EXCEL
document.getElementById('btn-exportar-excel').addEventListener('click', function () {
    const fechaIni = document.getElementById('fecha-ini').value;
    const fechaFin = document.getElementById('fecha-fin').value;
    // 1. Acceder a la instancia de DataTables
    const tablaDataTable = $('#tabla-licencias').DataTable();
    if (!tablaDataTable) return;


    // 1. Obtener el número total de columnas dinámicamente
    const totalColumnas = tablaDataTable.columns().count();

    // 2. Restamos 1 columna al ancho del texto porque el logo ocupará la primera columna
    const columnasTexto = totalColumnas - 1;


    const dominioAutomatico = window.location.origin;
    const rutaLogoIncrustado = `${dominioAutomatico}/logo-excel`; // Apunta a la ruta de Symfony

    // 3. Crear la fila del título principal que ocupa todo el ancho
    let encabezadosHtml = `
    <tr>
        <!-- Celda del Logo: Carga interna directa e independiente a través de Base64 -->
<th rowspan="3" style="background-color: #F8F9FA; text-align: center; vertical-align: middle; padding: 5px; width: 150px; border: 0.5pt solid #D0D7DE !important;">
    <img src="${rutaLogoIncrustado}" alt="Logo" width="90" style="display: inline-block; vertical-align: middle; margin: 0 auto;">
</th>
        <!-- Fila 1: Título Principal -->
        <th colspan="${columnasTexto}" style="font-size: 16pt; font-weight: bold; text-align: center; background-color: #E9ECEF; padding: 15px; vertical-align: middle;">
            MUNICIPALIDAD PROVINCIAL DE LA CONVENCIÓN
        </th>
    </tr>
    
    <tr style="height: 50px;">
        <!-- Fila 2: Subtítulo Sistema -->
        <th colspan="${columnasTexto}" style="font-size: 12pt; font-weight: bold; text-align: center; background-color: #E9ECEF; padding: 15px; vertical-align: middle;">
            Sistema de consulta de Licencias
        </th>
    </tr>
    <tr style="height: 50px;">
        <!-- Fila 3: Rango de Fechas -->
        <th colspan="${columnasTexto}" style="font-size: 12pt; font-weight: bold; text-align: center; background-color: #E9ECEF; padding: 15px; vertical-align: middle;">
            Reporte de licencias emitidas del ${fechaIni} al ${fechaFin}
        </th>
    </tr>
    `;

    // Adjuntar los encabezados originales de la tabla debajo del título principal
    encabezadosHtml += $('#tabla-licencias thead').html();

    // 4. Extraer TODOS los datos (ignora la paginación de la pantalla)
    let lasFilasHtml = '';
    const datosTodos = tablaDataTable.rows({
        search: 'applied'
    }).data().toArray();

    // 5. Construir dinámicamente el cuerpo de la tabla
    datosTodos.forEach(fila => {
        lasFilasHtml += '<tr>';
        const celdas = Array.isArray(fila) ? fila : Object.values(fila);
        celdas.forEach(celda => {
            lasFilasHtml += `<td>${celda !== null && celda !== undefined ? celda : ''}</td>`;
        });
        lasFilasHtml += '</tr>';
    });

    // 6. Estructura HTML final con los estilos de la cuadrícula
    const plantillaExcel = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://w3.org">
        <head>
            <meta charset="UTF-8">
            <style>
                table, th, td {
                    border: 0.5pt solid #D0D7DE !important;
                    border-collapse: collapse;
                    font-family: Calibri, sans-serif;
                }
                th {
                    background-color: #F8F9FA;
                    font-weight: bold;
                }
            </style>
        </head>
        <body>
            <table>
                <thead>${encabezadosHtml}</thead>
                <tbody>${lasFilasHtml}</tbody>
            </table>
        </body>
        </html>
    `;

    // 7. Crear el archivo Blob binario directo e iniciar descarga
    const blob = new Blob([plantillaExcel], {
        type: 'application/vnd.ms-excel;charset=utf-8;'
    });

    if (navigator.msSaveOrOpenBlob) {
        navigator.msSaveOrOpenBlob(blob, 'Reporte_Licencias.xls');
    } else {
        const enlaceDescarga = document.createElement("a");
        const url = URL.createObjectURL(blob);

        enlaceDescarga.href = url;
        enlaceDescarga.download = 'Reporte_Licencias.xls';

        enlaceDescarga.style.display = 'none';
        document.body.appendChild(enlaceDescarga);

        enlaceDescarga.click();

        setTimeout(function () {
            document.body.removeChild(enlaceDescarga);
            URL.revokeObjectURL(url);
        }, 100);
    }
});


// LOGICA ALTERNATIVA DE EXPORTACIÓN A PDF
document.getElementById('btn-exportar-pdf').addEventListener('click', function () {
    const fechaIni = document.getElementById('fecha-ini').value;
    const fechaFin = document.getElementById('fecha-fin').value;
    
    if (!window.jspdf || !window.jspdf.jsPDF) {
        alert("La librería jsPDF no se ha cargado correctamente.");
        return;
    }

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'pt', 'letter');

    // 1. Crear y precargar la imagen desde tu ruta local
    const imgLogo = new Image();
    imgLogo.src = '/img/logox100.png';

    // 2. Esperar a que la imagen cargue completamente antes de armar el PDF
    imgLogo.onload = function () {
        
        // --- NUEVA LÓGICA PARA EXTRAER TODOS LOS DATOS DE DATATABLES ---
        let tablaHead = [];
        let tablaBody = [];
        let totalRegistros = 0; // CORREGIDO: Se cambia const por let para poder modificarlo
        
        // Comprobamos si tienes DataTables activo en esa tabla
        if ($.fn.DataTable.isDataTable('#tabla-licencias')) {
            const table = $('#tabla-licencias').DataTable();
            
            // Extraer los nombres de las columnas (Cabecera)
            let headers = [];
            table.columns().every(function () {
                headers.push($(this.header()).text().trim());
            });
            tablaHead.push(headers);

            // Extraer ABSOLUTAMENTE TODAS las filas (ignora la paginación visual)
            tablaBody = table.rows({ search: 'applied' }).data().toArray();
            
            totalRegistros = tablaBody.length; // CORREGIDO: Ortografía de 'length'
            doc.cantidadRegistros = totalRegistros; // Se guarda en el objeto del documento
        } else {
            alert("No se detectó la instancia de DataTables. Se intentará exportar la vista actual.");
            doc.cantidadRegistros = 0;
        }

        doc.autoTable({
            head: tablaHead,
            body: tablaBody,
            startY: 95,
            margin: {
                top: 95,
                bottom: 50,
                left: 40,
                right: 40
            },
            styles: {
                fontSize: 8
            },
            headStyles: {
                fillColor: [0, 126, 51] 
            },

            didDrawPage: function (data) {
                // --- ENCABEZADO ---
                doc.addImage(imgLogo, 'PNG', 40, 25, 45, 45);

                doc.setFont("helvetica", "bold");
                doc.setFontSize(14);
                doc.setTextColor(0, 126, 51);
                doc.text("MUNICIPALIDAD PROVINCIAL DE LA CONVENCIÓN", 100, 42);

                doc.setFont("helvetica", "normal");
                doc.setFontSize(10);
                doc.setTextColor(110, 110, 110);
                
                // CORREGIDO: Se usa doc.cantidadRegistros para llamarlo correctamente
                doc.text(`Reporte de Licencias Emitidas del ${fechaIni} al ${fechaFin} - ${doc.cantidadRegistros} registros encontrados`, 100, 56);

                doc.setDrawColor(208, 215, 222);
                doc.setLineWidth(1);
                doc.line(40, 80, doc.internal.pageSize.width - 40, 80);
            }
        });

        // --- PROCESAR EL PIE DE PÁGINA AL FINAL ---
        const totalPages = doc.internal.getNumberOfPages();
        const dataPageWidth = doc.internal.pageSize.width;
        const dataPageHeight = doc.internal.pageSize.height;

        // Obtener y formatear la fecha y hora actual
        const ahora = new Date();
        const dia = String(ahora.getDate()).padStart(2, '0');
        const mes = String(ahora.getMonth() + 1).padStart(2, '0');
        const anio = ahora.getFullYear();
        const horas = String(ahora.getHours()).padStart(2, '0');
        const minutes = String(ahora.getMinutes()).padStart(2, '0');
        
        const fechaHoraStr = `Generado el: ${dia}/${mes}/${anio} ${horas}:${minutes}`;

        for (let i = 1; i <= totalPages; i++) {
            doc.setPage(i);
            
            doc.setFont("helvetica", "normal");
            doc.setFontSize(8);
            doc.setTextColor(150, 150, 150);

            const yPosition = dataPageHeight - 30; 

            // Dibujar la Fecha y Hora a la izquierda
            const marginIzquierdo = 40;
            doc.text(fechaHoraStr, marginIzquierdo, yPosition);

            // Dibujar el Número de Página a la derecha
            const strPaginas = "Página " + i + " de " + totalPages;
            const marginDerecho = 40;
            const xPositionDerecha = dataPageWidth - doc.getTextWidth(strPaginas) - marginDerecho;
            
            doc.text(strPaginas, xPositionDerecha, yPosition);
        }

        doc.save('Reporte_Licencias.pdf');
    };

    imgLogo.onerror = function () {
        alert("No se pudo cargar el logo desde la ruta especificada. Verifique que exista.");
    };
});
