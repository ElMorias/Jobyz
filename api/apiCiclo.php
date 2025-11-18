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

// Devuelve vacío si no se especifica familia_id
if (!isset($_GET['familia_id'])) {
    echo json_encode([]);
    exit;
}

$repo = new RepositorioCiclo();
echo json_encode($repo->getByFamilia($_GET['familia_id']));
