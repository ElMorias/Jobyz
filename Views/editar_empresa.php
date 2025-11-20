<?php $this->layout('base_simple', ['title' => 'Modificar Empresa']) ?>
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

  <form method="POST" enctype="multipart/form-data">
    <h2>Modificar Empresa</h2>
    
    <!-- Datos de Cuenta -->
    <div class="form-bloque">
      <h3>Datos de Cuenta</h3>
      <div class="form-group">
        <label for="correo">Correo electrónico</label>
        <input type="email" id="correo" name="correo" value="<?= htmlspecialchars($empresa['correo'] ?? '') ?>" readonly maxlength="80">
      </div>
      <input type="hidden" name="rol_id" value="3">
    </div>

    <!-- Datos Generales -->
    <div class="form-bloque">
      <h3>Datos Generales</h3>
      <div class="form-group">
        <label for="nombre">Nombre de la empresa</label>
        <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($empresa['nombre'] ?? '') ?>" required maxlength="60">
      </div>
      <div class="form-group">
        <label for="direccion">Dirección</label>
        <input type="text" id="direccion" name="direccion" value="<?= htmlspecialchars($empresa['direccion'] ?? '') ?>" required maxlength="80">
      </div>
      <div class="form-group">
        <label for="cif">CIF</label>
        <input type="text" id="cif" name="cif" value="<?= htmlspecialchars($empresa['cif'] ?? '') ?>" required maxlength="12" pattern="^[A-Z][0-9]{7}[A-Z0-9]$" title="Formato típico: letra + 7 dígitos + letra/dígito">
      </div>
    </div>

    <!-- Contacto -->
    <div class="form-bloque">
      <h3>Contacto</h3>
      <div class="form-group">
        <label for="pcontacto">Persona de contacto</label>
        <input type="text" id="pcontacto" name="pcontacto" value="<?= htmlspecialchars($empresa['pcontacto'] ?? '') ?>" maxlength="50">
      </div>
      <div class="form-group">
        <label for="pcontactoemail">Email persona contacto</label>
        <input type="email" id="pcontactoemail" name="pcontactoemail" value="<?= htmlspecialchars($empresa['pcontactoemail'] ?? '') ?>" maxlength="80">
      </div>
      <div class="form-group">
        <label for="tlfcontacto">Teléfono contacto</label>
        <input type="tel" id="tlfcontacto" name="tlfcontacto" value="<?= htmlspecialchars($empresa['tlfcontacto'] ?? '') ?>" maxlength="15" pattern="^[0-9]{9,15}$" title="9 a 15 dígitos, solo números">
      </div>
    </div>
    

    <!-- Logo / Foto -->
    <div class="form-bloque form-documentos">
      <h3>Logo</h3>
      <div class="form-group">
        <label>Logo actual:</label><br>
        <?php if (!empty($empresa['foto'])): ?>
          <img src="<?= htmlspecialchars($empresa['foto']) ?>" alt="Logo actual"><br>
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
      <a href="?page=tabla_empresas" class="btn">Cancelar</a>
    </div>
  </form>
</section>
<?php $this->stop() ?>
