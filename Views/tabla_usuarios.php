<?php $this->layout('base_simple', ['title' => 'Usuarios registrados']) ?>

<?php $this->start('css') ?>
<link rel="stylesheet" href="/assets/css/tabla_usuarios.css">
<?php $this->stop() ?>

<?php $this->start('contenido') ?>
<section class="tablaUsuariosContainer">
  <h1>Listado de usuarios</h1>
  <p>Esta tabla se genera dinámicamente desde JavaScript.</p>

  <table class="tablaUsuarios">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Email</th>
        <th>Tipo</th>
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
<script src="/assets/js/tabla_usuarios.js"></script>
<?php $this->stop() ?>