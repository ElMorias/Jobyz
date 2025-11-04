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

    // Carga familias desde API (una vez por contexto/formulario)
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

    const formRegistro = document.getElementById('form-registrar-alumno');

    // SOLO aquí añades el submit AJAX
    formRegistro.addEventListener('submit', function (e) {
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
    // ---- Preview de archivo foto ----
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


}

// Uso en modal, tras cargar el modal (en el callback de crearModalDesdeUrl):
// let modalRaiz = document.querySelector('.modal-contenedor');
// initRegistroAlumnoForm(modalRaiz);
