<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once dirname(__DIR__) . '/autoloader.php';
header('Content-Type: application/json');

// (OPCIONAL) solo admins
$headers = getallheaders();
$tokenHeader = $headers['Authorization'] ?? '';
$matches = [];
$token = (preg_match('/Bearer\s+(\S+)/', $tokenHeader, $matches)) ? $matches[1] : null;
$user_id = $headers['X-USER-ID'] ?? null;
// Suponiendo que tienes un método para comprobar si es admin
$security = new Security();
if (!$token || !$user_id || !$security->validateToken($user_id, $token) || $_SESSION['rol_id'] != 1) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$repoAlumno = new RepositorioAlumno();
$repoEmpresa = new RepositorioEmpresa();
$repoOferta = new RepositorioOfertas();
$repoCiclo = new RepositorioCiclo();

// % alumnos y empresas
$totalAlumnos = $repoAlumno->contarTodos();
$totalEmpresas = $repoEmpresa->contarTodas();
$totalUsuarios = $totalAlumnos + $totalEmpresas;
$porcentajeAlumnos = $totalUsuarios > 0 ? round(100 * $totalAlumnos / $totalUsuarios, 2) : 0;
$porcentajeEmpresas = $totalUsuarios > 0 ? round(100 * $totalEmpresas / $totalUsuarios, 2) : 0;

// Top ciclos por cantidad de ofertas asociadas
$topCiclosOfertas = $repoOferta->topCiclosEnOfertas(); // Devuelve array: ['DAW'=>12, 'DAM'=>10,...]

// Top ciclos con más alumnos
$topCiclosAlumnos = $repoCiclo->topCiclosPorAlumnos(); // Devuelve array: ['DAW'=>20, 'DAM'=>15,...]

echo json_encode([
    "porcentajes" => [
        "Alumnos" => $porcentajeAlumnos,
        "Empresas" => $porcentajeEmpresas,
    ],
    "topOfertasCiclo" => $topCiclosOfertas,
    "topAlumnosCiclo" => $topCiclosAlumnos
]);
