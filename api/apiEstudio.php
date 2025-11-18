<?php
require_once dirname(__DIR__) . '/autoloader.php';
header('Content-Type: application/json');

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

$repo = new RepositorioEstudio();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'DELETE':
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['id'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'mensaje' => 'Falta el id del estudio']);
            exit;
        }

        $id = $input['id'];
        $ok = $repo->borrarPorId($id);
        if ($ok) {
            echo json_encode(['status' => 'ok', 'mensaje' => 'Estudio borrado']);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'mensaje' => 'No se ha podido borrar el estudio']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}
