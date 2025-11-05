<?php $this->layout('base_simple', ['title' => '¿Quién eres?']) ?>

<?php $this->start('css') ?>
<?php $this->stop() ?>

<?php $this->start('contenido') ?>
<section class="elige-tipo-container">
  <h1>¿Cómo quieres registrarte?</h1>
  <p>Selecciona tu perfil para continuar con el registro.</p>

  <div class="tipo-opciones">
    <a href="/Jobyz/index.php?page=registro_alumno" class="btn-tipo alumno">
      Soy alumno
    </a>
    <a href="/Jobyz/index.php?page=registro_empresa" class="btn-tipo empresa">
      Soy empresa
    </a>
  </div>
</section>
<?php $this->stop() ?>
