window.addEventListener('load', function () {

    // cargar los usuarios desde el json al cargar la pagina
    const tbody = document.querySelector('.tablaUsuarios tbody');

    fetch('mockeos/alumnos.json')
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
        tdape.textContent = usuario.apellidos;
        fila.appendChild(tdape);

        const tdmail = document.createElement('td');
        tdmail.textContent = usuario.mail;
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



    //ver los detalles de los usuarios con el boton detalles
    tbody.addEventListener('click', function (e) {
        const modalManager = new ModalManager();
        
        if (e.target.classList.contains('btn-detalles')) {
            const fila = e.target.closest('tr');
            const id = e.target.dataset.id;
            
            modalManager.crearModal('<h2>Detalles del usuario</h2><p>Cargando...</p>');

            fetch('mockeos/alumno12.json')
                .then(res => res.json())
                .then(data => {
                    const usuario = data[0];
                    alert(`Detalles del usuario:\nID: ${usuario.id}\nNombre: ${usuario.nombre}\nApellidos: ${usuario.apellidos}\nCorreo: ${usuario.mail}`);
                })
                .catch(err => {
                    console.error('Error al cargar detalles del usuario:', err);
                });
        }
    });

    const btnadd = document.getElementById('addUsuario');

    btnadd.addEventListener('click', function () {
        fetch('mockeos/crearUsuario.json')
            .then(res => res.json())
            .then(data => {
                if (data.creado === true) {
                    const usuario = data.usuario;
                    pintarTabla(usuario);
            }})
            .catch(err => {
                console.error('Error al crear usuario:', err);
            });
    });



});