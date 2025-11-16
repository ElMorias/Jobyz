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
      
      if (is_array($try)) {
          $datos = $try;
      } else {
          $datos = $_POST;
      }
    } else {
      $datos = $_POST;
    }

    
    if (!empty($datos['usuarios'])) {
      $usuarios = $datos['usuarios'];
      $familia = $datos['familia'] ?? null;
      $ciclo = $datos['ciclo'] ?? null;
      $res = $repo->cargaMasiva($usuarios, $familia, $ciclo);

      echo json_encode([
          'ok'         => $res['ok'] ?? false,
          'insertados' => $res['insertados'] ?? 0,
          'fallos'     => $res['fallos'] ?? 0,
          'errores'    => $res['fallosEmails'] ?? []
      ]);
      break;
    }

    // Edición (update)
    if (!empty($datos['id'])) {
      $id = $datos['id'];
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
    $alumno = $repo->crear($datos);
    echo json_encode([
      'status'=>'ok',
      'alumno'=>$alumno->toArray(),
      'mensaje'=>'Alumno creado correctamente'
    ]);
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

  case 'PUT':
    $datos = json_decode(file_get_contents("php://input"), true);
    if (isset($datos['id'])) {
      $id = $datos['id'];
      $ok = $repo->actualizar($id, $datos);
      if ($ok) {
        echo json_encode(['status' => 'ok', 'mensaje' => 'Alumno actualizado']);
      } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'mensaje' => 'No se pudo actualizar']);
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
        // Solo para perfil propio
        $userId = $_SESSION['user_id'];
        $id = $repo->getAlumnoIdPorUserId($userId);
        if (!$userId) {
            http_response_code(401); // No autorizado
            return ['error' => 'No autenticado'];
        }
        //datos básicos
        $alumno = $repo->getAlumnoCompleto($id)->toArray();
        
        // ---  estudios con nombres ---
        $repoEstudios = new RepositorioEstudio();
        $estudios = $repoEstudios->getPorAlumnoIdConNombres($id); // método con JOIN a ciclo y familia
        $alumno['estudios'] = $estudios;
        
        return $alumno; // Ahora el alumno tiene array 'estudios' listo para el frontend
    } else {
        return $repo->getTodos();
    }
}



?>
