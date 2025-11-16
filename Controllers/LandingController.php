<?php
require_once dirname(__DIR__) . '/autoloader.php';
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}


class LandingController {
  private $templates;

  public function __construct($templates) {
    $this ->templates = $templates;
  }

  public function mostrarLanding() {
    if(isset($_SESSION['correo'])){
      echo $this->templates->render('../landing_logueado', ['title' => 'Bienvenido a Jobyz']);
    }else{
      echo $this->templates->render('../landing', ['title' => 'Bienvenido a Jobyz']);
    }
    
  }
}

?>