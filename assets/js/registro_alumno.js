// Uso en página normal:
window.addEventListener('load', function () {
    initRegistroAlumnoForm(document);
});


function initRegistroAlumnoForm(raiz) {
    // ---- Estudios ----
    const estudiosWrapper = raiz.querySelector('#estudios-wrapper');
    const btnAgregarEstudio = raiz.querySelector('#btn-agregar-estudio');
    const plantillaEstudio = `
        <div class="form-estudio">
            <div class="form-group">
                <label>Familia profesional</label>
                <select class="familia" name="familia[]"></select>
            </div>
            <div class="form-group">
                <label>Ciclo formativo</label>
                <select class="ciclo" name="ciclo[]"></select>
            </div>
            <div class="form-group">
                <label>Fecha inicio</label>
                <input type="date" class="fechainicio" name="fechainicio[]">
            </div>
            <div class="form-group">
                <label>Fecha fin</label>
                <input type="date" class="fechafin" name="fechafin[]">
            </div>
            <button type="button" class="btn-eliminar-estudio">Eliminar</button>
        </div>
 `;

    // Carga familias desde API 
    let familiasCache = [];
    fetch("api/apiFamilia.php")
        .then(res => res.json())
        .then(familias => { familiasCache = familias; });

    // Añadir estudio
    if (btnAgregarEstudio && estudiosWrapper) {
        btnAgregarEstudio.addEventListener('click', () => {
            estudiosWrapper.insertAdjacentHTML('beforeend', plantillaEstudio);
            inicializarNuevoEstudio(estudiosWrapper.lastElementChild);
        });

        // Eliminar bloque de estudio
        estudiosWrapper.addEventListener('click', function (e) {
            if (e.target.classList.contains('btn-eliminar-estudio')) {
                e.target.closest('.form-estudio').remove();
            }
        });

        // Inicializa selects para familia y ciclo
        function inicializarNuevoEstudio(estudioDiv) {
            const selectFamilia = estudioDiv.querySelector('.familia');
            const selectCiclo = estudioDiv.querySelector('.ciclo');
            selectFamilia.innerHTML = '<option value="">Selecciona familia</option>';
            familiasCache.forEach(f => {
                const option = document.createElement('option');
                option.value = f.id;
                option.textContent = f.nombre;
                selectFamilia.appendChild(option);
            });
            selectFamilia.addEventListener('change', function () {
                const famId = selectFamilia.value;
                selectCiclo.innerHTML = '<option value="">Selecciona ciclo</option>';
                selectCiclo.disabled = !famId;
                if (!famId) return;
                fetch(`api/apiCiclo.php?familia_id=${famId}`)
                    .then(res => res.json())
                    .then(ciclos => {
                        ciclos.forEach(c => {
                            const option = document.createElement('option');
                            option.value = c.id;
                            option.textContent = c.nombre;
                            selectCiclo.appendChild(option);
                        });
                    });
            });
            selectCiclo.disabled = true;
        }
    }


    const formRegistro = document.getElementById('form-registro-alumno');

    if (formRegistro){
       
        formRegistro.addEventListener('submit', function (e) {
            debugger;
            e.preventDefault();
            let datos = new FormData(formRegistro);
            fetch('api/apiAlumno.php', {
                method: 'POST',
                body: datos
            })
            .then(res => res.json())
            .then(resp => {
                if(resp.status === "ok") {
                    alert(resp.mensaje);
                    window.location.href = 'index.php?page=login';
                } else {
                    alert("Error: " + resp.mensaje);
                }
            })
            .catch(err => {
                alert("Fallo en petición AJAX");
            });
        });
    }
  
    // ---- Foto y Cámara ----
    let streamActivo = null;
    const btnTomarFoto = raiz.querySelector('#tomarFotoBtn');
    if (btnTomarFoto) {
        btnTomarFoto.addEventListener('click', abrirModalCamaraJobyz);
    }
    function abrirModalCamaraJobyz() {
        fetch('assets/modales/modalCaptura.txt')
            .then(r => r.text())
            .then(html => {
                modalManager.crearModal(html);
                iniciarCamara();
                document.getElementById('capturarFotoBtn').onclick = capturarFoto;
                document.getElementById('cancelarFotoBtn').onclick = cerrarModalCamara;
            });
    }
    function iniciarCamara() {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function (stream) {
                streamActivo = stream;
                const video = document.getElementById('videoFoto');
                video.srcObject = stream;
                video.play();
            });
    }
    function capturarFoto() {
        const video = document.getElementById('videoFoto');
        const canvas = document.getElementById('canvasFoto');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        const dataUrl = canvas.toDataURL('image/png');
        if (raiz.querySelector('#preview-foto')) raiz.querySelector('#preview-foto').innerHTML = `<img src="${dataUrl}" style="width:110px">`;
        dataURLtoFileInputJobyz(dataUrl, 'foto-captura.png', raiz.querySelector('#fotoFile'));
        cerrarModalCamara();
    }
    function cerrarModalCamara() {
        modalManager.cerrarModal();
        if (streamActivo) {
            streamActivo.getTracks().forEach(track => track.stop());
            streamActivo = null;
        }
    }
    function dataURLtoFileInputJobyz(dataUrl, filename, input) {
        fetch(dataUrl)
            .then(res => res.blob())
            .then(blob => {
                const file = new File([blob], filename, { type: blob.type });
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
            });
    }
    // ---- Preview de archivo foto ---- //
    const inputFileFoto = raiz.querySelector('#fotoFile');
    const previewFoto = raiz.querySelector('#preview-foto');
    if (inputFileFoto) {
        inputFileFoto.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    if (previewFoto) previewFoto.innerHTML = `<img src="${e.target.result}" width="120">`;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // ---- Salir de la página ---- //
    const salir = document.getElementById("btn-salir");

    if (salir){
        salir.addEventListener('click', function(e){
            window.location.href= "index.php?page=landing";
        });
    }
    



}


