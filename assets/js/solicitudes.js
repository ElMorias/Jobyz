document.addEventListener('DOMContentLoaded', function() {
  recargarSolicitudes();
});

function recargarSolicitudes() {
  fetch('api/ApiSolicitud.php')
    .then(res => res.json())
    .then(data => {
      renderSolicitudes(data.rol, data.solicitudes);
    });
}

function renderSolicitudes(rol, solicitudes) {
  const cont = document.getElementById('contenedor-solicitudes');
  cont.innerHTML = '';

  if (rol === 1) { // ADMIN
    let html = `
      <table class="table-solicitudes">
        <thead>
          <tr>
            <th>Correo solicitante</th>
            <th>Fecha</th>
            <th>Empresa</th>
            <th>Oferta</th>
          </tr>
        </thead>
        <tbody>
    `;
    solicitudes.forEach(function(s) {
      html += `
        <tr>
          <td>${s.alumno_email}</td>
          <td>${s.fecha_solicitud}</td>
          <td>${s.empresa_nombre}</td>
          <td>${s.oferta_titulo}</td>
        </tr>
      `;
    });
    html += `</tbody></table>`;
    if (solicitudes.length === 0)
      html += `<p class="ofertas-vacio">No hay solicitudes.</p>`;
    cont.innerHTML = html;
  }

  if (rol === 3) { // EMPRESA
    let html = `
      <table class="table-solicitudes">
        <thead>
          <tr>
            <th>Alumno</th>
            <th>Oferta</th>
            <th>Email</th>
            <th>Fecha</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
    `;
    solicitudes.forEach(function(s) {
      html += `
        <tr data-id="${s.id}">
          <td>${s.alumno_nombre}</td>
          <td>${s.oferta_titulo}</td>
          <td>${s.alumno_email}</td>
          <td>${s.fecha_solicitud}</td>
          <td>
            <button class="btn-tabla btn-aceptar">Aceptar</button>
            <button class="btn-tabla btn-rechaza btn-borrar">Rechazar</button>
          </td>
        </tr>
      `;
    });
    html += `</tbody></table>`;
    if (solicitudes.length === 0)
      html += `<p class="ofertas-vacio">No hay solicitudes.</p>`;
    cont.innerHTML = html;
    addEmpresaListeners();
  }

  if (rol === 2) { // ALUMNO
    let html = `<div class="solicitudes-grid">`;
    solicitudes.forEach(function(s) {
      html += `
        <div class="card-solicitud" data-id="${s.id}">
          <div class="titulo-oferta">${s.oferta_titulo}</div>
          <div class="empresa">${s.empresa_nombre}</div>
          <div class="fecha-solicitud">Solicitada el ${s.fecha_solicitud}</div>
          <div class="card-actions">
            <button class="btn-tabla btn-borrar">Eliminar</button>
          </div>
        </div>
      `;
    });
    html += `</div>`;
    if (solicitudes.length === 0)
      html += `<p class="ofertas-vacio">No tienes solicitudes activas.</p>`;
    cont.innerHTML = html;
    addAlumnoListeners();
  }
}

// ACCIONES para empresa
function addEmpresaListeners() {
  document.querySelectorAll('.btn-aceptar').forEach(btn => {
    btn.onclick = function() {
      const id = this.closest('tr').dataset.id;
      fetch('api/ApiSolicitud.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({accion: 'aceptar', id})
      }).then(() => recargarSolicitudes());
    };
  });
  document.querySelectorAll('.btn-rechazar').forEach(btn => {
    btn.onclick = function() {
      const id = this.closest('tr').dataset.id;
      fetch('api/ApiSolicitud.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({accion: 'rechazar', id})
      }).then(() => recargarSolicitudes());
    };
  });
}

// ACCIONES para alumno
function addAlumnoListeners() {
  document.querySelectorAll('.btn-borrar').forEach(btn => {
    btn.onclick = function() {
      const id = this.closest('.card-solicitud').dataset.id;
      fetch('api/ApiSolicitud.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({accion: 'eliminar', id})
      }).then(() => recargarSolicitudes());
    };
  });
}
