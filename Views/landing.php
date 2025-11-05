<?php $this->layout('base', ['title' => 'Jobyz – Portal de Empleo']) ?>

<?php $this->start('css') ?>
<?php $this->stop() ?>


<?php $this->start('contenido') ?>

<section class="hero">
  <div class="hero-text">
    <h1>Tu próximo desafío profesional empieza aquí</h1>
    <p>Conecta con empresas, postúlate a ofertas y haz crecer tu carrera desde el aula.</p>
    <a href="/Jobyz/index.php?page=registro_alumno" class="cta-btn">Únete a Jobyz</a> <a href="/Jobyz/index.php?page=registro_empresa" class="cta-btn">¿Eres una empresa?</a>
  </div>
  <div class="hero-image">
    
  </div>
</section>

<section id="descripcion" class="descripcion">
  <h2>¿Qué es Jobyz?</h2>
  <p>Jobyz conecta alumnos con empresas de forma ágil, clara y directa. Crea tu perfil, postúlate y recibe notificaciones automáticas.</p>
  <div class="beneficios">
    <div class="beneficio">CV en PDF</div>
    <div class="beneficio">Empresas validadas</div>
    <div class="beneficio">Notificaciones automáticas</div>
    <div class="beneficio">Estadísticas de actividad</div>
  </div>
</section>

<section id="empresas" class="empresas">
  <h2>Últimas empresas registradas</h2>
  <ul class="lista-empresas">
    <li>NTT DATA</li>
    <li>AMSystem</li>
    <li>Nter Tech Services</li>
    <li>Grupo Ibersys</li>
    <li>Soltel</li>
    <li>Indra</li>
  </ul>
  <a href="#" class="ver-todas">Ver todas las empresas</a>
</section>
<?php $this->stop() ?>