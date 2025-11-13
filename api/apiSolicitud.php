<?php
require_once dirname(__DIR__) . '/autoloader.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
$rol = $_SESSION['rol_id'] ?? null;
$correo = $_SESSION['correo'] ?? null;

$repo = new RepositorioSolicitudes();
$alumnorepo = new RepositorioAlumno();

// ----------- Control de acciones POST (aceptar/rechazar/eliminar) --------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $accion = $input['accion'] ?? '';
    $id = $input['id'] ?? null;
    
    if ($accion === 'eliminar' && $id) {
        $repo->eliminar($id);
        echo json_encode(['resultado' => 'ok']);
        exit;
    }
    if ($accion === 'aceptar' && $id) {
        $repo->aceptar($id); 
        echo json_encode(['resultado' => 'ok']);
        exit;
    }
    if ($accion === 'rechazar' && $id) {
        $repo->rechazar($id);
        echo json_encode(['resultado' => 'ok']);
        exit;
    }
    // Respuesta por defecto
    echo json_encode(['resultado' => 'error', 'mensaje' => 'Acción no válida']);
    exit;
}

// ----------- Respuesta GET/consulta AJAX ----------------
if ($rol == 1) {
    $lista = $repo->todas();
} elseif ($rol == 2) {
    $alumno_id = $alumnorepo->getAlumnoIdPorUserId($_SESSION['user_id']);
    $lista = $repo->deAlumno($alumno_id);
} elseif ($rol == 3) {
    $userRepo = new RepositorioUser();
    $empresaRepo = new RepositorioEmpresa();
    $user_id = $userRepo->getIdPorCorreo($correo);
    if ($user_id) {
        $empresa_id = $empresaRepo->getEmpresaIdPorUserId($user_id);
    }
    $lista = $repo->deEmpresa($empresa_id);
} else {
    $lista = [];
}

$json = array_map(fn($s) => $s->toArray(), $lista);
echo json_encode([
    'rol' => $rol,
    'solicitudes' => $json
]);
?>