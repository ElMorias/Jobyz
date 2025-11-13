<?php
require_once dirname(__DIR__) . '/autoloader.php';
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

class AdminController {
  private $templates;

  public function __construct($templates) {
    $this ->templates = $templates;
  }

   public function mostrarPanel() {
    if (!isset($_SESSION['correo']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
      header('Location: index.php?page=landing');
      exit;
    }else{
      echo $this->templates->render('../admin/panel_admin', ['title' => 'Panel de Control']);
    }
  }

}