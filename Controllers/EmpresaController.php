<?php
require_once dirname(__DIR__) . '/autoloader.php';
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

class EmpresaController {
    private $repo;
    private $templates;

    public function __construct($templates) {
        $this->repo = new RepositorioEmpresa();
        $this ->templates = $templates;
    }

    public function registrarEmpresa() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Valida datos antes de guardar
            $validador = new Validators();
            $errores = $validador->validarEmpresa($_POST);

            if (!empty($errores)) {
                // Devuelve los errores, mantén los datos escritos
                echo $this->templates->render('../registro_empresa', [
                    'empresa' => $_POST,
                    'errores' => $errores // Cambia 'error' por 'errores' para mostrar lista
                ]);
                return;
            }

            // Si no hay errores, guardar en base de datos
            $ok = $this->repo->crearEmpresa($_POST, $_FILES);
            if ($ok) {
                MailerService::enviarBienvenida($_POST['correo'], $_POST['nombre']);
                header('Location: ?page=login');
                exit();
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


    public function crearEmpresa(){
        if (isset($_SESSION['correo']) && isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Validación antes de crear
                $validador = new Validators();
                $errores = $validador->validarEmpresa($_POST);
                if (!empty($errores)) {
                    echo $this->templates->render('../crear_empresa', [
                        'empresa' => $_POST,
                        'errores' => $errores
                    ]);
                    return;
                }

                // Solo si no hay errores se crea la empresa:
                $ok = $this->repo->crearEmpresa($_POST, $_FILES); 
                if ($ok) {
                    MailerService::enviarBienvenida($_POST['correo'], $_POST['nombre']);
                    header('Location: ?page=tabla_empresas');
                    exit();
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
            exit;
        }
    }



    public function mostrarTabla() {
        if (isset($_SESSION['correo']) || isset($_SESSION['rol_id']) || $_SESSION['rol_id'] == 1) {
            $porPagina = 10;
            $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
            $orden = $_GET['orden'] ?? 'id';
            $sentido = (isset($_GET['sentido']) && strtolower($_GET['sentido']) === 'desc') ? 'DESC' : 'ASC';
            $buscar = trim($_GET['buscar'] ?? '');

            // Llama a tu método de repositorio (te lo describo abajo)
            $resultado = $this->repo->getEmpresasPaginadoFiltrado($pagina, $porPagina, $orden, $sentido, $buscar);

            $empresas = $resultado['empresas'];
            $totalPaginas = $resultado['totalPaginas'];
            $no_validadas = $this->repo->getNoValidadas();

            echo $this->templates->render('../admin/tabla_empresas', [
                'empresas'     => $empresas,
                'no_validadas' => $no_validadas,
                'pagina'       => $pagina,
                'totalPaginas' => $totalPaginas,
                'orden'        => $orden,
                'sentido'      => $sentido,
                'buscar'       => $buscar
            ]);
        } else {
            header('Location: index.php?page=landing');
            exit;
        }
    }

    public function validarEmpresa() {
        if (isset($_SESSION['correo']) || isset($_SESSION['rol_id']) || $_SESSION['rol_id'] == 1) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id = $_POST['id'] ?? null;
                if ($id) {
                    $this->repo->validarEmpresa($id);
                }
            }
            header('Location: ?page=tabla_empresas');
            exit();
        }else{
            header('Location: index.php?page=landing');
            exit;
        }
    }


    public function verDetallesEmpresa(){
        if (isset($_SESSION['correo']) || isset($_SESSION['rol_id']) || $_SESSION['rol_id'] == 1) {
            $id = $_GET['id'] ?? null;
            if ($id) {
                $empresa = $this->repo->getPorId($id);
                if ($empresa) {
                    echo $this->templates->render('../detalles_empresa', [
                        'empresa' => $empresa
                    ]);
                    return;
                }
            }
            // Si no hay empresa o no hay id, redirige al listado
            header('Location: ?page=tabla_empresas');
            exit();
        }else{
            header('Location: index.php?page=landing');
            exit;
        }
    }

