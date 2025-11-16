<?php $this->layout('base_simple', ['title' => 'Iniciar sesión']) ?>

<?php $this->start('css') ?>
 <link rel="stylesheet" href="assets/css/login.css">
<?php $this->stop() ?>

<?php $this->start('contenido') ?>
<section class="login-container">
  <h1>Accede a tu cuenta</h1>

  <div class="error"><?php if(isset($error) && $error){ echo htmlspecialchars($error); } ?></div>


  <form action="" method="POST" class="login-form">
    <div class="form-group">
      <label for="usuario">Nombre de usuario</label>
      <input type="text" id="usuario" name="usuario" required>
    </div>

    <div class="form-group">
      <label for="contraseña">Contraseña</label>
      <input type="password" id="contraseña" name="contraseña" required>
    </div>

    <button type="submit" class="login-btn">Entrar</button>
  </form>

  <div class="forgot-password">
    <a href="/Jobyz/index.php?page=seleccion_registro">Crear cuenta</a>
    <a href="/Jobyz/index.php?page=recuperar">¿Ha olvidado la contraseña?</a>
  </div>
</section>

<?php $this->stop() ?>
