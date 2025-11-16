<?php $this->layout('base_simple', ['title' => 'Modificar Empresa']) ?>
<?php $this->start('css') ?>
<?php $this->stop() ?>
<?php $this->start('contenido') ?>
<section class="registro-container">
  <form method="POST" enctype="multipart/form-data">
    <h2>Perfil de la Empresa</h2>

    <?php if (!empty($error)): ?>
      <div class="form-error">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <div class="form-columnas">
      <!-- Datos de Cuenta -->
      <div class="form-bloque">
        <h3>Datos de Cuenta</h3>
        <div class="form-group">
          <label for="correo">Correo electrónico</label>
          <input type="email" id="correo" name="correo" value="<?= htmlspecialchars($empresa['correo'] ?? '') ?>" readonly>
        </div>
        <div class="form-group">
          <label for="contrasena">Contraseña</label>
          <input type="password" id="contrasena" name="contrasena">
        </div>
        <input type="hidden" name="rol_id" value="3">
      </div>

      <!-- Datos Generales -->
      <div class="form-bloque">
        <h3>Datos Generales</h3>
        <div class="form-group">
          <label for="nombre">Nombre de la empresa</label>
          <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($empresa['nombre'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label for="direccion">Dirección</label>
          <input type="text" id="direccion" name="direccion" value="<?= htmlspecialchars($empresa['direccion'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label for="cif">CIF</label>
          <input type="text" id="cif" name="cif" value="<?= htmlspecialchars($empresa['cif'] ?? '') ?>" required>
        </div>
      </div>

      <!-- Contacto -->
      <div class="form-bloque">
        <h3>Contacto</h3>
        <div class="form-group">
          <label for="pcontacto">Persona de contacto</label>
          <input type="text" id="pcontacto" name="pcontacto" value="<?= htmlspecialchars($empresa['pcontacto'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label for="pcontactoemail">Email persona contacto</label>
          <input type="email" id="pcontactoemail" name="pcontactoemail" value="<?= htmlspecialchars($empresa['pcontactoemail'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label for="tlfcontacto">Teléfono contacto</label>
          <input type="tel" id="tlfcontacto" name="tlfcontacto" value="<?= htmlspecialchars($empresa['tlfcontacto'] ?? '') ?>">
        </div>
      </div>
    </div>

    <!-- Logo / Foto -->
    <div class="form-bloque form-documentos">
      <h3>Logo / Imagen</h3>
      <div class="form-group">
        <label>Logo actual:</label><br>
        <?php if (!empty($empresa['foto'])): ?>
          <img src="<?= htmlspecialchars($empresa['foto']) ?>" alt="Logo actual" style="max-width:120px;"><br>
        <?php else: ?>
          <span>No hay logo.</span><br>
        <?php endif; ?>
        <label for="nuevoLogo">Subir logo nuevo</label>
        <input type="file" id="nuevoLogo" name="nuevoLogo" accept="image/*">
        <small>Al guardar, si eliges un logo nuevo, se reemplazará el actual.</small>
      </div>
    </div>

    <input type="hidden" name="validada" value="<?= htmlspecialchars($empresa['validada'] ?? '0') ?>">

    <div style="margin-top: 2em; display: flex; gap: 1em;">
      <button type="submit" class="btn-registro">Guardar cambios</button>
      <a href="?page=ofertas" class="btn">Cancelar</a>
    </div>
  </form>
</section>
<?php $this->stop() ?>