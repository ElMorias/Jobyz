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
      // Control especial: empresa sin validar
      if ($usuario->getRolId() == 3) {
        $repoEmpre = new RepositorioEmpresa();
        $estado = $repoEmpre->getEmpresaValidadaporUserId($usuario->getId());
        if (empty($estado) || $estado == 0) {
          $error = 'La empresa no ha sido validada por el administrador';
        }
      }

      if (!$error) {
          $_SESSION['user_id'] = $usuario->getId();
          $_SESSION['correo']  = $usuario->getCorreo();
          $_SESSION['rol_id']  = $usuario->getRolId();

          // --- TOKEN LOGIC: genera, guarda en BD y lo envía al navegador ---
          require_once dirname(__DIR__) . '/helpers/Security.php';
          $security = new Security();
          $token = $security->createAndStoreToken($usuario->getId(), $usuario->getCorreo());
          $userId = $usuario->getId();

          // Si el login es normal (no AJAX), pon el token en JS para que el frontend lo guarde
          echo "<script>
              sessionStorage.setItem('token', '".htmlspecialchars($token)."');
              sessionStorage.setItem('user_id', '".htmlspecialchars($userId)."');
              window.location.href = 'index.php?page=solicitudes';
          </script>";
          exit;

          // Si usas login AJAX, en vez del script anterior haz:
          // echo json_encode(['token' => $token, 'user_id' => $userId]);
          // exit;
      }
    } else {
        $error = 'Usuario y/o contraseña inválidas';
    }

    echo $this->templates->render('../login', [
        'title' => 'Iniciar sesión',
        'error' => $error
    ]);
  }



  public function logout() {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    // --- Lógica para invalidar token seguro ---
    $headers = getallheaders();
    $tokenHeader = $headers['Authorization'] ?? '';
    $matches = [];
    $token = (preg_match('/Bearer\s+(\S+)/', $tokenHeader, $matches)) ? $matches[1] : null;
    $user_id = $headers['X-USER-ID'] ?? ($_SESSION['user_id'] ?? null);

    if ($user_id && $token) {
      $security = new Security();
      $security->invalidateToken($user_id, $token); // Borra el token de la BD
    }

    // Limpieza de sesión PHP y cookie
    $_SESSION = [];
    session_unset();
    session_destroy();

    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
      );
    }
    // Puedes devolver un JSON o redirección según cómo llames el logout (AJAX o normal)
    // Para AJAX:
    // echo json_encode(['ok' => true]);
    // Forzado a recargar login:
    header('Location: index.php?page=login');
    exit;
  }
}