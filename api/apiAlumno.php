<?php
require_once '../controllers/AlumnoController.php';

$controller = new AlumnoController();

// GET → obtener todos o uno por ID
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (isset($_GET['id'])) {
    $controller->apiGetAlumno($_GET['id']);
  } else {
    $controller->apiGetTodos();
  }
}

// POST → crear alumno
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $datos = json_decode(file_get_contents('php://input'), true);
  $controller->apiCrearAlumno($datos);
}

// DELETE → borrar alumno
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
  $datos = json_decode(file_get_contents('php://input'), true);
  if (isset($datos['id'])) {
    $controller->apiBorrarAlumno($datos['id']);
  } else {
    http_response_code(400);
    echo json_encode(['error' => 'Falta el ID']);
  }
}