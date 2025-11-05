<?php
require_once dirname(__DIR__) . '/autoloader.php';

class LoginController {
  private $templates;

  public function __construct($templates) {
    $this ->templates = $templates;
  }


  public function mostrarLogin() {
    echo $this->templates->render('../login', ['title' => 'Iniciar sesión']);
  }

  public function mostrarSeleccion(){
    echo $this->templates->render('../seleccion_registro', ['title' => '¿Quién eres?']);
  }
}