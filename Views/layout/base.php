
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= $this->e($title) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="assets/css/estilos.css">
 

  <!-- Sección para CSS adicional -->
  <?= $this->section('css') ?>
</head>

<body>
  <div class="wrapper">
    <!-- Headerr -->
    <?= $this->insert('../partials/header') ?>
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
