<?php $this->layout('base_simple', ['title' => 'Ofertas']) ?>

<?php $this->start('contenido') ?>

<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $rolId = $_SESSION['rol_id'] ?? null;
?>

<div class="panel-box panel-box-nobg">

  <div class="ofertas-header">
    <h2 class="panel-titulo">Ofertas</h2>
    <?php if ($rolId == 3): // Solo empresas ?>
      <a href="index.php?page=nueva_oferta" class="btn-nueva-oferta">+ Crear oferta</a>
    <?php endif; ?>
  </div>

  <div class="ofertas-grid">
    <?php foreach($ofertas as $oferta): ?>
      <div class="card-oferta">
        <div class="empresa"><?= htmlspecialchars($oferta['empresa_nombre']) ?></div>
        <div class="titulo-oferta"><?= htmlspecialchars($oferta['titulo']) ?></div>
        <div class="descripcion"><?= htmlspecialchars($oferta['descripcion']) ?></div>
        <div class="fecha-inicio">Inicio: <?= htmlspecialchars($oferta['fechainicio']) ?></div>
        <div class="fecha-limite">Fecha límite: <?= htmlspecialchars($oferta['fechalimite']) ?></div>
        <div class="card-actions">
          <?php if ($rolId == 1): // Admin ?>
            <form action="index.php?page=ofertas" method="POST" style="display:inline;">
              <input type="hidden" name="id" value="<?= $oferta['id'] ?>">
              <button class="btn-card btn-borrar" type="submit" name="borrar">Borrar</button>
            </form>
            <a class="btn-card" href="index.php?page=solicitudes_oferta&id=<?= $oferta['id'] ?>">Solicitudes</a>
          <?php elseif ($rolId == 3): // Empresa ?>
            <a class="btn-card btn-modificar" href="index.php?page=modificar_oferta&id=<?= $oferta['id'] ?>">Modificar</a>
            <form action="index.php?page=ofertas" method="POST" style="display:inline;">
              <input type="hidden" name="id" value="<?= $oferta['id'] ?>">
              <button class="btn-card btn-borrar" type="submit" name="borrar">Borrar</button>
            </form>
          <?php elseif ($rolId == 2): // Alumno ?>
            <a class="btn-card" href="index.php?page=solicitar_oferta&id=<?= $oferta['id'] ?>">Solicitar</a>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach ?>
  </div>

  <?php if(empty($ofertas)): ?>
    <p class="ofertas-vacio">No hay ofertas actualmente.</p>
  <?php endif ?>

</div>
<?php $this->stop() ?>
