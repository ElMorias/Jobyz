window.addEventListener('load', function () {

 //----------------- cargar los usuarios desde el json al cargar la pagina --------------------------//
    const tbody = document.querySelector('.tablaUsuarios tbody');

    fetch('api/apiAlumno.php')
        .then(res => res.json())
        .then(usuarios => {
            usuarios.forEach(usuario => {
                pintarTabla(usuario);
            });
        })
        .catch(err => {
            console.error('Error al cargar usuarios:', err);
        });

    function pintarTabla(usuario) {
        const fila = document.createElement('tr');
        fila.id = 'fila-' + usuario.id;

        const tdId = document.createElement('td');
        tdId.textContent = usuario.id;
        fila.appendChild(tdId);

        const tdNombre = document.createElement('td');
        tdNombre.textContent = nombreCompleto(usuario);
        fila.appendChild(tdNombre);

        const tdmail = document.createElement('td');
        tdmail.textContent = usuario.correo;
        fila.appendChild(tdmail);

        const tdtel = document.createElement('td');
        tdtel.textContent = usuario.telefono;
        fila.appendChild(tdtel);

        const tdAcciones = document.createElement('td');

        // Botón Detalles
        const btnDetalles = document.createElement('button');
        btnDetalles.textContent = 'Detalles';
        btnDetalles.classList.add('btn-detalles');
        btnDetalles.dataset.id = usuario.id;

        // Botón Modificar
        const btnModificar = document.createElement('button');
        btnModificar.textContent = 'Modificar';
        btnModificar.classList.add('btn-modificar');
        btnModificar.dataset.id = usuario.id;

        // Botón Borrar
        const btnBorrar = document.createElement('button');
        btnBorrar.textContent = 'Borrar';
        btnBorrar.classList.add('btn-borrar');
        btnBorrar.dataset.id = usuario.id;

        // Añadir los botones al td
        tdAcciones.appendChild(btnDetalles);
        tdAcciones.appendChild(btnModificar);
        tdAcciones.appendChild(btnBorrar);

        fila.appendChild(tdAcciones);

        tbody.appendChild(fila);
    }

    function crearFila(usuario) {
        const fila = document.createElement('tr');
        fila.id = 'fila-' + usuario.id;

        const tdId = document.createElement('td');
        tdId.textContent = usuario.id;
        fila.appendChild(tdId);

        const tdNombre = document.createElement('td');
        tdNombre.textContent = nombreCompleto(usuario);
        fila.appendChild(tdNombre);

        const tdmail = document.createElement('td');
        tdmail.textContent = usuario.correo;
        fila.appendChild(tdmail);

        const tdtel = document.createElement('td');
        tdtel.textContent = usuario.telefono;
        fila.appendChild(tdtel);

        const tdAcciones = document.createElement('td');

        // Botón Detalles
        const btnDetalles = document.createElement('button');
        btnDetalles.textContent = 'Detalles';
        btnDetalles.classList.add('btn-detalles');
        btnDetalles.dataset.id = usuario.id;

        // Botón Modificar
        const btnModificar = document.createElement('button');
        btnModificar.textContent = 'Modificar';
        btnModificar.classList.add('btn-modificar');
        btnModificar.dataset.id = usuario.id;

        // Botón Borrar
        const btnBorrar = document.createElement('button');
        btnBorrar.textContent = 'Borrar';
        btnBorrar.classList.add('btn-borrar');
        btnBorrar.dataset.id = usuario.id;

        // Añadir los botones al td
        tdAcciones.appendChild(btnDetalles);
        tdAcciones.appendChild(btnModificar);
        tdAcciones.appendChild(btnBorrar);

        fila.appendChild(tdAcciones);

        return fila;
    }


    function nombreCompleto(usuario) {
        return `${usuario.nombre} ${usuario.apellido1} ${usuario.apellido2 || ''}`.trim();
    }

//----------------- gestionar el modal de borrar usuario --------------------------//

    //borrar los datos de un usuario
    tbody.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-borrar')) {
            const fila = e.target.closest('tr');
            const id = e.target.dataset.id;
            modalManager.crearModalDesdeUrl('assets/modales/modalborrar.txt', function () {

                let btnConfirmar = document.getElementById('confirmar');
                let btnCancelar = document.getElementById('cancelar');

                btnConfirmar.onclick = function () {
                    
                    fetch(`api/apiAlumno.php`, {
                        method: 'DELETE',
                        body: JSON.stringify({ id: id })
                    })
                    .then(res => res.json())
                    .then(resp => {
                        if (resp.status === "ok") {
                            alert(resp.mensaje);
                            fila.remove();
                            modalManager.cerrarModal();
                        } else {
                            alert("Error: " + resp.mensaje);
                        }
                    })
                    .catch(err => {
                        console.error("Fallo en petición AJAX", err);
                        alert("Fallo en petición AJAX");
                    });
                
                };
                
                btnCancelar.onclick = function () {
                    modalManager.cerrarModal();
                };

            });
        }
    });


