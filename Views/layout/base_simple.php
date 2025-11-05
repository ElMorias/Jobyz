<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= $this->e($title) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="assets/css/estilos.css">
  <?= $this->section('css') ?>
</head>
<body>
  <div class="wrapper">
    <!-- Header -->
    <?= $this->insert('../partials/header_simple') ?>

    <main>
      <?= $this->section('contenido') ?>
    </main>

    <!-- Footer -->
    <?= $this->insert('../partials/footer_simple') ?>
  </div>
  <?= $this->section('js') ?>
</body>
</html>

