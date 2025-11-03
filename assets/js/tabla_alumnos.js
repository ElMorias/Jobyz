window.addEventListener('load', function () {

    // cargar los usuarios desde el json al cargar la pagina
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

        const tdId = document.createElement('td');
        tdId.textContent = usuario.id;
        fila.appendChild(tdId);

        const tdNombre = document.createElement('td');
        tdNombre.textContent = usuario.nombre;
        fila.appendChild(tdNombre);

        const tdape = document.createElement('td');
        tdape.textContent = usuario.apellido1;
        fila.appendChild(tdape);

        const tdmail = document.createElement('td');
        tdmail.textContent = usuario.correo;
        fila.appendChild(tdmail);

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



    //borrar los datos de un usuario
    tbody.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-borrar')) {
            const fila = e.target.closest('tr');
            const id = e.target.dataset.id;

            fetch('mockeos/borrar8.json')
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'ok') {
                        alert(`Detalles del usuario:\nID: ${data.id_borrado}\n ${data.mensaje}`);
                    }
                })
                .catch(err => {
                    console.error('Error al eliminar los datos:', err);
                });
        }
    });




    const btnadd = document.getElementById('addUsuario');

    btnadd.addEventListener('click', function () {
        modalManager.crearModalDesdeUrl('assets/modales/modalRegistroAlumno.txt', function () {
            let modalRaiz = document.querySelector('.modal-contenedor');
            initRegistroAlumnoForm(modalRaiz); // Aquí se inicializa todo el JS
            // cambiar estilo del modal para que sea creaer
            let titulo = modalRaiz.querySelector('h2');
            if (titulo) titulo.textContent = 'Añadir alumno';
            let btnSubmit = modalRaiz.querySelector('button[type=submit]');
            if (btnSubmit) btnSubmit.textContent = 'Crear';

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