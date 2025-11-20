const token = sessionStorage.getItem("token");
const user_id = sessionStorage.getItem("user_id");

// Estado global independiente para cada tabla
let listaAlumnos = [];
let alumnosFiltrados = [];
let paginaActual = 1;

let listaNoValidados = [];
let alumnosNoValFiltrados = [];
let paginaActualNoVal = 1;

const alumnosPorPagina = 10;
let ordenAsc = [true, true, true];
let ordenAscNoVal = [true, true, true];


// --- LISTENERS POR CONTENEDOR --- //
function registrarListeners() {
    // Tabla principal (validados)
    const contAlumnos = document.getElementById('contenedor-alumnos');
    if (contAlumnos) {
        // Acciones sobre filas (delegación para la propia tabla principal)
        contAlumnos.addEventListener('click', function (e) {
            const fila = e.target.closest('tr');
            if (!fila) return;
            const id = fila.getAttribute('data-id');

            // Borrar
            if (e.target.classList.contains('btn-borrar')) {
                mostrarModalBorrado(id, fila);
            }

            // Detalles
            if (e.target.classList.contains('btn-detalles')) {
                mostrarModalDetalles(id);
            }

            // Modificar
            if (e.target.classList.contains('btn-modificar')) {
                mostrarModalModificar(id);
            }
        });
    }

    // Tabla no validados
    const contNoVal = document.getElementById('contenedor-alumnos-noval');
    if (contNoVal) {
        // Acciones sobre filas (delegación para la tabla no validados)
        contNoVal.addEventListener('click', function (e) {
            const fila = e.target.closest('tr');
            if (!fila) return;
            const id = fila.getAttribute('data-id');

            // Notificar
            if (e.target.classList.contains('btn-notificar')) {
                fetch('api/apiAlumno.php', {
                    method: 'POST',
                    headers: {
                        Authorization: "Bearer " + token,
                        "X-USER-ID": user_id,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ accion: 'notificar_no_validado', id })
                }).then(res => res.json())
                .then(data => {
                    if (!data.ok) alert("Error enviando el correo.");
                });
            }

            // Borrar
            if (e.target.classList.contains('btn-borrar')) {
                mostrarModalBorrado(id, fila);
            }
        });
    }

    // Borrar estudio desde modal-edición perfil (si usas bloques de estudios)
    document.body.addEventListener('click', function(e){
        if (!e.target.classList.contains('btn-borrar-estudio')) return;
        const estudioId = e.target.getAttribute('data-id');
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
                modalManager.cerrarModal();
                recargarAlumnos();
            } else {
                alert('No se pudo borrar ese estudio');
            }
        });
    });

    // Buscar alumnos (principal)
    const buscador = document.getElementById('buscador-alumnos');
    if (buscador) {
        buscador.addEventListener('input', function () {
            const texto = this.value.trim().toLowerCase();
            alumnosFiltrados = listaAlumnos.filter(u => {
                const nombreCompleto = ((u.nombre || '') + ' ' + (u.apellido1 || '') + ' ' + (u.apellido2 || '')).toLowerCase();
                const correo = (u.correo || '').toLowerCase();
                return nombreCompleto.includes(texto) || correo.includes(texto);
            });
            paginaActual = 1;
            renderAlumnos(alumnosFiltrados);
        });
    }

    // Botones globales de add, exportar, carga masiva...
    const btnAdd = document.getElementById('addUsuario');
    if (btnAdd) {
        btnAdd.addEventListener('click', function () {
            modalManager.crearModalDesdeUrl('assets/modales/modalRegistroAlumno.txt', function () {
                let modalRaiz = document.querySelector('.modal-contenedor');
                initRegistroAlumnoForm(modalRaiz);
            });
        });
    }

    const btnExport = document.getElementById('exportarAlumnosBtn');
    if (btnExport) {
        btnExport.addEventListener('click', function () {
            window.open('?page=exportar_alumno_pdf', '_blank');
        });
    }

    const btnCargaMasiva = document.getElementById('addMasivo');
    if (btnCargaMasiva) {
        btnCargaMasiva.addEventListener('click', function () {
            modalManager.crearModalDesdeUrl('assets/modales/modalCargaMas.txt', function () {
                let modalRaiz = document.querySelector('.modal-contenedor');
                initCargaMasiva(modalRaiz);
            });
        });
    }
}

