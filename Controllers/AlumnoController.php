<?php
require_once dirname(__DIR__) . '/autoloader.php';
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

class AlumnoController {
  private $repo;
  private $templates;

  public function __construct($templates) {
    $this->repo = new RepositorioAlumno();
    $this ->templates = $templates;
  }

  // @Route("tabla_alumnos", "GET")
  public function mostrarTabla() {
    if (!isset($_SESSION['correo']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
      header('Location: index.php?page=landing');
      exit;
    }else{
      echo $this->templates->render('../admin/tabla_alumnos', ['title' => 'Usuarios registrados']);
    }
  }

  public function registrarAlumno(){
    echo $this->templates->render('../registro_alumno', ['title' => 'Registro de alumno']);
  }

  public function mostrarPerfil(){
    echo $this->templates->render('../perfil_alumno', ['title' => 'Perfil del alumno']);
  }

  public function exportarAlumnoPDF() {
    $alumnos = $this->repo->getTodos(); // Reemplaza por tu método real
    PdfAlumnos::exportAlumnos($alumnos);
    exit;
  }
}