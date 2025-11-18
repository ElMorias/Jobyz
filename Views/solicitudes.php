<?php $this->layout('base_simple', ['title' => 'Solicitudes']) ?>

<?php $this->start('css') ?>
<?php $this->stop() ?>

<?php $this->start('contenido') ?>
<div class="panel-box panel-box-nobg">
  <h2 class="panel-titulo">Solicitudes</h2>
  <div id="contenedor-solicitudes"></div>
</div>
<script src="assets/js/solicitudes.js"></script>
<script src="assets/js/ModalManager.js"></script>
<?php $this->stop() ?>