// --- FUNCIONES MODALES --- //
function mostrarModalBorrado(id, filaHTML) {
    modalManager.crearModalDesdeUrl('assets/modales/modalborrar.txt', function () {
        let btnConfirmar = document.getElementById('confirmar');
        let btnCancelar = document.getElementById('cancelar');
        btnConfirmar.onclick = function () {
            fetch('api/apiAlumno.php', {
                method: 'DELETE',
                headers: {
                    Authorization: "Bearer " + token,
                    "X-USER-ID": user_id,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id })
            }).then(res => res.json())
            .then(resp => {
                if (resp.status === "ok") {
                    if(filaHTML) filaHTML.remove();
                    modalManager.cerrarModal();
                } else {
                    alert("Error: " + resp.mensaje);
                }
            });
        };
        btnCancelar.onclick = function () {
            modalManager.cerrarModal();
        };
    });
}

function mostrarModalDetalles(id) {
    modalManager.crearModalDesdeUrl('assets/modales/modalDetalles.txt', function () {
        fetch('api/apiAlumno.php?id=' + id, {
            headers: {
                Authorization: "Bearer " + token,
                "X-USER-ID": user_id
            }
        })
        .then(res => res.json())
        .then(alumno => {
            document.getElementById('modal-correo').value = alumno.correo || '';
            document.getElementById('modal-nombre').value = alumno.nombre || '';
            document.getElementById('modal-apellido1').value = alumno.apellido1 || '';
            document.getElementById('modal-apellido2').value = alumno.apellido2 || '';
            document.getElementById('modal-fnacimiento').value = alumno.fnacimiento || '';
            document.getElementById('modal-dni').value = alumno.dni || '';
            document.getElementById('modal-telefono').value = alumno.telefono || '';
            document.getElementById('modal-direccion').value = alumno.direccion || '';
            document.getElementById('detalle-foto').src = alumno.foto || 'assets/Images/default.png';
            let curriculumContainer = document.getElementById('modal-curriculum');
            if (curriculumContainer) {
                if (alumno.curriculum) {
                    curriculumContainer.innerHTML = `<a href="${alumno.curriculum}" target="_blank">Ver Curriculum</a>`;
                } else {
                    curriculumContainer.innerHTML = 'No disponible';
                }
            }
        });
    });
}

