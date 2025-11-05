<?php
require_once dirname(__DIR__) . '/autoloader.php';

class LandingController {
  private $templates;

  public function __construct($templates) {
    $this ->templates = $templates;
  }

  public function mostrarLanding() {
    echo $this->templates->render('../landing', ['title' => 'Bienvenido a Jobyz']);
  }
}

?>