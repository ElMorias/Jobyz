<?php
require_once dirname(__DIR__) . '/autoloader.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$headers = getallheaders();
$tokenHeader = $headers['Authorization'] ?? '';
$matches = [];
$token = (preg_match('/Bearer\s+(\S+)/', $tokenHeader, $matches)) ? $matches[1] : null;
$user_id = $headers['X-USER-ID'] ?? null;

$security = new Security();

if (!$token || !$user_id || !$security->validateToken($user_id, $token)) {
    http_response_code(401);
    echo json_encode(['error' => 'Token inválido o ha expirado']);
    exit;
}

header('Content-Type: application/json');
$rol = $_SESSION['rol_id'] ?? null;
$correo = $_SESSION['correo'] ?? null;

$repo = new RepositorioSolicitudes();
$alumnorepo = new RepositorioAlumno();

// Acciones POST: aceptar, rechazar, eliminar
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
        $solicitud = $repo->obtenerPorId($id);
        $empresaRepo = new RepositorioEmpresa();
        $empresaData = $empresaRepo->getPorId($solicitud->empresa_id);

        MailerService::enviarAceptacionSolicitud(
            $solicitud->alumno_email,
            $solicitud->alumno_nombre,
            $solicitud->oferta_titulo,
            $empresaData->getCorreo(),
            $empresaData->getNombre()
        );

        echo json_encode(['resultado' => 'ok']);
        exit;
    }
    if ($accion === 'rechazar' && $id) {
        $repo->rechazar($id);
        $solicitud = $repo->obtenerPorId($id);
        $empresaRepo = new RepositorioEmpresa();
        $empresaData = $empresaRepo->getPorId($solicitud->empresa_id);

        MailerService::enviarRechazoSolicitud(
            $solicitud->alumno_email,
            $solicitud->alumno_nombre,
            $solicitud->oferta_titulo,
            $empresaData->getCorreo(),
            $empresaData->getNombre()
        );

        echo json_encode(['resultado' => 'ok']);
        exit;
    }
    echo json_encode(['resultado' => 'error', 'mensaje' => 'Acción no válida']);
    exit;
}

// Consultas GET AJAX, adaptadas a rol
if ($rol == 1) {
    $lista = $repo->todas();
} elseif ($rol == 2) {
    $alumno_id = $alumnorepo->getAlumnoIdPorUserId($_SESSION['user_id']);
    $lista = $repo->deAlumno($alumno_id);
} elseif ($rol == 3) {
    $userRepo = new RepositorioUser();
    $empresaRepo = new RepositorioEmpresa();
    $user_id = $userRepo->getIdPorCorreo($correo);
    $empresa_id = $user_id ? $empresaRepo->getEmpresaIdPorUserId($user_id) : null;
    $lista = $repo->deEmpresa($empresa_id);
} else {
    $lista = [];
}
$json = array_map(fn($s) => $s->toArray(), $lista);
echo json_encode([
    'rol' => $rol,
    'solicitudes' => $json
]);
