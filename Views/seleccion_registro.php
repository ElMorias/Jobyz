<?php $this->layout('base_simple', ['title' => '¿Quién eres?']) ?>

<?php $this->start('css') ?>
 <link rel="stylesheet" href="assets/css/login.css">
<?php $this->stop() ?>

<?php $this->start('contenido') ?>
<section class="elige-tipo-container">
  <h1>¿Cómo quieres registrarte?</h1>
  <p>Selecciona tu perfil para continuar con el registro.</p>

  <div class="tipo-opciones">
    <a href="/index.php?page=registro_alumno" class="btn">
      Soy alumno
    </a>
    <a href="/index.php?page=registro_empresa" class="btn">
      Soy empresa
    </a>
  </div>
</section>
<?php $this->stop() ?>
