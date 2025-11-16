
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= $this->e($title) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="assets/css/estilos.css">
  <script src="assets\js\desplegable_header.js"></script>
 
  <!-- Sección para CSS adicional -->
  <?= $this->section('css') ?>
</head>

<body>
  <div class="wrapper">
    <!-- Headerr -->
    <?php
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    
      $rolId = isset($_SESSION['rol_id']) ? $_SESSION['rol_id'] : null;

      if (!$rolId) {
        echo $this->insert('../partials/header');
      } elseif ($rolId == 1) {
        echo $this->insert('../partials/header_admin');
      } else {
        echo $this->insert('../partials/header_usuarios');
      }
    ?>

  <main>
    <!-- contenido -->
    <?= $this->section('welcome') ?>
    <?= $this->section('listado') ?>
   <?= $this->section('contenido') ?>
  </main>
    <!-- Footer -->
    <?= $this->insert('../partials/footer') ?>
  </div>
  <!-- Sección para Js -->
  <?= $this->section('js') ?>
</body>
</html>
