<?php
// Incluimos el autoloader de clases para cargar automáticamente los repositorios y helpers necesarios
require_once dirname(__DIR__) . '/autoloader.php';

// Iniciamos la sesión PHP si aún no está activa (necesario para acceder a variables de sesión)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Extraemos los headers de la petición HTTP para capturar el token y el user_id enviados desde el frontend
$headers = getallheaders();
$tokenHeader = $headers['Authorization'] ?? '';
$matches = [];
// Extraemos el token 'Bearer' del header Authorization (formato "Bearer asdf1234...")
$token = (preg_match('/Bearer\s+(\S+)/', $tokenHeader, $matches)) ? $matches[1] : null;
// Obtenemos el user_id desde el header personalizado (o de la sesión)
$user_id = $headers['X-USER-ID'] ?? null;

// Instanciamos el helper de seguridad para validación de tokens
$security = new Security();

// Validamos que el usuario tenga token correcto y vigente; si no, respondemos con error 401 y cortamos la ejecución
if (!$token || !$user_id || !$security->validateToken($user_id, $token)) {
    http_response_code(401);
    echo json_encode(['error' => 'Token inválido o ha expirado']);
    exit;
}

// Indicamos que todas las respuestas serán JSON
header('Content-Type: application/json');

// Recuperamos el rol y correo del usuario desde la sesión (esto determina sus permisos)
$rol = $_SESSION['rol_id'] ?? null;
$correo = $_SESSION['correo'] ?? null;

// Instanciamos los repositorios principales de solicitudes y alumnos
$repo = new RepositorioSolicitudes();
$alumnorepo = new RepositorioAlumno();

// --- GESTIÓN DE ACCIONES VIA POST (aceptar, rechazar, eliminar) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decodificamos el body recibido (asumimos JSON desde el frontend)
    $input = json_decode(file_get_contents('php://input'), true);
    $accion = $input['accion'] ?? '';
    $id = $input['id'] ?? null;

    // Accion: Eliminar una solicitud
    if ($accion === 'eliminar' && $id) {
        $repo->eliminar($id);
        echo json_encode(['resultado' => 'ok']);
        exit;
    }

    // Accion: Aceptar una solicitud y notificar al alumno por email
    if ($accion === 'aceptar' && $id) {
        $repo->aceptar($id); // Marca la solicitud como aceptada
        $solicitud = $repo->obtenerPorId($id); // Obtenemos info de la solicitud para el email
        $empresaRepo = new RepositorioEmpresa();
        $empresaData = $empresaRepo->getPorId($solicitud->empresa_id);

        // Enviamos email de aceptación al alumno, usando datos de empresa y oferta
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

    // Accion: Rechazar una solicitud y notificar al alumno por email
    if ($accion === 'rechazar' && $id) {
        $repo->rechazar($id); // Marca la solicitud como rechazada
        $solicitud = $repo->obtenerPorId($id);
        $empresaRepo = new RepositorioEmpresa();
        $empresaData = $empresaRepo->getPorId($solicitud->empresa_id);

        // Enviamos email de rechazo al alumno
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

    // Si la acción no es válida, devolvemos error genérico (por robustez)
    echo json_encode(['resultado' => 'error', 'mensaje' => 'Acción no válida']);
    exit;
}

// --- GESTIÓN DE CONSULTAS AJAX (GET) SEGÚN ROL DEL USUARIO ---

/*
    - Rol 1 (Admin): puede ver todas las solicitudes del sistema.
    - Rol 2 (Alumno): solo puede ver sus propias solicitudes.
    - Rol 3 (Empresa): ve las solicitudes a sus ofertas.
*/

// Admin: listado completo de todas las solicitudes
if ($rol == 1) {
    $lista = $repo->todas();

// Alumno: solicita el id correspondiente y muestra solo sus solicitudes
} elseif ($rol == 2) {
    $alumno_id = $alumnorepo->getAlumnoIdPorUserId($_SESSION['user_id']);
    $lista = $repo->deAlumno($alumno_id);

// Empresa: obtiene user_id y empresa_id, muestra sus solicitudes
} elseif ($rol == 3) {
    $userRepo = new RepositorioUser();
    $empresaRepo = new RepositorioEmpresa();
    $user_id = $userRepo->getIdPorCorreo($correo);
    $empresa_id = $user_id ? $empresaRepo->getEmpresaIdPorUserId($user_id) : null;
    $lista = $repo->deEmpresa($empresa_id);

// Si el rol es otro, devuelve lista vacía
} else {
    $lista = [];
}

// Convertimos la lista de objetos a arrays, y la devolvemos junto al rol en formato JSON
$json = array_map(fn($s) => $s->toArray(), $lista);
echo json_encode([
    'rol' => $rol,
    'solicitudes' => $json
]);
