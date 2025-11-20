const canvasUsuarios = document.getElementById('grafico-usuarios');
const canvasOfertas = document.getElementById('grafico-ofertas-ciclos');
const canvasAlumnos = document.getElementById('grafico-alumnos-ciclos');

fetch('api/apiEstadisticas.php', {
    headers: {
        Authorization: "Bearer " + sessionStorage.getItem('token'),
        "X-USER-ID": sessionStorage.getItem('user_id')
    }
})
.then(res => res.json())
.then(data => {
    // Gráfico de tarta (pie)
    new Chart(canvasUsuarios.getContext('2d'), {
        type: 'pie',
        data: {
            labels: Object.keys(data.porcentajes),
            datasets: [{
                data: Object.values(data.porcentajes),
                backgroundColor: ['#215A63', '#8bcf03']
            }]
        },
        options: { plugins: { title: { display: true, text: 'Porcentaje de usuarios' } } }
    });
    canvasUsuarios.removeAttribute('width');
    canvasUsuarios.removeAttribute('height');

    // Ciclos en ofertas (BARRAS HORIZONTALES)
    new Chart(canvasOfertas.getContext('2d'), {
        type: 'bar',
        data: {
            labels: Object.keys(data.topOfertasCiclo),
            datasets: [{
                label: 'Ofertas por ciclo',
                data: Object.values(data.topOfertasCiclo),
                backgroundColor: '#2a7ae2'
            }]
        },
        options: {
            indexAxis: 'y',  // <- ESTO hace que sea horizontal
            plugins: { title: { display: true, text: 'Ciclos con más ofertas' } }
        }
    });
    canvasOfertas.removeAttribute('width');
    canvasOfertas.removeAttribute('height');

    // Ciclos con más alumnos (BARRAS HORIZONTALES)
    new Chart(canvasAlumnos.getContext('2d'), {
        type: 'bar',
        data: {
            labels: Object.keys(data.topAlumnosCiclo),
            datasets: [{
                label: 'Alumnos por ciclo',
                data: Object.values(data.topAlumnosCiclo),
                backgroundColor: '#d93025'
            }]
        },
        options: {
            indexAxis: 'y', // <- ESTO hace que sea horizontal
            plugins: { title: { display: true, text: 'Ciclos con más alumnos' } }
        }
    });
    canvasAlumnos.removeAttribute('width');
    canvasAlumnos.removeAttribute('height');
});
