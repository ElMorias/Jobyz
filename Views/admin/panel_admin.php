<?php $this->layout('base_simple', ['title' => 'Panel de control Admin']) ?>

<?php $this->start('css') ?>
<?php $this->stop() ?>

<?php $this->start('contenido') ?>
<div class="panel-box">
  <h2>Bienvenido al panel de control</h2>
  <h3> Estadisticas de la Página</h3>
</div>

<div class="dashboard-graficos">
    <div class="grafico-row">
        <div class="grafico-full">
            <canvas id="grafico-usuarios"></canvas>
        </div>
    </div>
    <div class="grafico-row">
        <div class="grafico-half">
            <canvas id="grafico-ofertas-ciclos"></canvas>
        </div>
        <div class="grafico-half">
            <canvas id="grafico-alumnos-ciclos"></canvas>
        </div>
    </div>
</div>
<?php $this->stop() ?>

<?php $this->start('js') ?>
  <script src="assets/js/estadisticas.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<?php $this->stop() ?>