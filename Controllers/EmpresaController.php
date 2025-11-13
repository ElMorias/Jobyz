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
            $empresas = $this->repo->getTodas();
            $no_validadas = $this->repo->getNoValidadas();

            echo $this->templates->render('../admin/tabla_empresas', ['empresas' => $empresas, 'no_validadas' => $no_validadas]);
        }else{
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