function initCargaMasiva(raiz){
    const selectFamilia = raiz.querySelector('#familia');
    const selectCiclo = raiz.querySelector('#ciclo');
    const inputCSV = raiz.querySelector('#csvAlumnos');
    const btnPreview = raiz.querySelector('#btnPrevisualizar');
    const btnSubir = raiz.querySelector('#btnSubirAlumnos');
    const previewDiv = raiz.querySelector('#previewCSV');
    const form = raiz.querySelector('#formCargaMasivaAlumnos');
    const btnCancelar = raiz.querySelector('#btnCancelarCarga');
    let parsedRows = [];

    // Familias
    fetch("api/apiFamilia.php")
        .then(res => res.json())
        .then(familias => {
            selectFamilia.innerHTML = '<option value="">Selecciona familia</option>';
            familias.forEach(f =>
                selectFamilia.innerHTML += `<option value="${f.id}">${f.nombre}</option>`
            );
        });

    // Ciclos
    selectFamilia.addEventListener('change', function () {
        const famId = selectFamilia.value;
        selectCiclo.innerHTML = '<option value="">Selecciona ciclo</option>';
        selectCiclo.disabled = !famId;
        if (!famId) return;
        fetch('api/apiCiclo.php?familia_id=' + famId)
            .then(res => res.json())
            .then(ciclos => {
                ciclos.forEach(c =>
                    selectCiclo.innerHTML += `<option value="${c.id}">${c.nombre}</option>`
                );
            });
    });
    selectCiclo.disabled = true;

    // Preview
    btnPreview.addEventListener('click', function (e) {
        e.preventDefault();
        const file = inputCSV.files[0];
        if (!file) return alert('Selecciona un archivo CSV primero.');
            const reader = new FileReader();
            reader.onload = evt => {
                const lines = evt.target.result.split(/\r?\n/);
                parsedRows = [];
                let html = `<table style="width:100%"><thead>
                    <tr>
                        <th>Subir  <input type="checkbox" id="check-todos" checked></th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Correo</th>
                        <th></th>
                    </tr>
                    </thead><tbody>`;
                lines.forEach((line, idx) => {
                    if (!line.trim()) return;
                    const [nombre, apellido, correo, dni] = line.split(',');
                    if (!nombre || !apellido || !correo || !dni) return;
                    parsedRows.push({nombre, apellido, correo, dni});
                    html += `<tr>
                        <td><input type="checkbox" class="fila-checkbox" data-idx="${idx}" checked></td>
                        <td><input type="text" class="input-nombre" value="${nombre}" data-idx="${idx}"></td>
                        <td><input type="text" class="input-apellido" value="${apellido}" data-idx="${idx}"></td>
                        <td><input type="email" class="input-correo" value="${correo}" data-idx="${idx}"></td>
                        <td><input type="hidden" class="input-dni" value="${dni}" data-idx="${idx}"></td>
                    </tr>`;
            });

            html += "</tbody></table>";
            previewDiv.innerHTML = html;
            btnSubir.style.display = "inline-block";

            // Checkbox toggle para seleccionar/des-seleccionar todas las filas
            const checkTodos = document.getElementById('check-todos');
            if (checkTodos) {
                checkTodos.addEventListener('change', function () {
                    const checks = previewDiv.querySelectorAll('.fila-checkbox');
                    checks.forEach(ck => ck.checked = checkTodos.checked);
                });
            }
        };
        reader.readAsText(file);
    });

    // Subir seleccionados al backend (con inputs editables en cada celda)
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const checkboxes = previewDiv.querySelectorAll('.fila-checkbox');
        const seleccionados = [];
        checkboxes.forEach((ck) => {
            if (ck.checked) {
                const idx = ck.getAttribute('data-idx');
                const nombre = previewDiv.querySelector(`.input-nombre[data-idx="${idx}"]`).value;
                const apellido = previewDiv.querySelector(`.input-apellido[data-idx="${idx}"]`).value;
                const correo = previewDiv.querySelector(`.input-correo[data-idx="${idx}"]`).value;
                const dni = previewDiv.querySelector(`.input-dni[data-idx="${idx}"]`).value;
                seleccionados.push({nombre, apellido, correo, dni});
            }
        });
        const familia = selectFamilia.value;
        const ciclo = selectCiclo.value;
        if (!familia || !ciclo) return alert('Familia y ciclo obligatorios.');
        if (!seleccionados.length) return alert('Debes seleccionar al menos un usuario.');

        fetch('api/apiAlumno.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                usuarios: seleccionados,
                familia, ciclo
            })
        })
        .then(r => r.json())
        .then(data => {
            // Mostrar fallos en el div del modal
            const divFallos = document.getElementById('carga-fallos');
            divFallos.innerHTML = '';
            if (data.errores && data.errores.length) {
                let html = `<div style="color:red;font-weight:bold;margin-bottom:6px;">No se pudieron cargar:</div><ul>`;
                data.errores.forEach(email => {
                    html += `<li>${email}</li>`;
                });
                html += "</ul>";
                divFallos.innerHTML = html;
            }

            // Pintar tabla con los alumnos insertados
            if (data.ok && data.alumnos && data.alumnos.length) {
                recargarAlumnos();
            }

            if (!data.ok) {
                alert('Error cargando: ' + (data.error || ''));
            }
        });
    });



    // Cancelar modal
    btnCancelar.onclick = function () {
        form.reset();
        selectCiclo.innerHTML = '<option value="">Selecciona ciclo</option>';
        previewDiv.innerHTML = '';
        btnSubir.style.display = 'none';
        modalManager.cerrarModal();
    };

}

// Uso en modal, tras cargar el modal (en el callback de crearModalDesdeUrl):
// let modalRaiz = document.querySelector('.modal-contenedor');
// initRegistroAlumnoForm(modalRaiz);
