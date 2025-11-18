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

    // 1. Notificar alumno no validado (ACCIÓN ESPECIAL)
    if (!empty($datos['accion']) && $datos['accion'] === 'notificar_no_validado' && !empty($datos['id'])) {
        $alumno = $repo->getAlumnoCompleto($datos['id'])->toArray(); // array asociativo
        $ok = MailerService::enviarAvisoNoValidado($alumno['correo'], $alumno['nombre']);
        echo json_encode(['ok' => $ok === true]);
        exit;
    }

    // 2. Carga masiva usuarios
    if (!empty($datos['usuarios'])) {
        $usuarios = $datos['usuarios'];
        $familia = $datos['familia'] ?? null;
        $ciclo = $datos['ciclo'] ?? null;
        $res = $repo->cargaMasiva($usuarios, $familia, $ciclo);

        // Envía email a cada nuevo usuario insertado
        if (!empty($res['insertados'])) {
            foreach ($res['alumnos'] as $u) {
                MailerService::enviarBienvenidaMasiva($u['correo'], $u['nombre'], $u['correo'], 'Temporal1234');
            }
        }

        echo json_encode([
            'ok' => $res['ok'] ?? false,
            'insertados' => $res['insertados'] ?? 0,
            'fallos' => $res['fallos'] ?? 0,
            'errores' => $res['fallosEmails'] ?? []
        ]);
        exit;
    }


    // 3. Edición (update alumno existente)
    if (!empty($datos['id']) && empty($datos['accion'])) { // Solo si NO es acción especial
        $id = $datos['id'];
        $validador = new Validators();
        $datosConId = array_merge($datos, ['id' => $id]);
        $errores = $validador->validarAlumnoEdicion($datosConId);

        if (!empty($errores)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'errores' => $errores]);
            exit;
        }

        $ok = $repo->actualizar($id, $datos);
        if ($ok) {
            $usuario = $repo->getAlumnoCompleto($id)->toArray();
            echo json_encode(['status' => 'ok', 'mensaje' => 'Alumno actualizado', 'alumno' => $usuario]);
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'mensaje' => 'No se pudo actualizar']);
        }
        exit;
    }

    // 4. Registro (alta normal, si no hay id ni acción especial ni carga masiva)
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
    exit;

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
    $todos = $repo->getTodos();
    $noValidados = $repo->getNoValidados(); // Debes tener este método en tu repo

    return [
      'alumnos' => $todos,
      'noValidados' => $noValidados
    ];
  }
}