function mostrarModalModificar(id) {
    modalManager.crearModalDesdeUrl('assets/modales/modalModificar.txt', function () {
        let modalRaiz = document.querySelector('.modal-contenedor');
        initRegistroAlumnoForm(modalRaiz);
        let form = modalRaiz.querySelector('form');
        let modalErrores = modalRaiz.querySelector('#modal-errores');
        fetch('api/apiAlumno.php?id=' + id, {
            headers: {
                Authorization: "Bearer " + token,
                "X-USER-ID": user_id
            }
        })
            .then(res => res.json())
            .then(alumno => {
                document.getElementById('modal-id').value = alumno.id || '';
                document.getElementById('modal-correo').value = alumno.correo || '';
                document.getElementById('modal-contrasena').value = '';
                document.getElementById('repetir-contrasena').value = '';
                document.getElementById('modal-nombre').value = alumno.nombre || '';
                document.getElementById('modal-apellido1').value = alumno.apellido1 || '';
                document.getElementById('modal-apellido2').value = alumno.apellido2 || '';
                document.getElementById('modal-fnacimiento').value = alumno.fnacimiento || '';
                document.getElementById('modal-dni').value = alumno.dni || '';
                document.getElementById('modal-direccion').value = alumno.direccion || '';
                document.getElementById('modal-telefono').value = alumno.telefono || '';
                document.getElementById('preview-foto').innerHTML = alumno.foto
                    ? `<img src="${alumno.foto}">` : '';
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

        // Evento del formulario del modal para modificar el alumno
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (modalErrores) modalErrores.innerHTML = '';
            let errores = [];

            // Recoge los valores del formulario
            const nombre = form['nombre'].value.trim();
            const apellido1 = form['apellido1'].value.trim();
            const apellido2 = form['apellido2'].value.trim();
            const fnacimiento = form['fnacimiento'].value;
            const dni = form['dni'].value.trim();
            const telefono = form['telefono'].value.trim();
            const direccion = form['direccion'].value.trim();
            const contrasena1 = form['contrasena'].value;
            const contrasena2 = form['repetir-contrasena'].value;

            // Validaciones básicas
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
                errores.push('El DNI debe tener formato 12345678A (Letra Mayuscula).');
            }
            if (!/^\d{9}$/.test(telefono)) {
                errores.push('El teléfono debe tener 9 dígitos.');
            }
            if (!direccion || direccion.length > 80) {
                errores.push('La dirección es obligatoria y de máximo 80 caracteres.');
            }
            if (contrasena1 && (contrasena1.length < 6 || contrasena1.length > 60)) {
                errores.push('La nueva contraseña debe tener entre 6 y 60 caracteres.');
            }
            if (contrasena1 !== contrasena2) {
                errores.push('Las contraseñas no coinciden.');
            }

            // Mostrar errores si existe
            if (errores.length > 0) {
                const ul = document.createElement('ul');
                errores.forEach(msg => {
                    const li = document.createElement('li');
                    li.textContent = msg;
                    ul.appendChild(li);
                });
                modalErrores.innerHTML = '';
                modalErrores.appendChild(ul);
                return;
            }

            //enviar datos a api
            let formData = new FormData(form);

            fetch('api/apiAlumno.php', {
                method: 'POST',
                headers: {
                    Authorization: "Bearer " + token,
                    "X-USER-ID": user_id
                },
                body: formData
            })
                .then(r => r.json())
                .then(resp => {
                    if (resp.status === 'ok') {
                        modalErrores.innerHTML = '<span style="color:green;">Alumno actualizado correctamente</span>';
                        setTimeout(() => modalManager.cerrarModal(), 1200);
                        recargarAlumnos(); // Recargar la tabla
                    } else if (Array.isArray(resp.errores)) {
                        const ul = document.createElement('ul');
                        resp.errores.forEach(msg => {
                            const li = document.createElement('li');
                            li.textContent = msg;
                            ul.appendChild(li);
                        });
                        modalErrores.innerHTML = '';
                        modalErrores.appendChild(ul);
                    } else if (resp.mensaje) {
                        modalErrores.innerHTML = '<span>Error: ' + resp.mensaje + '</span>';
                    } else {
                        modalErrores.innerHTML = '<span>Error desconocido</span>';
                    }
                });
        });


        const btnCerrar = document.getElementById('cerrar');
        if (btnCerrar) {
            btnCerrar.onclick = function () {
                modalManager.cerrarModal();
            };
        }
    });
}

// --- RECARGA Y ESTADO DE TABLAS --- //
function recargarAlumnos() {
    fetch('api/apiAlumno.php', {
        headers: {
            Authorization: "Bearer " + token,
            "X-USER-ID": user_id
        }
    })
    .then(res => res.json())
    .then(datos => {
        listaAlumnos = datos.alumnos || [];
        alumnosFiltrados = [...listaAlumnos];
        paginaActual = 1;
        listaNoValidados = datos.noValidados || [];
        alumnosNoValFiltrados = [...listaNoValidados];
        paginaActualNoVal = 1;
        renderAlumnos(alumnosFiltrados);
        renderAlumnosNoValidados(alumnosNoValFiltrados);
    })
    .catch(() => {
        listaAlumnos = [];
        renderAlumnos([]);
        renderAlumnosNoValidados([]);
    });
}

