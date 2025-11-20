<?php $this->layout('base', ['title' => 'Jobyz – Portal de Empleo']);?>

<?php $this->start('css') ?>
  <link rel="stylesheet" href="assets/css/estilos.css">
<?php $this->stop() ?>

<?php $this->start('contenido') ?>
<div class="landing-wrap">

  <!-- HERO / CABECERA -->
  <section class="hero-landing">
    <div class="hero-overlay">
      <h1>Tu futuro empieza en <span class="marcajobyz">Jobyz</span></h1>
      <p class="hero-slogan">Conecta con empresas, lanza tu carrera e impulsa tu talento</p>
      <div class="hero-btns">
        <a href="/index.php?page=registro_alumno" class="cta-btn btn-main">Regístrate como Alumno</a>
        <a href="/index.php?page=registro_empresa" class="cta-btn btn-sec">¿Eres una Empresa?</a>
      </div>
    </div>
  </section>

  <!-- DESCRIPCIÓN Y BENEFICIOS -->
  <section class="section-info">
    <h2>¿Por qué elegir Jobyz?</h2>
    <div class="info-flex">
      <div>
        <p>Descubre una experiencia de empleo directa, práctica y adaptada a centros educativos.
           Jobyz es el puente real entre formación y trabajo: crea tu perfil, postúlate y recibe avisos en tiempo real.</p>
        <div class="beneficios">
          <div class="beneficio"><span>📄</span> CV digital y descarga PDF</div>
          <div class="beneficio"><span>✅</span> Empresas verificadas</div>
          <div class="beneficio"><span>🔔</span> Alertas automáticas</div>
          <div class="beneficio"><span>📊</span> Estadísticas y seguimiento</div>
        </div>
      </div>
    </div>
  </section>

  <!-- CARROUSEL DINÁMICO DE EMPRESAS -->
  <section class="carrousel-empresas">
    <h2>Empresas que confían en Jobyz</h2>
   <div class="carrousel-logos" id="carrouselLogos">
    <?php foreach ($empresas as $empresa): ?>
      <?php if (!empty($empresa['foto'])): ?>
        <div class="logo-slide">
          <img src="<?= htmlspecialchars($empresa['foto']) ?>"
              alt="<?= htmlspecialchars($empresa['nombre']) ?>"
              title="<?= htmlspecialchars($empresa['nombre']) ?>">
        </div>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
  </section>
</div>
<?php $this->stop() ?>

<?php $this->start('js') ?>
<script src="assets/js/landing.js"></script>
<?php $this->stop() ?>
