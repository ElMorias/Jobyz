window.addEventListener('DOMContentLoaded', function() {
    modificarAlumno();
});

function modificarAlumno(){

    const form = document.getElementById('form-editar-alumno');

    // Modificar
    // lo de yo=1 es porquue e modifiar del admin le paso el id del la empresa, pero aqui no
    // asi que de default me manda la lista de todos los alumnos, si pongo yo=1 en el get puedo distinguir
    fetch('api/apiAlumno.php?yo=1')
        .then(res => res.json())
        .then(alumno => {
        document.getElementById('perfil-id').value = alumno.id || '';
        document.getElementById('perfil-correo').value = alumno.correo || '';
        document.getElementById('perfil-contrasena').value = '';
        document.getElementById('perfil-nombre').value = alumno.nombre || '';
        document.getElementById('perfil-apellido1').value = alumno.apellido1 || '';
        document.getElementById('perfil-apellido2').value = alumno.apellido2 || '';
        document.getElementById('perfil-fnacimiento').value = alumno.fnacimiento || '';
        document.getElementById('perfil-dni').value = alumno.dni || '';
        document.getElementById('perfil-direccion').value = alumno.direccion || '';
        document.getElementById('perfil-telefono').value = alumno.telefono || '';

        // Foto actual
        document.getElementById('preview-foto').innerHTML = alumno.foto
            ? `<img src="${alumno.foto}" style="max-width:150px;">`
            : '';

        let enlaceCurriculum = document.getElementById('curriculum-link');
        if (alumno.curriculum) {
            enlaceCurriculum.href = alumno.curriculum;
            enlaceCurriculum.style.display = 'inline';
        } else {
            enlaceCurriculum.href = '#';
            enlaceCurriculum.style.display = 'none';
        }

        pintarEstudiosGuardados(alumno.estudios);

    });

    function pintarEstudiosGuardados(estudios) {
        const wrapper = document.getElementById('estudios-guardados');
        wrapper.innerHTML = ''; // Limpia por si acaso
        if (!estudios || estudios.length === 0) {
            wrapper.innerHTML = '<p>No tienes estudios registrados.</p>';
            return;
        }
        estudios.forEach(est => {
            const div = document.createElement('div');
            div.className = 'estudio-guardado';
            div.innerHTML = `
            <span><b>Familia:</b> ${est.familia_nombre}</span> |
            <span><b>Ciclo:</b> ${est.ciclo_nombre}</span> |
            <span><b>Inicio:</b> ${est.fechainicio}</span> |
            <span><b>Fin:</b> ${est.fechafin || 'Sin finalizar'}</span>
            <button type="button" data-id="${est.id}" class="btn-borrar-estudio">Borrar</button>
            `;
            wrapper.appendChild(div);
        });
    }

    const estudiosGuardadosWrapper = document.getElementById('estudios-guardados');

    // Escucha clicks en el bloque de estudios guardados
        estudiosGuardadosWrapper.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-borrar-estudio')) {
            const estudioId = e.target.getAttribute('data-id');
            // Llama a tu API/endpoint para borrar ese estudio
            fetch('api/apiEstudio.php', {
            method: 'DELETE',
            body: JSON.stringify({ id: estudioId })
            })
            .then(r => r.json())
            .then(resp => {
            if (resp.status === 'ok') {
                // Recarga el perfil
                modificarAlumno();
            } else {
                alert('No se pudo borrar ese estudio');
            }
            });
        }
    });


    // Preview de nueva foto
    form.foto.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function (evt) {
                document.getElementById('preview-foto').innerHTML = `<img src="${evt.target.result}" style="max-width:150px;">`;
            };
            reader.readAsDataURL(file);
        }
    });

    // Guardar cambios
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        let data = new FormData(form);
        fetch('api/apiAlumno.php', {
            method: 'POST',
            body: data
        })
        .then(res => res.json())
        .then(resp => {
            if(resp.status === "ok") {
                // O actualizar la UI, recargar datos, etc
                modificarAlumno(); // por ejemplo, recarga los datos desde AJAX
            } else {
                alert("Error: " + resp.mensaje);
            }
        });
    });

   // Botón cerrar
    const cerrarBtn = document.getElementById('cerrar');
        if (cerrarBtn) {
            cerrarBtn.onclick = function () {
                window.location.href = 'index.php?page=landing'; 
        };
    }

    const btnTomarFoto = document.getElementById('tomarFotoBtn');
    let streamActivo = null;

    if (btnTomarFoto) {
    btnTomarFoto.addEventListener('click', () => {
        // Abre el modal de captura
        modalManager.crearModalDesdeUrl('assets/modales/modalCaptura.txt', () => {
        const video = document.getElementById('videoFoto');
        iniciarCamara(video);
        document.getElementById('capturarFotoBtn').onclick = capturarFoto;
        document.getElementById('cancelarFotoBtn').onclick = cerrarModalCamara;
        });
    });
    }

    function iniciarCamara(video) {
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
        streamActivo = stream;
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
    document.getElementById('preview-foto').innerHTML = `<img src="${dataUrl}" style="width:110px">`;
    dataURLtoFileInputJobyz(dataUrl, 'foto-captura.png', document.getElementById('fotoFile'));
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


    // Listener para preview tradicional de selección de archivo
    const inputFileFoto = document.getElementById('fotoFile');
    const previewFoto = document.getElementById('preview-foto');
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

    // Botón salir o cancelar
    const salir = document.getElementById('btn-salir');
    if (salir){
    salir.addEventListener('click', function(e){
        window.location.href= 'index.php?page=landing';
    });
    }

      // ---- Estudios ----
    const estudiosWrapper = document.querySelector('#estudios-wrapper');
    const btnAgregarEstudio = document.querySelector('#btn-agregar-estudio');
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

}