// --- RENDER DE TABLAS: UNO PARA CADA ESTADO ---
function renderAlumnos(usuarios) {
    const cont = document.getElementById('contenedor-alumnos');
    cont.innerHTML = '';
    const totalPaginas = Math.ceil(usuarios.length / alumnosPorPagina) || 1;
    if (paginaActual > totalPaginas) paginaActual = totalPaginas;
    if (paginaActual < 1) paginaActual = 1;
    const desde = (paginaActual - 1) * alumnosPorPagina;
    const hasta = paginaActual * alumnosPorPagina;
    const alumnosPagina = usuarios.slice(desde, hasta);

    let html = `
        <table id="tabla-alumnos" class="tablaUsuarios">
        <thead>
          <tr>
            <th>ID <span class="ordenar-todos" data-col="0">⇅</span></th>
            <th>Nombre completo <span class="ordenar-todos" data-col="1">⇅</span></th>
            <th>Email <span class="ordenar-todos" data-col="2">⇅</span></th>
            <th>Teléfono</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
    `;
    alumnosPagina.forEach(usuario => {
        html += `
          <tr data-id="${usuario.id}">
            <td>${usuario.id}</td>
            <td>${usuario.nombre} ${usuario.apellido1} ${usuario.apellido2 || ''}</td>
            <td>${usuario.correo}</td>
            <td>${usuario.telefono}</td>
            <td>
                <button class="btn-tabla btn-detalles">Detalles</button>
                <button class="btn-tabla btn-modificar">Modificar</button>
                <button class="btn-tabla btn-borrar">Borrar</button>
            </td>
          </tr>
        `;
    });
    html += `</tbody></table>
      <div class="tabla-paginacion">
        <button id="btn-previa-todos" ${paginaActual === 1 ? 'disabled' : ''}>Anterior</button>
        <span> Página ${paginaActual} de ${totalPaginas} </span>
        <button id="btn-siguiente-todos" ${paginaActual === totalPaginas ? 'disabled' : ''}>Siguiente</button>
      </div>
    `;
    if (!usuarios.length)
        html += `<p class="ofertas-vacio">No hay alumnos registrados.</p>`;
    cont.innerHTML = html;

    cont.querySelectorAll('.ordenar-todos').forEach(btn => {
        const colIdx = Number(btn.getAttribute('data-col'));
        btn.onclick = () => {
            ordenarAlumnos(colIdx, ordenAsc[colIdx], false);
            ordenAsc[colIdx] = !ordenAsc[colIdx];
        };
    });

    document.getElementById('btn-previa-todos').onclick = function () {
        if (paginaActual > 1) {
            paginaActual--;
            renderAlumnos(alumnosFiltrados);
        }
    };
    document.getElementById('btn-siguiente-todos').onclick = function () {
        const totalPaginas = Math.ceil(alumnosFiltrados.length / alumnosPorPagina) || 1;
        if (paginaActual < totalPaginas) {
            paginaActual++;
            renderAlumnos(alumnosFiltrados);
        }
    };
}