//----------------gestionar el modal de ver detalles usuario --------------------------//

    tbody.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-detalles')) {
            const id = e.target.dataset.id;
            modalManager.crearModalDesdeUrl('assets/modales/modalDetalles.txt', function () {
                fetch('api/apiAlumno.php?id=' + id, { method: 'GET' })
                    .then(res => res.json())
                    .then(alumno => {
                        console.log(alumno);
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

                    document.getElementById('cerrar').onclick = function () {
                        modalManager.cerrarModal();
                    };

            });
        }
    });

    //----------------gestionar el modal de modificar usuario --------------------------//

   tbody.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-modificar')) {
            const id = e.target.dataset.id;
            modalManager.crearModalDesdeUrl('assets/modales/modalModificar.txt', function () {
                let modalRaiz = document.querySelector('.modal-contenedor');
                initRegistroAlumnoForm(modalRaiz); // Aquí se inicializa todo el JS
                let form = modalRaiz.querySelector('form');

                // Rellenar los campos con datos actuales
                fetch('api/apiAlumno.php?id=' + id, { method: 'GET' })
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

                        // Curriculum actual
                        let enlaceCurriculum = document.getElementById('curriculum-link');
                        if(alumno.curriculum){
                            enlaceCurriculum.href = alumno.curriculum;
                            enlaceCurriculum.style.display = 'inline';
                        } else {
                            enlaceCurriculum.href = '#';
                            enlaceCurriculum.style.display = 'none';
                        }
                    });

                // Preview instantánea al elegir archivo de foto nuevo
                form.foto.addEventListener('change', function(e){
                    const file = e.target.files[0];
                    if(file){
                        let reader = new FileReader();
                        reader.onload = function(evt){
                            document.getElementById('preview-foto').innerHTML =
                                `<img src="${evt.target.result}" style="max-width:150px;">`;
                        };
                        reader.readAsDataURL(file);
                    }
                });

                // Enviar formulario con archivos y datos
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
                            console.log(resp.mensaje);
                            const filaAntigua = document.getElementById('fila-' + resp.alumno.id);
                            if (filaAntigua) {
                                const nuevaFila = crearFila(resp.alumno); // tu función tipo pintarTabla pero devuelve la fila sin añadirla a tbody
                                filaAntigua.parentNode.replaceChild(nuevaFila, filaAntigua);
                            } else {
                                pintarTabla(resp.alumno);
                            }
                            modalManager.cerrarModal();
                        } else {
                            alert("Error: " + resp.mensaje);
                        }
                    })
                    .catch(err => {
                        alert("Fallo en petición AJAX");
                    });
                });

                // Botón cerrar
                document.getElementById('cerrar').onclick = function () {
                    modalManager.cerrarModal();
                };
            });
        }
    });



    //----------------- gestionar el modal de añadir usuario --------------------------//

    const btnadd = document.getElementById('addUsuario');

    btnadd.addEventListener('click', function () {
        modalManager.crearModalDesdeUrl('assets/modales/modalRegistroAlumno.txt', function () {
            let modalRaiz = document.querySelector('.modal-contenedor');
            initRegistroAlumnoForm(modalRaiz); // Aquí se inicializa todo el JS

            let form = modalRaiz.querySelector('form');

            // SOLO aquí añades el submit AJAX
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                let datos = new FormData(form);
                fetch('api/apiAlumno.php', {
                    method: 'POST',
                    body: datos
                })
                .then(res => res.json())
                .then(resp => {
                    if(resp.status === "ok") {
                        alert(resp.mensaje);
                        pintarTabla(resp.alumno);
                        modalManager.cerrarModal();
                    } else {
                        alert("Error: " + resp.mensaje);
                    }
                })
                .catch(err => {
                    alert("Fallo en petición AJAX");
                });
            });
        });
            
    });



});