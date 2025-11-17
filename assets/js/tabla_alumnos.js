let listaAlumnos = []; // global apra la busqueda dinamica
let alumnosFiltrados = [];
let paginaActual = 1;
const alumnosPorPagina = 10;

document.addEventListener('DOMContentLoaded', function () {

    recargarAlumnos();


    // Añadir alumno
    const btnAdd = document.getElementById('addUsuario');
    if (btnAdd) {
        btnAdd.addEventListener('click', function () {
            modalManager.crearModalDesdeUrl('assets/modales/modalRegistroAlumno.txt', function () {
                let modalRaiz = document.querySelector('.modal-contenedor');
                initRegistroAlumnoForm(modalRaiz);
            });
        });
    }


    // boton de generar el pdf
    document.getElementById('exportarAlumnosBtn').addEventListener('click', function () {
        window.open('?page=exportar_alumno_pdf', '_blank');
    });

    // Carga masiva
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

function recargarAlumnos() {
    fetch('api/apiAlumno.php')
        .then(res => res.json())
        .then(usuarios => {
            if (!Array.isArray(usuarios)) usuarios = [];
            listaAlumnos = usuarios;
            alumnosFiltrados = [...listaAlumnos];
            paginaActual = 1;
            renderAlumnos(alumnosFiltrados);
        })
        .catch(err => {
            listaAlumnos = [];
            renderAlumnos([]);
        });
}


// busqueda dinamica //
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

function renderAlumnos(usuarios) {
    const cont = document.getElementById('contenedor-alumnos');
    cont.innerHTML = '';

    // ---- PAGINACIÓN ----
    if (typeof alumnosFiltrados === 'undefined' || alumnosFiltrados.length === 0) alumnosFiltrados = usuarios;
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
                    <th>ID <span id="boton-ordenar-id" class="boton-ordenar">⇅</span></th>
                    <th>Nombre completo <span id="boton-ordenar-nombre" class="boton-ordenar">⇅</span></th>
                    <th>Email <span id="boton-ordenar-correo" class="boton-ordenar">⇅</span></th>
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
    html += '</tbody></table>';

    // ---- PAGINACIÓN BOTONES ----
    html += `<div class="tabla-paginacion">`;
    html += `<button id="btn-previa" ${paginaActual === 1 ? 'disabled' : ''}>Anterior</button>`;
    html += `<span> Página ${paginaActual} de ${totalPaginas} </span>`;
    html += `<button id="btn-siguiente" ${paginaActual === totalPaginas ? 'disabled' : ''}>Siguiente</button>`;
    html += `</div>`;

    if (!usuarios.length)
        html += `<p class="ofertas-vacio">No hay alumnos registrados.</p>`;

    cont.innerHTML = html;

    // --- Ordenar filas  --- //
    let ordenIdAsc = true;
    let ordenNombreAsc = true;
    let ordenCorreoAsc = true;

    const tabla = document.getElementById('tabla-alumnos');

    document.getElementById('boton-ordenar-id').onclick = function () {
        ordenarPorColumna(tabla, 0, ordenIdAsc);
        ordenIdAsc = !ordenIdAsc;
    };
    document.getElementById('boton-ordenar-nombre').onclick = function () {
        ordenarPorColumna(tabla, 1, ordenNombreAsc);
        ordenNombreAsc = !ordenNombreAsc;
    };
    document.getElementById('boton-ordenar-correo').onclick = function () {
        ordenarPorColumna(tabla, 2, ordenCorreoAsc);
        ordenCorreoAsc = !ordenCorreoAsc;
    };

    addAlumnoListeners();

    // ---- Listeners de paginación ----
    document.getElementById('btn-previa').onclick = function () {
        if (paginaActual > 1) {
            paginaActual--;
            renderAlumnos(usuarios);
        }
    };
    document.getElementById('btn-siguiente').onclick = function () {
        if (paginaActual < totalPaginas) {
            paginaActual++;
            renderAlumnos(usuarios);
        }
    };
}


// Función genérica de ordenación
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

function addAlumnoListeners() {
    // Borrar
    document.querySelectorAll('.btn-borrar').forEach(btn => {
        btn.onclick = function () {
            const id = this.closest('tr').dataset.id;
            modalManager.crearModalDesdeUrl('assets/modales/modalborrar.txt', function () {
                let btnConfirmar = document.getElementById('confirmar');
                let btnCancelar = document.getElementById('cancelar');
                btnConfirmar.onclick = function () {
                    fetch('api/apiAlumno.php', {
                        method: 'DELETE',
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
        };
    });

    // Detalles
    document.querySelectorAll('.btn-detalles').forEach(btn => {
        btn.onclick = function () {
            const id = this.closest('tr').dataset.id;
            modalManager.crearModalDesdeUrl('assets/modales/modalDetalles.txt', function () {
                fetch('api/apiAlumno.php?id=' + id)
                    .then(res => res.json())
                    .then(alumno => {
                        document.getElementById('modal-correo').value = alumno.correo || '';
                        document.getElementById('modal-nombre').value = alumno.nombre || '';
                        document.getElementById('modal-apellido1').value = alumno.apellido1 || '';
                        document.getElementById('modal-apellido2').value = alumno.apellido2 || '';
                        document.getElementById('modal-fnacimiento').value = alumno.fnacimiento || '';
                        document.getElementById('modal-curriculum').value = alumno.curriculum || '';
                        document.getElementById('modal-dni').value = alumno.dni || '';
                        document.getElementById('modal-telefono').value = alumno.telefono || '';
                        document.getElementById('modal-direccion').value = alumno.direccion || '';
                        document.getElementById('detalle-foto').src = alumno.foto || 'assets/Images/default.png';
                    });
            });
        };
    });

    // Modificar
    document.querySelectorAll('.btn-modificar').forEach(btn => {
        btn.onclick = function () {
            const id = this.closest('tr').dataset.id;
            modalManager.crearModalDesdeUrl('assets/modales/modalModificar.txt', function () {
                let modalRaiz = document.querySelector('.modal-contenedor');
                initRegistroAlumnoForm(modalRaiz);
                let form = modalRaiz.querySelector('form');
                // Carga datos actuales
                fetch('api/apiAlumno.php?id=' + id)
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
                modalErrores = modalRaiz.querySelector('#modal-errores');

                if (form) {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        let errores = [];
                        if (modalErrores) modalErrores.innerHTML = '';

                        // Validaciones
                        const nombre = form['modal-nombre'].value.trim();
                        const apellido1 = form['modal-apellido1'].value.trim();
                        const apellido2 = form['modal-apellido2'].value.trim();
                        const fnacimiento = form['modal-fnacimiento'].value;
                        const dni = form['modal-dni'].value.trim();
                        const telefono = form['modal-telefono'].value.trim();
                        const direccion = form['modal-direccion'].value.trim();
                        const contrasena = form['modal-contrasena'] ? form['modal-contrasena'].value : '';

                        // Nombre y apellidos
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
                        if (!/^\d{8}[A-Za-z]$/.test(dni)) {
                            errores.push('El DNI debe tener formato 12345678A.');
                        }
                        if (!/^\d{9}$/.test(telefono)) {
                            errores.push('El teléfono debe tener 9 dígitos.');
                        }
                        if (!direccion || direccion.length > 80) {
                            errores.push('La dirección es obligatoria y de máximo 80 caracteres.');
                        }
                        // Validación de contraseña SOLO si el campo está habilitado
                        if (!form['modal-contrasena'].disabled && contrasena) {
                            if (contrasena.length < 6 || contrasena.length > 60) {
                                errores.push('La nueva contraseña debe tener entre 6 y 60 caracteres.');
                            }
                        }

                        // Mostrar errores si existen
                        if (errores.length > 0) {
                            if (modalErrores) {
                                const ul = document.createElement('ul');
                                errores.forEach(msg => {
                                    const li = document.createElement('li');
                                    li.textContent = msg;
                                    ul.appendChild(li);
                                });
                                modalErrores.appendChild(ul);
                            }
                            return;
                        }

                        // Si no hay errores, enviar AJAX normalmente
                        let data = new FormData(form);
                        fetch('api/apiAlumno.php', {
                            method: 'POST',
                            body: data
                        })
                            .then(res => res.json())
                            .then(resp => {
                                if (resp.status === 'ok') {
                                    if (modalErrores) {
                                        modalErrores.innerHTML = '<span style="color:green;">Datos guardados correctamente.</span>';
                                    }
                                    recargarAlumnos();
                                    setTimeout(() => { modalManager.cerrarModal(); }, 800);
                                } else if (Array.isArray(resp.errores)) {
                                    if (modalErrores) {
                                        const ul = document.createElement('ul');
                                        resp.errores.forEach(msg => {
                                            const li = document.createElement('li');
                                            li.textContent = msg;
                                            ul.appendChild(li);
                                        });
                                        modalErrores.innerHTML = '';
                                        modalErrores.appendChild(ul);
                                    }
                                } else if (resp.mensaje) {
                                    if (modalErrores) {
                                        modalErrores.innerHTML = '<span>Error: ' + resp.mensaje + '</span>';
                                    }
                                } else {
                                    if (modalErrores) {
                                        modalErrores.innerHTML = '<span>Error desconocido</span>';
                                    }
                                }
                            });
                    });

                    // Botón cerrar
                    const btnCerrar = document.getElementById('cerrar');
                    if (btnCerrar) {
                        btnCerrar.onclick = function () {
                            modalManager.cerrarModal();
                        };
                    }
                }
            });
        };
    });
}