function renderAlumnosNoValidados(usuarios) {
    const cont = document.getElementById('contenedor-alumnos-noval');
    cont.innerHTML = '';
    const totalPaginas = Math.ceil(usuarios.length / alumnosPorPagina) || 1;
    if (paginaActualNoVal > totalPaginas) paginaActualNoVal = totalPaginas;
    if (paginaActualNoVal < 1) paginaActualNoVal = 1;
    const desde = (paginaActualNoVal - 1) * alumnosPorPagina;
    const hasta = paginaActualNoVal * alumnosPorPagina;
    const alumnosPagina = usuarios.slice(desde, hasta);

    let html = `
        <table id="tabla-no-validados" class="tablaUsuarios">
        <thead>
          <tr>
            <th>ID <span class="ordenar-noval" data-col="0">⇅</span></th>
            <th>Nombre completo <span class="ordenar-noval" data-col="1">⇅</span></th>
            <th>Email <span class="ordenar-noval" data-col="2">⇅</span></th>
            <th>Teléfono</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
    `;
    alumnosPagina.forEach(usuario => {
        html += `
          <tr data-id="${usuario.id}">
            <td>${usuario.id}</td>
            <td>${usuario.nombre} ${usuario.apellido1} ${usuario.apellido2 || ''}</td>
            <td>${usuario.correo}</td>
            <td>${usuario.telefono}</td>
            <td>
                <button  class="btn-tabla btn-notificar">Notificar</button>
                <button class="btn-tabla btn-borrar">Borrar</button>
            </td>
          </tr>
        `;
    });
    html += `</tbody></table>
      <div class="tabla-paginacion">
        <button id="btn-previa-noval" ${paginaActualNoVal === 1 ? 'disabled' : ''}>Anterior</button>
        <span> Página ${paginaActualNoVal} de ${totalPaginas} </span>
        <button id="btn-siguiente-noval" ${paginaActualNoVal === totalPaginas ? 'disabled' : ''}>Siguiente</button>
      </div>
    `;
    if (!usuarios.length)
        html += `<p class="ofertas-vacio">No hay alumnos registrados.</p>`;
    cont.innerHTML = html;

    cont.querySelectorAll('.ordenar-noval').forEach(btn => {
        const colIdx = Number(btn.getAttribute('data-col'));
        btn.onclick = () => {
            ordenarAlumnos(colIdx, ordenAscNoVal[colIdx], true);
            ordenAscNoVal[colIdx] = !ordenAscNoVal[colIdx];
        };
    });

    document.getElementById('btn-previa-noval').onclick = function () {
        if (paginaActualNoVal > 1) {
            paginaActualNoVal--;
            renderAlumnosNoValidados(alumnosNoValFiltrados);
        }
    };
    document.getElementById('btn-siguiente-noval').onclick = function () {
        const totalPaginas = Math.ceil(alumnosNoValFiltrados.length / alumnosPorPagina) || 1;
        if (paginaActualNoVal < totalPaginas) {
            paginaActualNoVal++;
            renderAlumnosNoValidados(alumnosNoValFiltrados);
        }
    };
}

// Esta función ordena el array fuente y vuelve a renderizar la tabla desde la página 1.
function ordenarAlumnos(columna, ascendente, esNoValidados = false) {
    // Elige la lista global a usar
    let lista = esNoValidados ? alumnosNoValFiltrados : alumnosFiltrados;

    lista.sort((a, b) => {
        let valA, valB;
        switch (columna) {
            case 0: // ID (numérico)
                valA = Number(a.id);
                valB = Number(b.id);
                break;
            case 1: // Nombre completo (texto)
                valA = (a.nombre + ' ' + a.apellido1 + ' ' + (a.apellido2 || '')).toLowerCase();
                valB = (b.nombre + ' ' + b.apellido1 + ' ' + (b.apellido2 || '')).toLowerCase();
                break;
            case 2: // Email (texto)
                valA = (a.correo || '').toLowerCase();
                valB = (b.correo || '').toLowerCase();
                break;
            default:
                valA = '';
                valB = '';
        }
        if (valA < valB) return ascendente ? -1 : 1;
        if (valA > valB) return ascendente ? 1 : -1;
        return 0;
    });

    if (esNoValidados) {
        paginaActualNoVal = 1;
        renderAlumnosNoValidados(lista);
    } else {
        paginaActual = 1;
        renderAlumnos(lista);
    }
}

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

document.addEventListener('DOMContentLoaded', function () {
    recargarAlumnos();
    registrarListeners();
});
