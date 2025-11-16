<?php
require_once dirname(__DIR__) . '/autoloader.php';

class LoginController {
  private $templates;
  private $repo;

  public function __construct($templates) {
    $this ->templates = $templates;
    $this -> repo = new RepositorioUser;
  }


  public function mostrarLogin() {
    echo $this->templates->render('../login', ['title' => 'Iniciar sesión']);
  }

  public function mostrarSeleccion(){
    echo $this->templates->render('../seleccion_registro', ['title' => '¿Quién eres?']);
  }

  public function login() {
    if (session_status() === PHP_SESSION_NONE) session_start();

    $correo = $_POST['usuario'];
    $contraseña = $_POST['contraseña'];
    $usuario = $this->repo->findUser($correo);

    $error = '';

    if ($usuario && password_verify($contraseña, $usuario->getContraseña())) {
        // Primero, comprobar el caso de EMPRESA sin validar
        if ($usuario->getRolId() == 3) {
            $repoEmpre = new RepositorioEmpresa();
            $estado = $repoEmpre->getEmpresaValidadaporUserId($usuario->getId());
            if (empty($estado) || $estado == 0) {
                $error = 'La empresa no ha sido validada por el administrador';
            }
        }
        
        // Si NO hay error, guardar en sesión y redirigir
        if (!$error) {
            $_SESSION['user_id'] = $usuario->getId();
            $_SESSION['correo']  = $usuario->getCorreo();
            $_SESSION['rol_id']  = $usuario->getRolId();
            header('Location: index.php?page=solicitudes');
            exit;
        }
    } else {
        $error = 'Usuario y/o contraseña inválidas';
    }

    // Mostrar login con error en caso de empresa NO validada o usuario/clave inválida
    echo $this->templates->render('../login', [
        'title' => 'Iniciar sesión',
        'error' => $error
    ]);
  }


  public function logout(){
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    $_SESSION = []; 
    session_unset();
    session_destroy();

  // Borra la cookie de sesión en el navegador
  if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
      $params["path"], $params["domain"],
      $params["secure"], $params["httponly"]
    );
  }
    header('Location: index.php?page=login');
    exit;
  }
}