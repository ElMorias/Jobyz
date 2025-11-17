<?php $this->layout('base_simple', ['title' => 'Registro de empresa']) ?>

<?php $this->start('css') ?>
<?php $this->stop() ?>

<?php $this->start('contenido') ?>
<section class="registro-container">

  <?php if (!empty($errores)): ?>
    <div class="form-errores">
      <ul>
        <?php foreach ($errores as $err): ?>
          <li><?= htmlspecialchars($err) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form action="" method="POST" id="form-crear-empresa" class="form-registrar-empresa" enctype="multipart/form-data">

  <!-- Datos de Cuenta -->
  <div class="form-bloque">
    <h3>Datos de Cuenta</h3>
    <div class="form-row">
      <div class="form-group">
        <label for="correo">Correo electrónico</label>
        <input type="email" id="correo" name="correo" value="<?= $empresa['correo'] ?? '' ?>" required maxlength="80">
      </div>
      <div class="form-group">
        <label for="contrasena">Contraseña</label>
        <input type="password" id="contrasena" name="contrasena" required minlength="8" maxlength="60" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Al menos 8 caracteres, una mayúscula, una minúscula y un número">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label for="repetir_contrasena">Repetir contraseña</label>
        <input type="password" id="repetir_contrasena" name="repetir_contrasena" required minlength="8" maxlength="60" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Al menos 8 caracteres, una mayúscula, una minúscula y un número">
      </div>
    </div>
    <input type="hidden" name="rol_id" value="3">
  </div>

  <!-- Datos Generales -->
  <div class="form-bloque">
    <h3>Datos Generales</h3>
    <div class="form-row">
      <div class="form-group">
        <label for="nombre">Nombre de la empresa</label>
        <input type="text" id="nombre" name="nombre" value="<?= $empresa['nombre'] ?? '' ?>" required maxlength="60">
      </div>
      <div class="form-group">
        <label for="direccion">Dirección</label>
        <input type="text" id="direccion" name="direccion" value="<?= $empresa['direccion'] ?? '' ?>" required maxlength="80">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label for="cif">CIF</label>
        <input type="text" id="cif" name="cif" value="<?= $empresa['cif'] ?? '' ?>" required maxlength="12" pattern="^[A-Z][0-9]{7}[A-Z0-9]$" title="Formato típico: letra + 7 dígitos + letra/dígito">
      </div>
    </div>
  </div>

  <!-- Contacto -->
  <div class="form-bloque">
    <h3>Contacto</h3>
    <div class="form-row">
      <div class="form-group">
        <label for="pcontacto">Persona de contacto</label>
        <input type="text" id="pcontacto" name="pcontacto" value="<?= $empresa['pcontacto'] ?? '' ?>" maxlength="50">
      </div>
      <div class="form-group">
        <label for="pcontactoemail">Email persona contacto</label>
        <input type="email" id="pcontactoemail" name="pcontactoemail" value="<?= $empresa['pcontactoemail'] ?? '' ?>" maxlength="80">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label for="tfcontacto">Teléfono contacto</label>
        <input type="tel" id="tfcontacto" name="tfcontacto" value="<?= $empresa['tfcontacto'] ?? '' ?>" maxlength="15" pattern="^[0-9]{9,15}$" title="9 a 15 dígitos, solo números">
      </div>
    </div>
  </div>

    <!-- Logo / Foto -->
    <div class="form-bloque form-documentos">
      <h3>Logo / Imagen</h3>
      <div class="form-row">
        <div class="foto-contenedor">
          <input type="file" id="fotoFile" name="foto" accept="image/*">
        </div>
        <div id="preview-foto"></div>
      </div>
    </div>

    <!-- Botón registro -->
    <div class="registro-botones">
      <input type="hidden" name="validada" value="0">
      <button type="submit" class="btn-registro">Registrar</button>
      <a href="/Jobyz/index.php?page=landing" class="btn">  
        Cancelar Registro
      </a>
    </div>
  </form>
</section>
<?php $this->stop() ?>

<?php $this->start('js') ?>
<?php $this->stop() ?>


