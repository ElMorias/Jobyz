<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once dirname(__DIR__) . '/autoloader.php';
header('Content-Type: application/json');

$repo = new RepositorioAlumno();

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    echo json_encode(getAlumnos($repo));
    break;

  case 'POST':
    // Recoge JSON o formdata
    $input = file_get_contents('php://input');
    $datos = [];
    if ($input) {
      $try = json_decode($input, true);
      $datos = is_array($try) ? $try : $_POST;
    } else {
      $datos = $_POST;
    }

    // Carga masiva
    if (!empty($datos['usuarios'])) {
      $usuarios = $datos['usuarios'];
      $familia = $datos['familia'] ?? null;
      $ciclo = $datos['ciclo'] ?? null;
      $res = $repo->cargaMasiva($usuarios, $familia, $ciclo);

      echo json_encode([
        'ok' => $res['ok'] ?? false,
        'insertados' => $res['insertados'] ?? 0,
        'fallos' => $res['fallos'] ?? 0,
        'errores' => $res['fallosEmails'] ?? []
      ]);
      break;
    }

    // Edición (update)
    if (!empty($datos['id'])) {
      $id = $datos['id'];
      $validador = new Validators();
      $datosConId = array_merge($datos, ['id' => $id]);
      $errores = $validador->validarAlumnoEdicion($datosConId);

      if (!empty($errores)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'errores' => $errores]);
        break;
      }

      $ok = $repo->actualizar($id, $datos);
      if ($ok) {
        $usuario = $repo->getAlumnoCompleto($id)->toArray();
        echo json_encode(['status' => 'ok', 'mensaje' => 'Alumno actualizado', 'alumno' => $usuario]);
      } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'mensaje' => 'No se pudo actualizar']);
      }
      break;
    }

    // Registro (alta normal)
    $validador = new Validators();
    $errores = $validador->validarAlumnoRegistro($datos);

    if (!empty($errores)) {
      echo json_encode(['status' => 'error', 'errores' => $errores]);
      exit;
    }

    $alumno = $repo->crear($datos);

    if ($alumno) {
      MailerService::enviarBienvenida($datos['correo'], $datos['nombre']);
      echo json_encode([
        'status' => 'ok',
        'alumno' => $alumno->toArray(),
        'mensaje' => 'Alumno creado correctamente'
      ]);
    } else {
      echo json_encode(['status' => 'error', 'errores' => ['Error al registrar el alumno.']]);
    }
    break;

  case 'DELETE':
    $datos = json_decode(file_get_contents("php://input"), true);
    if (isset($datos['id'])) {
      $ok = $repo->borrarPorAlumnoId($datos['id']);
      if ($ok) {
        echo json_encode(['status' => 'ok', 'mensaje' => 'Alumno borrado']);
      } else {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'mensaje' => 'No se pudo borrar']);
      }
    } else {
      http_response_code(400);
      echo json_encode(['error' => 'Falta el ID']);
    }
    break;

  default:
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
}

function getAlumnos($repo) {
  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    return $repo->getAlumnoCompleto($id)->toArray();
  } elseif (isset($_GET['yo'])) {
    $userId = $_SESSION['user_id'];
    $id = $repo->getAlumnoIdPorUserId($userId);

    if (!$userId) {
      http_response_code(401);
      return ['error' => 'No autenticado'];
    }

    $alumno = $repo->getAlumnoCompleto($id)->toArray();

    $repoEstudios = new RepositorioEstudio();
    $estudios = $repoEstudios->getPorAlumnoIdConNombres($id);
    $alumno['estudios'] = $estudios;

    return $alumno;
  } else {
    return $repo->getTodos();
  }
}
