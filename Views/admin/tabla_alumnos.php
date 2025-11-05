<?php $this->layout('base_simple', ['title' => 'Usuarios registrados']) ?>

<?php $this->start('css') ?>

<?php $this->stop() ?>

<?php $this->start('contenido') ?>
<section class="tablaUsuariosContainer">
  <h1>Listado de Alumnos</h1>

  <div class="accionesUsuarios">
    <div class="busqueda">
      <input type="text" id="buscadorUsuarios" placeholder="Buscar Alumno...">
      <button id="filtrarUsuarios">Filtrar</button>
    </div>
    <button id="addUsuario">Añadir</button>
    <button id="addUsuario">Añadir varios</button>
  </div>


  <table class="tablaUsuarios">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Correo</th>
        <th>Teléfono</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <!-- Las filas se insertan dinámicamente desde JS -->
    </tbody>
  </table>
</section>
<?php $this->stop() ?>

<?php $this->start('js') ?>
  <script src="assets/js/ModalManager.js"></script>
  <script src="assets/js/registro_alumno.js"></script>
  <script src="assets/js/tabla_alumnos.js"></script>

<?php $this->stop() ?>