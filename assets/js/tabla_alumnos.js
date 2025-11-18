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

// Listener global para los botones (notificar, borrar, etc.)
document.addEventListener('click', function (e) {
    // Notificar no validados
    if (e.target.classList.contains('btn-notificar')) {
        const fila = e.target.closest('tr');
        const alumnoId = fila.getAttribute('data-id');
        if (!alumnoId) return;
        fetch('api/apiAlumno.php', {
            method: 'POST',
            headers: {
                Authorization: "Bearer " + token,
                "X-USER-ID": user_id,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ accion: 'notificar_no_validado', id: alumnoId })
        }).then(res => res.json())
        .then(data => {
            if (!data.ok) {
                alert("Error enviando el correo.");
            }
        });
    }

    // Borrar (vale para ambas tablas)
    if (e.target.classList.contains('btn-borrar')) {
        const id = e.target.closest('tr').getAttribute('data-id');
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
                        recargarAlumnos();
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

    // Detalles
    if (e.target.classList.contains('btn-detalles')) {
        const id = e.target.closest('tr').getAttribute('data-id');
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
            })
        });
    }

    // Modificar
    if (e.target.classList.contains('btn-modificar')) {
        const id = e.target.closest('tr').getAttribute('data-id');
        modalManager.crearModalDesdeUrl('assets/modales/modalModificar.txt', function () {
            let modalRaiz = document.querySelector('.modal-contenedor');
            initRegistroAlumnoForm(modalRaiz);
            let form = modalRaiz.querySelector('form');
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
                document.getElementById('modal-contrasena').value = alumno.contrasena || '';
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

            let modalErrores = modalRaiz.querySelector('#modal-errores');
            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    let errores = [];
                    if (modalErrores) modalErrores.innerHTML = '';
                    // ... validaciones ...
                    // Mostrar errores o enviar AJAX...
                });
            }

            const btnCerrar = document.getElementById('cerrar');
            if (btnCerrar) {
                btnCerrar.onclick = function () {
                    modalManager.cerrarModal();
                };
            }
        });
    }
});

// DOMContentLoaded para los botones superiores/acciones globales
document.addEventListener('DOMContentLoaded', function () {
    recargarAlumnos();

    const btnAdd = document.getElementById('addUsuario');
    if (btnAdd) {
        btnAdd.addEventListener('click', function () {
            modalManager.crearModalDesdeUrl('assets/modales/modalRegistroAlumno.txt', function () {
                let modalRaiz = document.querySelector('.modal-contenedor');
                initRegistroAlumnoForm(modalRaiz);
            });
        });
    }

    document.getElementById('exportarAlumnosBtn').addEventListener('click', function () {
        window.open('?page=exportar_alumno_pdf', '_blank');
    });

    const btnCargaMasiva = document.getElementById('addMasivo');
    if (btnCargaMasiva) {
        btnCargaMasiva.addEventListener('click', function () {
            modalManager.crearModalDesdeUrl('assets/modales/modalCargaMas.txt', function () {
                let modalRaiz = document.querySelector('.modal-contenedor');
                initCargaMasiva(modalRaiz);
            });
        });
    }
});

// --- RECARGA Y ESTADO DE TABLAS ---
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
    .catch(err => {
        listaAlumnos = [];
        renderAlumnos([]);
        renderAlumnosNoValidados([]);
    });
}

// ---- BÚSQUEDA SÓLO EN TABLA PRINCIPAL ----
document.getElementById('buscador-alumnos').addEventListener('input', function () {
    const texto = this.value.trim().toLowerCase();
    alumnosFiltrados = listaAlumnos.filter(u => {
        const nombreCompleto = ((u.nombre || '') + ' ' + (u.apellido1 || '') + ' ' + (u.apellido2 || '')).toLowerCase();
        const correo = (u.correo || '').toLowerCase();
        return nombreCompleto.includes(texto) || correo.includes(texto);
    });
    paginaActual = 1;
    renderAlumnos(alumnosFiltrados);
});


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

    let ordenAsc = [true, true, true];
    cont.querySelectorAll('.ordenar-todos').forEach(btn => {
        const colIdx = Number(btn.getAttribute('data-col'));
        btn.onclick = () => {
            ordenarPorColumna(cont.querySelector('table'), colIdx, ordenAsc[colIdx]);
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
                <button class="btn-tabla btn-notificar">Notificar</button>
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

    let ordenAsc = [true, true, true];
    cont.querySelectorAll('.ordenar-noval').forEach(btn => {
        const colIdx = Number(btn.getAttribute('data-col'));
        btn.onclick = () => {
            ordenarPorColumna(cont.querySelector('table'), colIdx, ordenAsc[colIdx]);
            ordenAsc[colIdx] = !ordenAsc[colIdx];
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

// Reutilizable para cualquier tabla simple
function ordenarPorColumna(tabla, indiceCol, ascendente) {
    let filas = Array.from(tabla.tBodies[0].rows);
    filas.sort(function (a, b) {
        let valA = a.cells[indiceCol].innerText.trim().toLowerCase();
        let valB = b.cells[indiceCol].innerText.trim().toLowerCase();
        if (ascendente) {
            return valA.localeCompare(valB);
        } else {
            return valB.localeCompare(valA);
        }
    });
    filas.forEach(fila => tabla.tBodies[0].appendChild(fila));
}
