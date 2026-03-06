import * as bootstrap from 'bootstrap';
const inputs = document.querySelectorAll('input[type="text"]');

inputs.forEach(input => {
    input.addEventListener('blur', function () {
        this.value = this.value.toUpperCase();
    });
});
document.getElementById('dep-rep').addEventListener('change', function () {
    const depId = this.value;
    const provSelect = document.getElementById('prov-rep');
    const disSelect = document.getElementById('dis-rep');

    // Limpiar provincias y distritos previos
    provSelect.innerHTML = '<option disabled selected></option>';
    disSelect.innerHTML = '<option disabled selected></option>';

    if (depId) {
        // Reemplaza '/get-provincias/' por tu ruta real de backend
        fetch(`/get-provincias/${depId}/`)
            .then(response => response.json())
            .then(data => {
                data.forEach(prov => {
                    const option = document.createElement('option');
                    option.value = prov.id;
                    option.textContent = prov.nombre;
                    provSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error cargando provincias:', error));
    }
});
document.getElementById('prov-rep').addEventListener('change', function () {
    const provId = this.value;
    const disSelect = document.getElementById('dis-rep');

    // Limpiar distritos previos
    disSelect.innerHTML = '<option disabled selected></option>';

    if (provId) {
        // Reemplaza '/get-distritos/' por tu ruta real de backend
        fetch(`/get-distritos/${provId}/`)
            .then(response => response.json())
            .then(data => {
                data.forEach(dis => {
                    const option = document.createElement('option');
                    option.value = dis.id;
                    option.textContent = dis.nombre;
                    disSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error cargando distritos:', error));
    }
});


document.getElementById('dep-sede').addEventListener('change', function () {
    const depId = this.value;
    const provSelect = document.getElementById('prov-sede');
    const disSelect = document.getElementById('dis-sede');

    // Limpiar provincias y distritos previos
    provSelect.innerHTML = '<option disabled selected></option>';
    disSelect.innerHTML = '<option disabled selected></option>';

    if (depId) {
        // Reemplaza '/get-provincias/' por tu ruta real de backend
        fetch(`/get-provincias/${depId}/`)
            .then(response => response.json())
            .then(data => {
                data.forEach(prov => {
                    const option = document.createElement('option');
                    option.value = prov.id;
                    option.textContent = prov.nombre;
                    provSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error cargando provincias:', error));
    }
});
document.getElementById('prov-sede').addEventListener('change', function () {
    const provId = this.value;
    const disSelect = document.getElementById('dis-sede');

    // Limpiar distritos previos
    disSelect.innerHTML = '<option disabled selected></option>';

    if (provId) {
        // Reemplaza '/get-distritos/' por tu ruta real de backend
        fetch(`/get-distritos/${provId}/`)
            .then(response => response.json())
            .then(data => {
                data.forEach(dis => {
                    const option = document.createElement('option');
                    option.value = dis.id;
                    option.textContent = dis.nombre;
                    disSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error cargando distritos:', error));
    }
});



document.getElementById('ficha-ruc').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('ficha-ruc', file);

    // Aquí debes reemplazar '/upload-ficha' por tu ruta real de backend
    fetch('/subir-pdf', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Supongamos que tu backend devuelve { url: "/uploads/ficha.pdf" }
            const url = data.url;
            document.getElementById('url-ficha').value = url;

            const link = document.getElementById('ver-ficha');
            link.href = url;
            link.textContent = "Ver Ficha";
        })
        .catch(error => console.error('Error subiendo archivo:', error));
});

document.getElementById('cert-comp-suelos-sede').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('file', file); // debe coincidir con el backend

    fetch('/subir-pdf', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.url) {
                // Guardar la URL en el campo oculto
                document.getElementById('url-uso-suelo-sede').value = data.url;

                // Actualizar el enlace para ver el PDF
                const link = document.getElementById('ver-certificado-suelos');
                link.href = data.url;
                link.textContent = "Ver Certificado";
            } else {
                console.error('Error:', data.error);
            }
        })
        .catch(error => console.error('Error subiendo archivo:', error));
});

document.getElementById('cert-def-civil-sede').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('file', file); // debe coincidir con el backend

    fetch('/subir-pdf', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.url) {
                // Guardar la URL en el campo oculto
                document.getElementById('url-def-civil-sede').value = data.url;

                // Actualizar el enlace para ver el PDF
                const link = document.getElementById('ver-def-civil');
                link.href = data.url;
                link.textContent = "Ver Certificado";
            } else {
                console.error('Error:', data.error);
            }
        })
        .catch(error => console.error('Error subiendo archivo:', error));
});

document.getElementById('guardar').addEventListener('click', function (e) {
    e.preventDefault(); // Evita el submit tradicional

    const form = document.querySelector('form');
    const formData = new FormData(form);

    fetch('/guardar-negocio', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Datos guardados correctamente");
                // Aquí podrías redirigir o generar un PDF
                if (data.pdfUrl) { 
                    window.open(data.pdfUrl, '_blank'); 
                }
                // Abrir el PDF en una nueva ventana

                window.location.href = '/home';
                
                
            } else {
                alert("Error al guardar: " + data.error);
            }
        })
        .catch(error => console.error('Error:', error));
});
document.getElementById('generar-licencia').addEventListener('click', function (e) {
    e.preventDefault(); // Evita el submit tradicional

    const form = document.querySelector('form');
    const formData = new FormData(form);

    fetch('/guardar-negocio-2', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Datos guardados correctamente");
                // Aquí podrías redirigir o generar un PDF
                if (data.pdfUrl) { 
                    window.open(data.pdfUrl, '_blank'); 
                }
                // Abrir el PDF en una nueva ventana

                window.location.href = '/home';
                
                
            } else {
                alert("Error al guardar: " + data.error);
            }
        })
        .catch(error => console.error('Error:', error));
});
document.getElementById('giro').addEventListener('change', function () {
    if (this.value === "0") {
        // Mostrar modal
        var modal = new bootstrap.Modal(document.getElementById('modalGiro'));
        modal.show();
    }
});

document.getElementById('guardar-giro').addEventListener('click', function () {
    const nuevoGiro = document.getElementById('nuevo-giro').value.trim();
    if (!nuevoGiro) return;

    fetch('/guardar-giro', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                actividad: nuevoGiro
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Crear nueva opción en el select
                const select = document.getElementById('giro');
                const option = document.createElement('option');
                option.value = data.id; // id devuelto por backend
                option.textContent = data.actividad;
                select.appendChild(option);
                select.value = data.id;

                // Cerrar modal
                bootstrap.Modal.getInstance(document.getElementById('modalGiro')).hide();
            } else {
                alert("Error al guardar giro: " + data.error);
            }
        })
        .catch(error => console.error('Error:', error));
});

