<?php $this->layout('base_simple', ['title' => 'Registro de usuario']) ?>

<?php $this->start('css') ?>
<link rel="stylesheet" href="assets/css/registro.css">
<?php $this->stop() ?>

<?php $this->start('contenido') ?>
<section class="registro-container">
  <h1>Regístrate en Jobyz</h1>
  <p>Crea tu cuenta como alumno o empresa.</p>

  <form action="/?page=registro" method="POST" class="registro-form">
    <!-- Correo -->
    <label for="email">Correo electrónico</label>
    <input type="email" id="email" name="email" required value="<?= $_POST['email'] ?? '' ?>">

    <!-- Contraseña -->
    <label for="password">Contraseña</label>
    <input type="password" id="password" name="password" required>

    <!-- Tipo de usuario -->
    <label for="tipo">Soy:</label>
    <select id="tipo" name="tipo" required onchange="this.form.submit()">
      <option value="">Selecciona una opción</option>
      <option value="alumno" <?= ($_POST['tipo'] ?? '') === 'alumno' ? 'selected' : '' ?>>Alumno</option>
      <option value="empresa" <?= ($_POST['tipo'] ?? '') === 'empresa' ? 'selected' : '' ?>>Empresa</option>
    </select>

    <?php if ($_POST['tipo'] ?? '' === 'alumno'): ?>
      <!-- Campos adicionales para alumno -->
      <label for="carrera">Carrera o especialidad</label>
      <input type="text" id="carrera" name="carrera" value="<?= $_POST['carrera'] ?? '' ?>">

      <label for="centro">Centro educativo</label>
      <input type="text" id="centro" name="centro" value="<?= $_POST['centro'] ?? '' ?>">
    <?php elseif ($_POST['tipo'] ?? '' === 'empresa'): ?>
      <!-- Campos adicionales para empresa -->
      <label for="nombre_empresa">Nombre de la empresa</label>
      <input type="text" id="nombre_empresa" name="nombre_empresa" value="<?= $_POST['nombre_empresa'] ?? '' ?>">

      <label for="sector">Sector</label>
      <input type="text" id="sector" name="sector" value="<?= $_POST['sector'] ?? '' ?>">
    <?php endif; ?>

    <button type="submit" class="registro-btn">Crear cuenta</button>
  </form>
</section>
<?php $this->stop() ?>