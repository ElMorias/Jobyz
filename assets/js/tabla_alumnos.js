window.addEventListener('load', function () {

    const tbody = document.querySelector('.tablaUsuarios tbody');

    fetch('mockeos/alumnos.json')
        .then(res => res.json())
        .then(usuarios => {
            usuarios.forEach(usuario => {
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
                tdAcciones.textContent = ''; // vacío por ahora
                fila.appendChild(tdAcciones);

                tbody.appendChild(fila);
            });
        })
        .catch(err => {
            console.error('Error al cargar usuarios:', err);
        });


    const btnBorrar = document.getElementById('borrar');

    btnBorrar.addEventListener('click', function () {
        fetch('mockeos/borrar8.json')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'ok') {
                    alert(`Usuario con ID ${data.id_borrado} borrado correctamente.`);
                } else {
                    alert('Error al borrar el usuario.');
                }
            })
            .catch(err => {
                console.error('Error al borrar usuario:', err);
            });
    });

    tbody.addEventListener('click', function (e) {
        const fila = e.target.closest('tr');
        if (!fila) return;

        fetch('mockeos/alumno12.json')
            .then(res => res.json())
            .then(data => {
                const usuario = data[0];
                alert(`tr.id: ${fila.firstChild.textContent}`); // desde aqui tambien puedo sacar datos de la fila especifica
                alert(`Detalles del usuario:\nID: ${usuario.id}\nNombre: ${usuario.nombre}\nApellidos: ${usuario.apellidos}\nCorreo: ${usuario.mail}`);
            })
            .catch(err => {
                console.error('Error al cargar detalles del usuario:', err);
            });
    });



});