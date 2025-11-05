<?php
require_once dirname(__DIR__) . '/autoloader.php';
  use League\Plates\Engine;

// no va con ../ poque se ejecuta desde index.php
$templates = new Engine('Views/layout');

// Ruta simple con parámetro GET
$page = $_GET['page'] ?? 'landing';

switch ($page) {
  case 'login':
    $controller = new LoginController($templates);
    $controller->mostrarLogin();
    break;
  case 'registro_alumno':
    $controller = new AlumnoController($templates);
    $controller->registrarAlumno();
    break;
  case 'registro_empresa':
    $controller = new EmpresaController($templates);
    $controller->registrarEmpresa();
    break;
  case 'seleccion_registro':
    $controller = new LoginController($templates);
    $controller->mostrarSeleccion();
    break;
  case 'tabla_alumnos':
    $controller = new AlumnoController($templates);
    $controller->mostrarTabla();
    break;
  case 'tabla_empresas':
    $controller = new EmpresaController($templates);
    $controller->mostrarTabla();
    break;
  case 'crear_empresa':
    $controller = new EmpresaController($templates);
    $controller->crearEmpresa();
    break;
  case 'detalles_empresa':
    $controller = new EmpresaController($templates);
    $controller->verDetallesEmpresa();
    break;
  case 'editar_empresa':
    $controller = new EmpresaController($templates);
    $controller->editarEmpresa();
    break;
  case 'borrar_empresa':
    $controller = new EmpresaController($templates);
    $controller->borrarEmpresa();
    break;
  case 'validar_empresa':
    $controller = new EmpresaController($templates);
    $controller->validarEmpresa();
    break;


  case 'landing':
  default:
    $controller = new LandingController($templates);
    $controller->mostrarLanding();
    break;
}

?>