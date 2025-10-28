<?php
use League\Plates\Engine;

// no va con ../ poque se ejecuta desde index.php
$templates = new Engine('Views');

// Ruta simple con parámetro GET
$page = $_GET['page'] ?? 'landing';

switch ($page) {
  case 'login':
    echo $templates->render('login', ['title' => 'Iniciar sesión']);
    break;
  case 'registro_alumno':
    echo $templates->render('registro_alumno', ['title' => 'Registro de alumno']);
    break;
  case 'registro_empresa':
    echo $templates->render('registro_empresa', ['title' => 'Registro de usuario']);
    break;
  case 'seleccion_registro':
    echo $templates->render('seleccion_registro', ['title' => '¿Quién eres?']);
    break;

  case 'landing':
  default:
    echo $templates->render('landing', ['title' => 'Jobyz – Portal de Empleo']);
    break;
}

?>