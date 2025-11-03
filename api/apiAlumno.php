<?php
ob_start();
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once dirname(__DIR__) . '/autoloader.php';
header('Content-Type: application/json');

$repo = new RepositorioAlumno();

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    echo json_encode(getAlumnos($repo));
    break;

  case 'POST':
    $datos = $_POST;

    $alumno = $repo->crear($datos);
        echo json_encode([
            'status'=>'ok',
            'alumno'=>$alumno,
            'mensaje'=>'Alumno creado correctamente'
      ]);
      
    break;

  case 'DELETE':
    $datos = json_decode(file_get_contents("php://input"), true);
    if (isset($datos['id'])) {
      $ok = $repo->borrar($datos['id']);
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
    return $repo->getAlumnoCompleto($id);
  } else {
    return $repo->getTodos();
  }
}

?>
