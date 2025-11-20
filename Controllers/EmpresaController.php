<?php
require_once dirname(__DIR__) . '/autoloader.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Controlador OO para empresa. Trabaja siempre con objetos Empresa hasta el render.
 */
class EmpresaController {
    private $repo;
    private $templates;

    public function __construct($templates) {
        $this->repo = new RepositorioEmpresa();
        $this->templates = $templates;
    }

    public function registrarEmpresa() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validador = new Validators();
            $errores = $validador->validarEmpresa($_POST);

            if (!empty($errores)) {
                echo $this->templates->render('../registro_empresa', [
                    'empresa' => $_POST,
                    'errores' => $errores
                ]);
                return;
            }

            $nuevaEmpresa = $this->repo->crearEmpresa($_POST, $_FILES);

            if ($nuevaEmpresa) {
                MailerService::enviarBienvenida($_POST['correo'], $_POST['nombre']);
                header('Location: ?page=login');
            } else {
                $error = 'Error al registrar la empresa. Revisa los datos.';
                echo $this->templates->render('../registro_empresa', [
                    'empresa' => $_POST,
                    'errores' => [$error]
                ]);
                return;
            }
        } else {
            echo $this->templates->render('../registro_empresa', [
                'empresa' => [],
                'errores' => []
            ]);
        }
    }

    public function crearEmpresa() {
        if (isset($_SESSION['correo']) && isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $validador = new Validators();
                $errores = $validador->validarEmpresa($_POST);
                if (!empty($errores)) {
                    echo $this->templates->render('../crear_empresa', [
                        'empresa' => $_POST,
                        'errores' => $errores
                    ]);
                    return;
                }

                $nuevaEmpresa = $this->repo->crearEmpresa($_POST, $_FILES);
                if ($nuevaEmpresa) {
                    MailerService::enviarBienvenida($_POST['correo'], $_POST['nombre']);
                    header('Location: ?page=tabla_empresas');
                } else {
                    $errores = ['Error al crear la empresa. Por favor, revisa los datos.'];
                    echo $this->templates->render('../crear_empresa', [
                        'empresa' => $_POST,
                        'errores' => $errores
                    ]);
                    return;
                }
            } else {
                echo $this->templates->render('../crear_empresa', [
                    'empresa' => [],
                    'errores' => []
                ]);
            }
        } else {
            header('Location: index.php?page=landing');
        }
    }

    public function mostrarTabla() {
        if (isset($_SESSION['correo']) && isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1) {
            $porPagina = 10;
            $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
            $orden = $_GET['orden'] ?? 'id';
            $sentido = (isset($_GET['sentido']) && strtolower($_GET['sentido']) === 'desc') ? 'DESC' : 'ASC';
            $buscar = trim($_GET['buscar'] ?? '');

            $resultado = $this->repo->getEmpresasPaginadoFiltrado($pagina, $porPagina, $orden, $sentido, $buscar);

            // Convierte a array para la vista
            $empresasArray = array_map(fn($e) => $e->toArray(), $resultado['empresas']);
            $noValidadasArray = array_map(fn($e) => $e->toArray(), $this->repo->getNoValidadas());

            echo $this->templates->render('../admin/tabla_empresas', [
                'empresas' => $empresasArray,
                'no_validadas' => $noValidadasArray,
                'pagina' => $pagina,
                'totalPaginas' => $resultado['totalPaginas'],
                'orden' => $orden,
                'sentido' => $sentido,
                'buscar' => $buscar
            ]);
        } else {
            header('Location: index.php?page=landing');
        }
    }

    public function validarEmpresa() {
        if (isset($_SESSION['correo']) && isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id = $_POST['id'] ?? null;
                if ($id) {
                    $this->repo->validarEmpresa($id);
                }
            }
            header('Location: ?page=tabla_empresas');
        } else {
            header('Location: index.php?page=landing');
        }
    }

    public function verDetallesEmpresa() {
        if (isset($_SESSION['correo']) && isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1) {
            $id = $_GET['id'] ?? null;
            $empresa = $id ? $this->repo->getPorId($id) : null;
            if ($empresa) {
                echo $this->templates->render('../detalles_empresa', [
                    'empresa' => $empresa->toArray()
                ]);
                return;
            }
            header('Location: ?page=tabla_empresas');
        } else {
            header('Location: index.php?page=landing');
        }
    }

    public function editarEmpresa() {
        if (isset($_SESSION['correo']) && isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1) {
            $id = $_GET['id'] ?? null;
            if (!$id) {
                header('Location: ?page=tabla_empresas');
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $validador = new Validators();
                $errores = $validador->validarEmpresaEdicion($_POST);
                if (!empty($errores)) {
                    $empresaArr = array_merge($_POST, ['id' => $id]);
                    echo $this->templates->render('../editar_empresa', [
                        'empresa' => $empresaArr,
                        'errores' => $errores
                    ]);
                    return;
                }

                $ok = $this->repo->actualizar($id, $_POST, $_FILES);
                if ($ok) {
                    header('Location: ?page=tabla_empresas');
                } else {
                    $empresaArr = array_merge($_POST, ['id' => $id]);
                    echo $this->templates->render('../editar_empresa', [
                        'empresa' => $empresaArr,
                        'errores' => ['Error al actualizar la empresa.']
                    ]);
                    return;
                }
            } else {
                $empresa = $this->repo->getPorId($id);
                echo $this->templates->render('../editar_empresa', [
                    'empresa' => $empresa ? $empresa->toArray() : [],
                    'errores' => []
                ]);
            }
        } else {
            header('Location: index.php?page=landing');
        }
    }

    public function editarPerfilEmpresa() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 3) {
            header('Location: index.php?page=landing');
        }
        $userId = $_SESSION['user_id'];
        $empresa = $this->repo->getPorUserId($userId);
        if (!$empresa) {
            header('Location: index.php?page=landing');
        }
        $empresaId = $empresa->getId();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $_POST;
            $validador = new Validators();
            $errores = $validador->validarEmpresaEdicion($datos);
            if (!empty($errores)) {
                echo $this->templates->render('../perfil_empresa', [
                    'empresa' => array_merge($empresa->toArray(), $datos),
                    'errores' => $errores
                ]);
                return;
            }

            if (empty($datos['contrasena'])) {
                unset($datos['contrasena']);
            } else {
                $datos['contrasena'] = password_hash($datos['contrasena'], PASSWORD_DEFAULT);
            }
            $ok = $this->repo->actualizar($empresaId, $datos, $_FILES);

            if ($ok) {
                header('Location: index.php?page=perfil_empresa');
            } else {
                $errores = ['Error actualizando datos en la base de datos.'];
                echo $this->templates->render('../perfil_empresa', [
                    'empresa' => array_merge($empresa->toArray(), $datos),
                    'errores' => $errores
                ]);
                return;
            }
        } else {
            echo $this->templates->render('../perfil_empresa', [
                'empresa' => $empresa->toArray(),
                'errores' => []
            ]);
        }
    }

    public function borrarEmpresa() {
        if (isset($_SESSION['correo']) && isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id = $_POST['id'] ?? null;
                if ($id) {
                    $this->repo->borrarPorEmpresaId($id);
                    header('Location: ?page=tabla_empresas');
                }
            }
            header('Location: ?page=tabla_empresas');
        } else {
            header('Location: index.php?page=landing');
        }
    }

    public function exportarEmpresaPDF() {
        $empresas = $this->repo->getTodas();
        // Si necesitas arrays:
        $empresasArray = array_map(fn($e) => $e->toArray(), $empresas);
        PdfEmpresa::exportEmpresas($empresasArray);
        exit;
    }
}
