<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . '/autoloader.php';
header('Content-Type: application/json');

$repo = new RepositorioAlumno();

/**
 * API REST: Controlador central para la entidad Alumno.
 * - GET:      Consulta (detalle, propio, listado)
 * - POST:     Notificación, carga masiva, edición, alta individual
 * - DELETE:   Borrado físico de alumno/usuario
 */
switch ($_SERVER['REQUEST_METHOD']) {

    // === CONSULTA DE DATOS (DETALLE, PROPIO O LISTADO) ===
    case 'GET':
        /*
            Solo exige autenticación cuando se trata de una consulta privada:
            - Detalle por ID (usado en edición/detalle)
            - Consulta "yo" (perfil propio)
            El resto (listado global para la tabla de administración, o para selects) NO necesita token.
        */
        if (isset($_GET['id']) || isset($_GET['yo'])) {
            requireToken();
        }
        echo json_encode(getAlumnos($repo));
        break;

    // === ACCIONES, ALTA, EDICIÓN Y CARGA MASIVA ===
    case 'POST':
        $input = file_get_contents('php://input');
        $datos = [];
        if ($input) {
            $try = json_decode($input, true);
            $datos = is_array($try) ? $try : $_POST;
        } else {
            $datos = $_POST;
        }

        // --- 1. Notificar alumno NO validado (protegido, requiere token) ---
        if (!empty($datos['accion']) && $datos['accion'] === 'notificar_no_validado' && !empty($datos['id'])) {
            requireToken();
            $alumno = $repo->getAlumnoCompleto($datos['id']);
            $ok = $alumno ? MailerService::enviarAvisoNoValidado($alumno->getCorreo(), $alumno->getNombre()) : false;
            echo json_encode(['ok' => $ok === true]);
            exit;
        }

        // --- 2. Carga masiva de usuarios (protegido, requiere token) ---
        if (!empty($datos['usuarios'])) {
            requireToken();
            $usuarios = $datos['usuarios'];
            $familia = $datos['familia'] ?? null;
            $ciclo = $datos['ciclo'] ?? null;
            $res = $repo->cargaMasiva($usuarios, $familia, $ciclo);

            // Envía email a cada usuario dado de alta correctamente
            if (!empty($res['alumnos'])) {
                foreach ($res['alumnos'] as $a) {
                    MailerService::enviarBienvenidaMasiva($a['correo'], $a['nombre'], $a['correo'], 'Temporal1234');
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

        // --- 3. Edición de alumno EXISTENTE (protegido, requiere token) ---
        if (!empty($datos['id']) && empty($datos['accion'])) {
            requireToken();
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

        // --- 4. Alta INDIVIDUAL (NO requiere token, permite registro público) ---
        $validador = new Validators();
        $errores = $validador->validarAlumnoRegistro($datos);

        if (!empty($errores)) {
            echo json_encode(['status' => 'error', 'errores' => $errores]);
            exit;
        }

        $alumno = $repo->crear($datos);

        if ($alumno) {
            MailerService::enviarBienvenida($alumno->getCorreo(), $alumno->getNombre());
            echo json_encode([
                'status' => 'ok',
                'alumno' => $alumno->toArray(),
                'mensaje' => 'Alumno creado correctamente'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'errores' => ['Error al registrar el alumno.']]);
        }
        exit;

    // === BORRAR UN ALUMNO POR ID ===
    case 'DELETE':
        requireToken();
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

    // === PETICIÓN NO SOPORTADA ===
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
}

/**
 * Helper para validar tokens en endpoints que sí requieren autenticación.
 */
function requireToken() {
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
}

/**
 * Consulta flexible de alumnos:
 * - id  : devuelve alumno como array (protegido)
 * - yo  : devuelve alumno propio + estudios (protegido)
 * - else: listado general (público o solo para administración, según tu lógica de frontend)
 */
function getAlumnos($repo) {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $alumno = $repo->getAlumnoCompleto($id);
        if (!$alumno) return null;

        $array = $alumno->toArray();
        $repoEstudios = new RepositorioEstudio();
        $array['estudios'] = $repoEstudios->getPorAlumnoIdConNombres($id);

        return $array;
    } elseif (isset($_GET['yo'])) {
        $userId = $_SESSION['user_id'] ?? null;
        $id = $repo->getAlumnoIdPorUserId($userId);

        if (!$userId) {
            http_response_code(401);
            return ['error' => 'No autenticado'];
        }

        $alumno = $repo->getAlumnoCompleto($id);
        if (!$alumno) return null;

        $array = $alumno->toArray();
        $repoEstudios = new RepositorioEstudio();
        $array['estudios'] = $repoEstudios->getPorAlumnoIdConNombres($id);

        return $array;
    } else {
        $todos = array_map(fn($a) => $a->toArray(), $repo->getTodos());
        $noValidados = array_map(fn($a) => $a->toArray(), $repo->getNoValidados());

        return [
            'alumnos' => $todos,
            'noValidados' => $noValidados
        ];
    }
}
