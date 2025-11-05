<?php $this->layout('base_simple', ['title' => 'Detalles empresa']) ?>
<?php $this->start('contenido') ?>
<section class="registro-container">
  <h2>Detalles de Empresa</h2>

  <form>
    <!-- Datos de Cuenta -->
    <div class="form-bloque">
      <h3>Datos de Cuenta</h3>
      <div class="form-group">
        <label for="correo">Correo electrónico</label>
        <input type="email" id="correo" name="correo" value="<?= htmlspecialchars($empresa['correo'] ?? '') ?>" readonly>
      </div>
    </div>

    <!-- Datos Generales -->
    <div class="form-bloque">
      <h3>Datos Generales</h3>
      <div class="form-group">
        <label for="nombre">Nombre de la empresa</label>
        <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($empresa['nombre'] ?? '') ?>" readonly>
      </div>
      <div class="form-group">
        <label for="direccion">Dirección</label>
        <input type="text" id="direccion" name="direccion" value="<?= htmlspecialchars($empresa['direccion'] ?? '') ?>" readonly>
      </div>
      <div class="form-group">
        <label for="cif">CIF</label>
        <input type="text" id="cif" name="cif" value="<?= htmlspecialchars($empresa['cif'] ?? '') ?>" readonly>
      </div>
    </div>

    <!-- Contacto -->
    <div class="form-bloque">
      <h3>Contacto</h3>
      <div class="form-group">
        <label for="pcontacto">Persona de contacto</label>
        <input type="text" id="pcontacto" name="pcontacto" value="<?= htmlspecialchars($empresa['pcontacto'] ?? '') ?>" readonly>
      </div>
      <div class="form-group">
        <label for="pcontactoemail">Email persona contacto</label>
        <input type="email" id="pcontactoemail" name="pcontactoemail" value="<?= htmlspecialchars($empresa['pcontactoemail'] ?? '') ?>" readonly>
      </div>
      <div class="form-group">
        <label for="tfcontacto">Teléfono contacto</label>
        <input type="tel" id="tfcontacto" name="tfcontacto" value="<?= htmlspecialchars($empresa['tlfcontacto'] ?? '') ?>" readonly>
      </div>
    </div>

    <!-- Logo / Foto -->
    <div class="form-bloque form-documentos">
      <h3>Logo / Imagen</h3>
      <div class="foto-contenedor">
        <?php if (!empty($empresa['foto'])): ?>
        <img src="<?= htmlspecialchars($empresa['foto']) ?>" alt="Logo de la empresa" style="max-width:150px;">
        <?php else: ?>
        <span>No hay logo disponible.</span>
        <?php endif; ?>
      </div>
    </div>

    <!-- Estado de validación -->
    <input type="text" name="validada" value="<?= !empty($empresa['validada']) ? 'Validada' : 'No validada' ?>" readonly>

    <!-- Botón cerrar -->
    <div style="margin-top:2em;">
    <a href="?page=tabla_empresas" class="btn">Cerrar</a>
    </div>
  </form>
</section>
<?php $this->stop() ?>
