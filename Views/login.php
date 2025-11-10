<?php $this->layout('base_simple', ['title' => 'Iniciar sesión']) ?>

<?php $this->start('css') ?>
<?php $this->stop() ?>

<?php $this->start('contenido') ?>
<section class="login-container">
  <h1>Accede a tu cuenta</h1>

  <form action="/Controllers/login.php" method="POST" class="login-form">
    <div class="form-group">
      <label for="username">Nombre de usuario</label>
      <input type="text" id="username" name="username" required>
    </div>

    <div class="form-group">
      <label for="password">Contraseña</label>
      <input type="password" id="password" name="password" required>
    </div>

    <button type="submit" class="login-btn">Entrar</button>
  </form>

  <div class="forgot-password">
    <a href="/Jobyz/index.php?page=seleccion_registro">Crear cuenta</a>
    <a href="/Jobyz/index.php?page=recuperar">¿Ha olvidado la contraseña?</a>
  </div>
</section>

<?php $this->stop() ?>
