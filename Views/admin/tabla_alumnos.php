<?php $this->layout('base_simple', ['title' => 'Usuarios registrados']) ?>

<?php $this->start('css') ?>

<?php $this->stop() ?>

<?php $this->start('contenido') ?>
<section class="tablaUsuariosContainer">
  <h1>Listado de Alumnos</h1>

  <div class="accionesUsuarios">
    <div class="busqueda">
      <form action="" class="form-no-form">
         <input type="text" id="buscador-alumnos" placeholder="Buscar Alumno...">
      </form>
      
    </div>
    <div>
      <button id="addUsuario">Añadir</button>
      <button id="addMasivo">Añadir varios</button>
      <button type="button" id="exportarAlumnosBtn" class="btn">Descargar Alumnos PDF</button>
    </div>
  </div>


  <div id="contenedor-alumnos"></div>
  <div id="contenedor-alumnos-noval"></div>
</section>
<?php $this->stop() ?>

<?php $this->start('js') ?>
  <script src="assets/js/ModalManager.js"></script>
  <script src="assets/js/registro_alumno.js"></script>
  <script src="assets/js/tabla_alumnos.js"></script>

<?php $this->stop() ?>