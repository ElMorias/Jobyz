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

        // --- 1. Notificar alumno NO validado ---
        if (!empty($datos['accion']) && $datos['accion'] === 'notificar_no_validado' && !empty($datos['id'])) {
            $alumno = $repo->getAlumnoCompleto($datos['id']);
            $ok = $alumno ? MailerService::enviarAvisoNoValidado($alumno->getCorreo(), $alumno->getNombre()) : false;
            echo json_encode(['ok' => $ok === true]);
            exit;
        }

        // --- 2. Carga masiva de usuarios ---
        if (!empty($datos['usuarios'])) {
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

        // --- 3. Edición de alumno EXISTENTE ---
        if (!empty($datos['id']) && empty($datos['accion'])) {
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

        // --- 4. Alta INDIVIDUAL ---
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
 * Consulta flexible de alumnos:
 * - id  : devuelve alumno como array
 * - yo  : devuelve alumno propio (array) + estudios con nombres
 * - else: listado (convertido siempre a arrays planos)
 */
function getAlumnos($repo) {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $alumno = $repo->getAlumnoCompleto($id);
        return $alumno ? $alumno->toArray() : null;
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

        // Adjunta estudios detallados con nombres
        $repoEstudios = new RepositorioEstudio();
        $array['estudios'] = $repoEstudios->getPorAlumnoIdConNombres($id);

        return $array;
    } else {
        // Listado: convertir ambos a arrays asociativos listos para frontend
        $todos = array_map(fn($a) => $a->toArray(), $repo->getTodos());
        $noValidados = array_map(fn($a) => $a->toArray(), $repo->getNoValidados());

        return [
            'alumnos' => $todos,
            'noValidados' => $noValidados
        ];
    }
}
