<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= $this->e($title) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="assets/css/estilos.css">
  <script src="assets\js\desplegable_header.js"></script>
  <?= $this->section('css') ?>
</head>
<body>
  <div class="wrapper">
    <!-- Header -->
    <?php
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

      $rolId = isset($_SESSION['rol_id']) ? $_SESSION['rol_id'] : null;

      if (!$rolId) {
        echo $this->insert('../partials/header_simple');
      } elseif ($rolId == 1) {
        echo $this->insert('../partials/header_admin');
      } else {
        echo $this->insert('../partials/header_usuarios');
      }
    ?>

  <main>
    <?php if ($rolId == 1): ?>
      <div class="admin-layout">
        <aside class="admin-menu">
          <nav>
            <ul>
              <li><a href="index.php?page=tabla_alumnos">Panel alumnos</a></li>
              <li><a href="index.php?page=tabla_empresas">Panel empresas</a></li>
              <li><a href="index.php?page=ofertas">Ofertas</a></li>
              <li><a href="index.php?page=solicitudes">Solicitudes</a></li>
            </ul>
          </nav>
        </aside>
        <section class="contenido">
          <?= $this->section('contenido') ?>
        </section>
      </div>
    <?php else: ?>
      <section class="contenido">
        <?= $this->section('contenido') ?>
      </section>
    <?php endif; ?>
  </main>


    <!-- Footer -->
    <?= $this->insert('../partials/footer') ?>
  </div>
  <?= $this->section('js') ?>
</body>
</html>

