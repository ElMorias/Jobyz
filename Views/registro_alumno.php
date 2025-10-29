<?php $this->layout('base_simple', ['title' => 'Registro de Alumno']) ?>

<?php $this->start('css') ?>
<link rel="stylesheet" href="assets/css/registro.css">
<?php $this->stop() ?>

<?php $this->start('contenido') ?>
<section class="registro-container">
  <h1>Registro de Alumno</h1>

<form action="/?page=login" method="POST" enctype="multipart/form-data" class="registro-container">
 <div class="form-grid">
    <div>
      
      <label for="correo">Correo electrónico</label>
      <input type="email" name="correo" id="correo" required>
      <div id="errorCorreo"></div>

      <label for="contrasena">Contraseña</label>
      <input type="password" name="contrasena" id="contrasena" required>
      <div id="errorContrasena"></div>

      <label for="nombre">Nombre</label>
      <input type="text" name="nombre" id="nombre" required>
      <div id="errorNombre"></div>

      <label for="apellido1">Primer apellido</label>
      <input type="text" name="apellido1" id="apellido1" required>
      <div id="errorApellido1"></div>
            
      <label for="apellido2">Segundo apellido</label>
      <input type="text" name="apellido2" id="apellido2">
    </div>

    <div>
      <label for="fnacimiento">Fecha de nacimiento</label>
      <input type="date" name="fnacimiento" id="fnacimiento" required>
      <div id="errorFnacimiento"></div>

      <label for="curriculum">Currículum</label>
      <input type="file" name="curriculum" id="curriculum" accept=".pdf,.doc,.docx" required>
      <div id="errorCurriculum"></div>

      <label for="dni">DNI</label>
      <input type="text" name="dni" id="dni" required>
      <div id="errorDni"></div>

      <label for="direccion">Dirección</label>
      <input type="text" name="direccion" id="direccion" required>
      <div id="errorDireccion"></div>

      <label for="foto">Foto de perfil</label>
      <input type="file" name="foto" id="foto" accept="image/*">

      <button type="button" id="btnTomarFoto">Tomar foto</button>
      <button type="button" id="btnSubirFoto">Subir foto</button>


    </div>
  </div>

  <button type="submit">Registrarse</button>
</form>


</section>
<?php $this->stop() ?>
