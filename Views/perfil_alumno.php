<?php $this->layout('base_simple', ['title' => 'Registro de Alumno']) ?>

<?php $this->start('css') ?>
<?php $this->stop() ?>


<?php $this->start('contenido') ?>

<form action="" method="POST" id="form-editar-alumno" class="modal-form" enctype="multipart/form-data">
  <input type="hidden" name="id" id="perfil-id">
  <input type="hidden" name="validado" id="perfil-validado" value="1">
  <div id="modal-errores"></div>
    <!--Bloque 1: Correo, contraseña, nombre -->
    <div class="form-bloque">
    <h3>Identificación</h3>
    <div class="form-datos-personales">
        <div class="form-group">
        <label for="perfil-correo">Correo</label>
        <input type="email" name="correo" id="perfil-correo" required readonly maxlength="80">
        </div>
        <div class="form-group">
        <label for="perfil-contrasena">Contraseña</label>
        <input type="password" name="contrasena" id="perfil-contrasena" minlength="6" maxlength="60">
        </div>
        <div class="form-group">
        <label for="perfil-nombre">Nombre</label>
        <input type="text" name="nombre" id="perfil-nombre" required pattern="[A-Za-zÁÉÍÓÚáéíóúüÜñÑ ]{2,50}" maxlength="50">
        </div>
    </div>
    </div>

    <!--Bloque 2: Apellidos y fecha -->
    <div class="form-bloque">
    <h3>Datos personales</h3>
    <div class="form-datos-personales">
        <div class="form-group">
        <label for="perfil-apellido1">Primer apellido</label>
        <input type="text" name="apellido1" id="perfil-apellido1" required pattern="[A-Za-zÁÉÍÓÚáéíóúüÜñÑ ]{2,50}" maxlength="50">
        </div>
        <div class="form-group">
        <label for="perfil-apellido2">Segundo apellido</label>
        <input type="text" name="apellido2" id="perfil-apellido2" pattern="[A-Za-zÁÉÍÓÚáéíóúüÜñÑ ]{2,50}" maxlength="50">
        </div>
        <div class="form-group">
        <label for="perfil-fnacimiento">Fecha de nacimiento</label>
        <input type="date" name="fnacimiento" id="perfil-fnacimiento" required>
        </div>
    </div>
    </div>

    <!--Bloque 3: DNI, dirección, teléfono -->
    <div class="form-bloque">
    <h3>Contacto</h3>
    <div class="form-datos-personales">
        <div class="form-group">
        <label for="perfil-dni">DNI</label>
        <input type="text" name="dni" id="perfil-dni" required pattern="^[0-9]{8}[A-Za-z]$" maxlength="9" title="Debe tener formato 12345678A">
        </div>
        <div class="form-group">
        <label for="perfil-direccion">Dirección</label>
        <input type="text" name="direccion" id="perfil-direccion" required maxlength="80">
        </div>
        <div class="form-group">
        <label for="perfil-telefono">Teléfono</label>
        <input type="tel" name="telefono" id="perfil-telefono" required pattern="^[0-9]{9}$" maxlength="9" title="Debe tener 9 dígitos">
        </div>
    </div>
    </div>

    <!--Bloque 3: estudios -->
 
   <div class="form-bloque" id="bloque-estudios">
      <div id="estudios-guardados" class="formd-datos-personales">
      <h3>Mis estudios actuales</h3>
      <!-- Aquí JS irá metiendo los estudios existentes como bloques -->
      </div>
      <h3>Estudios</h3>
      <div id="estudios-wrapper"></div>
      <button type="button" id="btn-agregar-estudio" class="btn-verde-lima">+ Añadir estudio</button>
    </div>

  <!--Documentos -->
  <div class="form-bloque">
    <h3>Documentos</h3>
    <div class="form-datos-personales">
      <div class="form-group">
        <label for="curriculum">Currículum actual</label>
        <a id="curriculum-link" href="#" target="_blank" style="display:none;">Ver curriculum actual</a>
      </div>
      <div class="form-group">
        <label for="curriculum">Currículum (PDF nuevo)</label>
        <input type="file" id="curriculum" name="curriculum" accept="application/pdf">
      </div>
    </div>

    <h3>Foto de perfil</h3>
    <div class="form-datos-personales">
      <div class="form-group foto-contenedor">
        <label for="fotoFile">Subir foto</label>
        <input type="file" id="fotoFile" name="foto" accept="image/*">
      </div>
      <div class="form-group">
        <label>&nbsp;</label>
        <button type="button" id="tomarFotoBtn" class="btn-verde-lima">Tomar foto</button>
      </div>
    </div>
    <div id="preview-foto" style="margin-top: 12px;"></div>
  </div>

  <!--Acciones -->
  <div class="form-actions">
    <button type="button" id="cerrar" class="btn-verde-lima">Cerrar</button>
    <button type="submit" id="actualizar" class="btn-verde-lima">Guardar Cambios</button>
  </div>
</form>
<?php $this->stop() ?>

<?php $this->start('js') ?>
<script src="assets/js/modificarAlumno.js"></script>
<script src="assets/js/ModalManager.js"></script>
<?php $this->stop() ?>