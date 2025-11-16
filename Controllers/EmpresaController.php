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

    public function registrarEmpresa(){
           if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ok = $this->repo->crearEmpresa($_POST, $_FILES); // Passo POST y archivos
            if ($ok) {
                
                header('Location: ?page=login');
                exit();
            } else {
                // Si falla algo, muestra el mismo form con mensaje de error
                $error = 'Error al registrar la empresa. Por favor, revisa los datos.';
                echo $this->templates->render('../registro_empresa', [
                    'empresa' => $_POST,
                    'error' => $error
                ]);
                return;
            }
        } else {
            echo $this->templates->render('../registro_empresa', [
                'empresa' => [],
                'error' => ''
            ]);
        }
    }

    public function crearEmpresa(){
        if (isset($_SESSION['correo']) || isset($_SESSION['rol_id']) || $_SESSION['rol_id'] == 1) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $ok = $this->repo->crearEmpresa($_POST, $_FILES); 
                if ($ok) {
                    // Redirige al listado CRUD después de crear
                    header('Location: ?page=tabla_empresas');
                    exit();
                } else {
                    // Si falla algo, muestra el mismo form con mensaje de error
                    $error = 'Error al crear la empresa. Por favor, revisa los datos.';
                    echo $this->templates->render('../crear_empresa', [
                        'empresa' => $_POST,
                        'error' => $error
                    ]);
                    return;
                }
            } else {
                echo $this->templates->render('../crear_empresa', [
                    'empresa' => [],
                    'error' => ''
                ]);
            }
        }else{
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
        if (isset($_SESSION['correo']) || isset($_SESSION['rol_id']) || $_SESSION['rol_id'] == 1) {
            $id = $_GET['id'] ?? null;
            if (!$id) {
                header('Location: ?page=tabla_empresas');
                exit();
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $ok = $this->repo->actualizar($id, $_POST, $_FILES);
                if ($ok) {
                header('Location: ?page=tabla_empresas');
                exit();
                } else {
                $error = 'Error al actualizar la empresa.';
                $empresa = array_merge($_POST, ['id' => $id]);
                echo $this->templates->render('../editar_empresa', [
                    'empresa' => $empresa,
                    'error' => $error
                ]);
                return;
                }
            } else {
                $empresa = $this->repo->getPorId($id);
                echo $this->templates->render('../editar_empresa', [
                'empresa' => $empresa,
                'error' => ''
                ]);
            }
        }else{
            header('Location: index.php?page=landing');
            exit;
        }
    }

     
    public function editarPerfilEmpresa() {
    // 1. Comprobar que el usuario está logueado y es empresa
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 3) {
        // Si no es empresa, redirige fuera
        header('Location: index.php?page=landing');
        exit();
    }

    // 2. Recuperar el id del usuario desde la sesión (siempre está si está logueado)
    $userId = $_SESSION['user_id'];

    // 3. Buscar la empresa asociada a ese usuario mediante su user_id
    $empresa = $this->repo->getPorUserId($userId); // Debes tener este método en el repositorio
    if (!$empresa) {
        // Si no encuentra la empresa, redirige o muestra error
        header('Location: index.php?page=landing');
        exit();
    }
    $empresaId = $empresa['id'];

    // 4. Si el formulario fue enviado vía POST, procesamos actualización
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Si el campo 'contrasena' viene vacío, no se actualiza la contraseña
        $datos = $_POST;
        if (empty($datos['contrasena'])) {
            unset($datos['contrasena']);
        } else {
            // Si se envió nueva contraseña, la hasheamos
            $datos['contrasena'] = password_hash($datos['contrasena'], PASSWORD_DEFAULT);
        }
        // Procesar imagen/foto si se envía
        // (puedes poner tu lógica de $_FILES acá)
        $ok = $this->repo->actualizar($empresaId, $datos, $_FILES);
        
        if ($ok) {
            // Redirige al propio perfil tras actualizar
            header('Location: index.php?page=perfil_empresa');
            exit();
        } else {
            $error = 'Error actualizando datos';
            // Muestra el formulario con los datos enviados y el error
            echo $this->templates->render('../perfil_empresa', [
                'empresa' => array_merge($empresa, $_POST),
                'error' => $error
            ]);
            return;
        }
    } else {
        // 5. Si no se envió el formulario, mostrar la página de perfil con los datos actuales
        echo $this->templates->render('../perfil_empresa', [
            'empresa' => $empresa,
            'error' => ''
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
}
?>