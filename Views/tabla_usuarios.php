<?php $this->layout('base_simple', ['title' => 'Usuarios registrados']) ?>

<?php $this->start('css') ?>
<link rel="stylesheet" href="assets/css/tabla_usuarios.css">
<?php $this->stop() ?>

<?php $this->start('contenido') ?>
<section class="tablaUsuariosContainer">
  <h1>Listado de usuarios</h1>

  <div><button id="borrar">borrar usuario<button></div>

  <table class="tablaUsuarios">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Apellidos</th>
        <th>Correo</th>
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
<script src="assets/js/tabla_alumnos.js"></script>
<?php $this->stop() ?>