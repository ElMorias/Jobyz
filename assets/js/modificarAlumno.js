const token = sessionStorage.getItem("token");
const user_id = sessionStorage.getItem("user_id");

window.addEventListener('DOMContentLoaded', function () {
    modificarAlumno();
});

function modificarAlumno() {

    // Modificar
    const form = document.getElementById('form-editar-alumno');


    // lo de yo=1 es porquue e modifiar del admin le paso el id del la empresa, pero aqui no
    // asi que de default me manda la lista de todos los alumnos, si pongo yo=1 en el get puedo distinguir
    fetch('api/apiAlumno.php?yo=1', {
        headers: {
            Authorization: "Bearer " + token,
            "X-USER-ID": user_id
        }
    })
        .then(res => res.json())
        .then(alumno => {
            document.getElementById('perfil-id').value = alumno.id || '';
            document.getElementById('perfil-correo').value = alumno.correo || '';
            document.getElementById('perfil-contrasena').value = alumno.contrasena || '';
            document.getElementById('repetir-contrasena').value = alumno.contrasena || '';
            document.getElementById('perfil-nombre').value = alumno.nombre || '';
            document.getElementById('perfil-apellido1').value = alumno.apellido1 || '';
            document.getElementById('perfil-apellido2').value = alumno.apellido2 || '';
            document.getElementById('perfil-fnacimiento').value = alumno.fnacimiento || '';
            document.getElementById('perfil-dni').value = alumno.dni || '';
            document.getElementById('perfil-direccion').value = alumno.direccion || '';
            document.getElementById('perfil-telefono').value = alumno.telefono || '';

            // Foto actual
            document.getElementById('preview-foto').innerHTML = alumno.foto
                ? `<img src="${alumno.foto}">`: '';

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
    estudiosGuardadosWrapper.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-borrar-estudio')) {
            const estudioId = e.target.getAttribute('data-id');
            // Llamar para borrar ese estudio
            fetch('api/apiEstudio.php', {
                headers: {
                    Authorization: "Bearer " + token,
                    "X-USER-ID": user_id
                },
                method: 'DELETE',
                body: JSON.stringify({ id: estudioId })
            })
                .then(r => r.json())
                .then(resp => {
                    if (resp.status === 'ok') {
                        // Recarga el perfil
                        modificarAlumno();
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
                document.getElementById('preview-foto').innerHTML = `<img src="${evt.target.result}">`;
            };
            reader.readAsDataURL(file);
        }
    });

    // Guardar cambios
    const modificarErrores = document.getElementById('modal-errores');

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            modificarErrores.innerHTML = '';
            let errores = [];

            // Recoge valores
            const nombre = form['nombre'].value.trim();
            const apellido1 = form['apellido1'].value.trim();
            const apellido2 = form['apellido2'].value.trim();
            const fnacimiento = form['fnacimiento'].value;
            const dni = form['dni'].value.trim();
            const telefono = form['telefono'].value.trim();
            const direccion = form['direccion'].value.trim();
            const contrasena1 = form['contrasena'].value;
            const contrasena2 = form['repetir-contrasena'].value;
            // Correo solo lectura, no lo comprobamos aquí

            // Validaciones
            const patronNombre = /^[A-Za-zÁÉÍÓÚáéíóúüÜñÑ ]{2,50}$/;
            if (!patronNombre.test(nombre)) {
                errores.push('El nombre solo puede tener letras y espacios (2-50).');
            }
            if (!patronNombre.test(apellido1)) {
                errores.push('El primer apellido solo puede tener letras y espacios (2-50).');
            }
            if (apellido2 && !patronNombre.test(apellido2)) {
                errores.push('El segundo apellido solo puede tener letras y espacios (2-50).');
            }
            if (!fnacimiento) {
                errores.push('Debe indicar su fecha de nacimiento.');
            }
            if (!/^\d{8}[A-Z]$/.test(dni)) {
                errores.push('El DNI debe tener formato 12345678A(letra mayuscula).');
            }
            if (!/^\d{9}$/.test(telefono)) {
                errores.push('El teléfono debe tener 9 dígitos.');
            }
            if (!direccion || direccion.length > 80) {
                errores.push('La dirección es obligatoria y de máximo 80 caracteres.');
            }
            // Si el usuario escribe una contraseña nueva, debe tener mínimo
            if (contrasena1 && (contrasena1.length < 6 || contrasena1.length > 60)) {
                errores.push('La nueva contraseña debe tener entre 6 y 60 caracteres.');
            }
            if (contrasena1 !== contrasena2) {
                errores.push('Las contraseñas no coinciden.');
            }

            // Mostrar errores si existen
            if (errores.length > 0) {
                const ul = document.createElement('ul');
                errores.forEach(msg => {
                    const li = document.createElement('li');
                    li.textContent = msg;
                    ul.appendChild(li);
                });
                modificarErrores.appendChild(ul);
                return; // No sigue ni hace fetch si hay errores
            }

            // Si todo está ok
            let data = new FormData(form);
            fetch('api/apiAlumno.php', {
                headers: {
                    Authorization: "Bearer " + token,
                    "X-USER-ID": user_id
                },
                method: 'POST',
                body: data
            })
                .then(res => res.json())
                .then(resp => {
                    if (resp.status === 'ok') {
                        modificarErrores.innerHTML = '<span style="color:green;">Perfil actualizado correctamente</span>';
                        modificarAlumno();
                    } else if (Array.isArray(resp.errores)) {
                        // Mostrar lista de errores que trae el back
                        const ul = document.createElement('ul');
                        resp.errores.forEach(msg => {
                            const li = document.createElement('li');
                            li.textContent = msg;
                            ul.appendChild(li);
                        });
                        modificarErrores.innerHTML = '';
                        modificarErrores.appendChild(ul);
                    } else if (resp.mensaje) {
                        // si no da fallos el ok
                        modificarErrores.innerHTML = '<span>Error: ' + resp.mensaje + '</span>';
                    } else {
                        // esto es si estatus no esta ok
                        modificarErrores.innerHTML = '<span>Error desconocido</span>';
                    }
                });
        });
    }


    // Botón cerrar
    const cerrarBtn = document.getElementById('cerrar');
    if (cerrarBtn) {
        cerrarBtn.onclick = function () {
            window.location.href = 'index.php?page=landing';
        };
    }

    //modal para la camara toamr foto
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
        document.getElementById('preview-foto').innerHTML = `<img src="${dataUrl}">`;
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
        // Convierte el dataURL en un objeto Blob (archivo binario)
        fetch(dataUrl)
            .then(res => res.blob())
            .then(blob => {
                // Crea un objeto File usando el blob anterior y el nombre de archivo deseado
                const file = new File([blob], filename, { type: blob.type });

                // Crea un DataTransfer
                const dt = new DataTransfer();

                // Añade el archivo al DataTransfer
                dt.items.add(file);

                // Asigna los archivos (falsos) generados al input file,
                // para que el formulario lo trate como si el usuario los hubiera seleccionado manualmente
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
                    if (previewFoto) previewFoto.innerHTML = `<img src="${e.target.result}">`;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // Botón salir o cancelar
    const salir = document.getElementById('btn-salir');
    if (salir) {
        salir.addEventListener('click', function (e) {
            window.location.href = 'index.php?page=landing';
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
    fetch("api/apiFamilia.php", {
        headers: {
            Authorization: "Bearer " + token,
            "X-USER-ID": user_id
        }
    })
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
                fetch(`api/apiCiclo.php?familia_id=${famId}`, {
                    headers: {
                        Authorization: "Bearer " + token,
                        "X-USER-ID": user_id
                    }
                })
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