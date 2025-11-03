<?php
require_once dirname(__DIR__) . '/autoloader.php';

class AlumnoController {
  private $repo;
  private $templates;

  public function __construct($templates) {
    $this->repo = new RepositorioAlumno();
    $this ->templates = $templates;
  }

  // @Route("tabla_alumnos", "GET")
  public function mostrarTabla() {
    echo $this->templates->render('tabla_alumnos', ['title' => 'Usuarios registrados']);
  }
}