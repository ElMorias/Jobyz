<?php $this->layout('base_simple', ['title' => 'Registro de empresa']) ?>

<?php $this->start('css') ?>
<?php $this->stop() ?>

<?php $this->start('contenido') ?>
<section class="registro-container">

  <?php if (!empty($error)): ?>
    <div class="form-error" style="color: #a00; background: #fdd; padding: 1em; border: 1px solid #a00; margin-bottom:1em;">
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

  <form action="" method="POST" id="form-registrar-empresa" class="form-registrar-empresa" enctype="multipart/form-data">
    <h2>Registro de Empresa</h2>

    <!-- Datos de Cuenta -->
    <div class="form-bloque">
      <h3>Datos de Cuenta</h3>
      <div class="form-group">
        <label for="correo">Correo electrónico</label>
        <input type="email" id="correo" name="correo" value="<?= $empresa['correo'] ?? '' ?>" required>
      </div>
      <div class="form-group">
        <label for="contrasena">Contraseña</label>
        <input type="password" id="contrasena" name="contrasena" required>
      </div>
      <div class="form-group">
        <label for="repetir_contrasena">Repetir contraseña</label>
        <input type="password" id="repetir_contrasena" name="repetir_contrasena" required>
      </div>
      <input type="hidden" name="rol_id" value="3">
    </div>

    <!-- Datos Generales -->
    <div class="form-bloque">
      <h3>Datos Generales</h3>
      <div class="form-group">
        <label for="nombre">Nombre de la empresa</label>
        <input type="text" id="nombre" name="nombre" value="<?= $empresa['nombre'] ?? '' ?>" required>
      </div>
      <div class="form-group">
        <label for="direccion">Dirección</label>
        <input type="text" id="direccion" name="direccion" value="<?= $empresa['direccion'] ?? '' ?>" required>
      </div>
      <div class="form-group">
        <label for="cif">CIF</label>
        <input type="text" id="cif" name="cif" value="<?= $empresa['cif'] ?? '' ?>" required>
      </div>
    </div>

    <!-- Contacto -->
    <div class="form-bloque">
      <h3>Contacto</h3>
      <div class="form-group">
        <label for="pcontacto">Persona de contacto</label>
        <input type="text" id="pcontacto" name="pcontacto" value="<?= $empresa['pcontacto'] ?? '' ?>">
      </div>
      <div class="form-group">
        <label for="pcontactoemail">Email persona contacto</label>
        <input type="email" id="pcontactoemail" name="pcontactoemail" value="<?= $empresa['pcontactoemail'] ?? '' ?>">
      </div>
      <div class="form-group">
        <label for="tfcontacto">Teléfono contacto</label>
        <input type="tel" id="tfcontacto" name="tfcontacto" value="<?= $empresa['tfcontacto'] ?? '' ?>">
      </div>
    </div>

    <!-- Logo / Foto -->
    <div class="form-bloque form-documentos">
      <h3>Logo / Imagen</h3>
      <div class="foto-contenedor">
      <input type="file" id="fotoFile" name="foto" accept="image/*">
    </div>
    <div id="preview-foto"></div>

    <!-- Estado de validación -->
    <input type="hidden" name="validada" value="0">

    <button type="submit" class="btn-registro">Registrar empresa</button>
  </form>

</section>
<?php $this->stop() ?>

<?php $this->start('js') ?>
<?php $this->stop() ?>