    public function editarEmpresa() {
        if (isset($_SESSION['correo']) && isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1) {
            $id = $_GET['id'] ?? null;
            if (!$id) {
                header('Location: ?page=tabla_empresas');
                exit();
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $validador = new Validators();
                $errores = $validador->validarEmpresaEdicion($_POST);  // <-- validación aquí

                if (!empty($errores)) {
                    $empresa = array_merge($_POST, ['id' => $id]);
                    echo $this->templates->render('../editar_empresa', [
                        'empresa' => $empresa,
                        'errores' => $errores // <-- muestra lista o por campo en la vista
                    ]);
                    return;
                }

                $ok = $this->repo->actualizar($id, $_POST, $_FILES);
                if ($ok) {
                    header('Location: ?page=tabla_empresas');
                    exit();
                } else {
                    $error = 'Error al actualizar la empresa.';
                    $empresa = array_merge($_POST, ['id' => $id]);
                    echo $this->templates->render('../editar_empresa', [
                        'empresa' => $empresa,
                        'errores' => [$error]
                    ]);
                    return;
                }
            } else {
                $empresa = $this->repo->getPorId($id);
                echo $this->templates->render('../editar_empresa', [
                    'empresa' => $empresa,
                    'errores' => []
                ]);
            }
        } else {
            header('Location: index.php?page=landing');
            exit;
        }
    }


     
    public function editarPerfilEmpresa() {
        // 1. Comprobar que el usuario está logueado y es empresa
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 3) {
            header('Location: index.php?page=landing');
            exit();
        }
        $userId = $_SESSION['user_id'];
        $empresa = $this->repo->getPorUserId($userId);
        if (!$empresa) {
            header('Location: index.php?page=landing');
            exit();
        }
        $empresaId = $empresa['id'];

        // 4. Si el formulario fue enviado vía POST, procesamos actualización
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $_POST;

            // VALIDACIÓN ANTES DE ACTUALIZAR
            $validador = new Validators();
            $errores = $validador->validarEmpresaEdicion($datos);
            if (!empty($errores)) {
                // Vuelve a mostrar el formulario con los errores y datos actuales
                echo $this->templates->render('../perfil_empresa', [
                    'empresa' => array_merge($empresa, $datos),
                    'errores' => $errores
                ]);
                return;
            }

            // Procesa la contraseña: si viene, la hashea, si no, no la pongas
            if (empty($datos['contrasena'])) {
                unset($datos['contrasena']);
            } else {
                $datos['contrasena'] = password_hash($datos['contrasena'], PASSWORD_DEFAULT);
            }
            // Aquí lógicamente podrías procesar $_FILES si lo necesitas
            $ok = $this->repo->actualizar($empresaId, $datos, $_FILES);

            if ($ok) {
                header('Location: index.php?page=perfil_empresa');
                exit();
            } else {
                // Podrías agregar más detalles según error, aquí le añadimos uno general
                $errores = ['Error actualizando datos en la base de datos.'];
                echo $this->templates->render('../perfil_empresa', [
                    'empresa' => array_merge($empresa, $datos),
                    'errores' => $errores
                ]);
                return;
            }
        } else {
            // Si no se envió el formulario, mostrar el perfil normal
            echo $this->templates->render('../perfil_empresa', [
                'empresa' => $empresa,
                'errores' => []
            ]);
        }
    }


    public function borrarEmpresa() {
        if (isset($_SESSION['correo']) || isset($_SESSION['rol_id']) || $_SESSION['rol_id'] == 1) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id = $_POST['id'] ?? null;
                if ($id) {
                $ok = $this->repo->borrarPorEmpresaId($id);
                // Da igual mostrar mensaje, basta con recargar la tabla
                header('Location: ?page=tabla_empresas');
                exit();
                }
            }
            header('Location: ?page=tabla_empresas.');
            exit();
        }else{
            header('Location: index.php?page=landing');
            exit;
        }
    }

    public function exportarEmpresaPDF(){
        $empresas = $this->repo->getTodas();
        PdfEmpresa::exportEmpresas($empresas);
        exit; // importante para que no imprima HTML adicional
    }
}
?>