document.getElementById('tipo-sede').addEventListener('change', function () {
    if (this.value === "0") {
        var modal = new bootstrap.Modal(document.getElementById('modalTipo'));
        modal.show();
    }
});

document.getElementById('guardar-tipo').addEventListener('click', function () {
    const nuevoTipo = document.getElementById('nuevo-tipo').value.trim();
    if (!nuevoTipo) return;

    fetch('/guardar-tipo', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                denominacion: nuevoTipo
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('tipo-sede');
                const option = document.createElement('option');
                option.value = data.id;
                option.textContent = data.denominacion;
                select.appendChild(option);
                select.value = data.id;

                bootstrap.Modal.getInstance(document.getElementById('modalTipo')).hide();
            } else {
                alert("Error al guardar tipo: " + data.error);
            }
        })
        .catch(error => console.error('Error:', error));
});


const diasInput = document.getElementById('dias-licencia');
const iniInput = document.getElementById('ini-licencia');
const finInput = document.getElementById('fin-licencia');

function calcularFin() {
    const dias = parseInt(diasInput.value, 10);
    const fechaInicio = iniInput.value;

    if (!isNaN(dias) && fechaInicio) {
        const inicio = new Date(fechaInicio);
        inicio.setDate(inicio.getDate() + dias); // suma los días
        // Formatea a yyyy-mm-dd
        const yyyy = inicio.getFullYear();
        const mm = String(inicio.getMonth() + 1).padStart(2, '0');
        const dd = String(inicio.getDate()).padStart(2, '0');
        finInput.value = `${yyyy}-${mm}-${dd}`;
    }
}

diasInput.addEventListener('input', calcularFin);
iniInput.addEventListener('change', calcularFin);
