<?php $this->layout('base_simple', ['title' => 'Registro de Alumno']) ?>

<?php $this->start('css') ?>
<?php $this->stop() ?>

<?php $this->start('contenido') ?>
<section class="registro-container">

  <form action="" method="POST" id="form-registro-alumno" class="form-registro-alumno" enctype="multipart/form-data">
    <h2>Registro de Alumno</h2>

    <!-- Primera fila: dos columnas -->
    <div class="form-columnas">
      <!-- Datos de Cuenta -->
      <div class="form-bloque">
        <h3>Datos de Cuenta</h3>
        <div class="form-group">
          <label for="correo">Correo electrónico</label>
          <input type="email" id="correo" name="correo">
        </div>
        <div class="form-group">
          <label for="contrasena">Contraseña</label>
          <input type="password" id="contrasena" name="contrasena">
        </div>
        <div class="form-group">
          <label for="repetir_contrasena">Repetir contraseña</label>
          <input type="password" id="repetir_contrasena" name="repetir_contrasena">
        </div>
        <input type="hidden" name="rol_id" value="2">
        <input type="hidden" name="validado" value="1">
      </div>

      <!-- Datos Personales -->
      <div class="form-bloque">
        <h3>Datos Personales</h3>
        <div class="form-datos-personales">
          <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre">
          </div>
          <div class="form-group">
            <label for="apellido1">Primer Apellido</label>
            <input type="text" id="apellido1" name="apellido1">
          </div>
          <div class="form-group">
            <label for="apellido2">Segundo Apellido</label>
            <input type="text" id="apellido2" name="apellido2">
          </div>
          <div class="form-group">
            <label for="fnacimiento">Fecha de Nacimiento</label>
            <input type="date" id="fnacimiento" name="fnacimiento">
          </div>
          <div class="form-group">
            <label for="dni">DNI</label>
            <input type="text" id="dni" name="dni">
          </div>
          <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input type="tel" id="telefono" name="telefono">
          </div>
          <div class="form-group" style="grid-column: span 2;">
            <label for="direccion">Dirección</label>
            <input type="text" id="direccion" name="direccion">
          </div>
        </div>
      </div>
    </div>

    <!-- Segunda fila: bloque completo -->
    <div class="form-bloque" id="bloque-estudios">
      <h3>Estudios</h3>
      <div id="estudios-wrapper"></div>
      <button type="button" id="btn-agregar-estudio" class="btn-verde-lima">+ Añadir estudio</button>
    </div>

    <!-- Documentos y Foto -->
    <div class="form-bloque form-documentos">
      <h3>Documentos</h3>
      <div class="form-group">
        <label for="curriculum">Curriculum (PDF)</label>
        <input type="file" id="curriculum" name="curriculum" accept="application/pdf">
      </div>
      <h3>Foto de perfil</h3>
      <div class="foto-contenedor">
        <input type="file" id="fotoFile" name="foto" accept="image/*">
        <button type="button" id="tomarFotoBtn" class="btn-verde-lima">Tomar foto</button>
      </div>
      <div id="preview-foto"></div>
    </div>

    <button type="submit" class="btn-registro">Registrar alumno</button>
  </form>

</section>
<?php $this->stop() ?>

<?php $this->start('js') ?>
<script src="assets/js/registro_alumno.js"></script>
<script src="assets/js/ModalManager.js"></script>
<?php $this->stop() ?>
