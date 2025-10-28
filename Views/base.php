
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= $this->e($title) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="assets/css/base.css">
  <link rel="stylesheet" href="assets/css/responsive.css">

  <!-- Sección para CSS adicional -->
  <?= $this->section('css') ?>
</head>

<body>
  <!-- Headerr -->
  <?= $this->insert('partials/header') ?>

  <!-- contenido -->
  <?= $this->section('welcome') ?>
  <?= $this->section('listado') ?>
  <?= $this->section('contenido') ?>

  <!-- Footer -->
  <?= $this->insert('partials/footer') ?>

  <!-- Sección para Js -->
  <?= $this->section('js') ?>
</body>
</html>
