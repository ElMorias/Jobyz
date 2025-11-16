<?php
require_once dirname(__DIR__) . '/autoloader.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class OfertaController {
    private $templates;
    private $repo;

    public function __construct($templates) {
        $this->templates = $templates;
        $this->repo = new RepositorioOfertas;
    }

    // Listado de ofertas según rol, y borrado
    public function mostrarOfertas() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $rolId = $_SESSION['rol_id'] ?? null;
        $empresaRepo = new RepositorioEmpresa();
        $empresa_id = $empresaRepo->getEmpresaIdPorUserId($_SESSION['user_id']);

        // Borrar oferta (admin o empresa)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrar'])) {
            if ( $rol_id != 2){
                $this->repo->borrar((int)$_POST['id']);
                header('Location: index.php?page=ofertas');
                exit;
            }    
        }

        if ($rolId == 3) {
            $empresa_id;
            $ofertas = $this->repo->deEmpresa($empresa_id);
        } else {
            $ofertas = $this->repo->todas();
        }

        echo $this->templates->render('../ofertas', [
            'title' => 'Ofertas',
            'ofertas' => array_map(fn($o) => $o->toArray(), $ofertas)
        ]);
    }

    // Crear nueva oferta (solo para empresa)
    public function nuevaOferta() {
        $empresaRepo = new RepositorioEmpresa();
        $empresa_id = $empresaRepo->getEmpresaIdPorUserId($_SESSION['user_id']);

        if ($_SESSION['rol_id'] != 3) {
            header('Location: index.php?page=ofertas');
            exit;
        }
        $errores = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo = trim($_POST['titulo'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $fechalimite = $_POST['fechalimite'] ?? '';
            $empresa_id;
            if (!$titulo) $errores[] = "El título es obligatorio";
            if (!$descripcion) $errores[] = "La descripción es obligatoria";
            if (!$fechalimite) $errores[] = "La fecha límite es obligatoria";
            if (!$errores) {
                $this->repo->insertarOferta($titulo, $descripcion, $empresa_id, $fechalimite);
                header('Location: index.php?page=ofertas&creada=1');
                exit;
            }
        }
        echo $this->templates->render('../nueva_oferta', [
            'errores' => $errores
        ]);
    }

    // Modificar oferta (solo empresa dueña)
    public function modificarOferta() {
        $empresaRepo = new RepositorioEmpresa();
        $empresa_id = $empresaRepo->getEmpresaIdPorUserId($_SESSION['user_id']);
        $id = $_GET['id'] ?? null;
        
        if ($_SESSION['rol_id'] != 3 || !$id) {
            header('Location: index.php?page=ofertas');
            exit;
        }
        $oferta = $this->repo->obtener($id);
        if (!$oferta || $oferta->empresa_id != $empresa_id) {
            header('Location: index.php?page=ofertas');
            exit;
        }
        $errores = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo = trim($_POST['titulo'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $fechalimite = $_POST['fechalimite'] ?? '';
            if (!$titulo) $errores[] = "El título es obligatorio";
            if (!$descripcion) $errores[] = "La descripción es obligatoria";
            if (!$fechalimite) $errores[] = "La fecha límite es obligatoria";
            if (!$errores) {
                $this->repo->actualizar($id, $titulo, $descripcion, $fechalimite);
                header('Location: index.php?page=ofertas');
                exit;
            }
        }
        echo $this->templates->render('../modificar_oferta', [
            'errores' => $errores,
            'oferta' => $oferta
        ]);
    }

    // Ver detalle de una oferta (solo admin), id SIEMPRE por $_GET
    public function detalleOferta() {
        $id = $_GET['id'] ?? null;
        if ($_SESSION['rol_id'] != 1 || !$id || !is_numeric($id)) {
            header('Location: index.php?page=ofertas');
            exit;
        }
        $oferta = $this->repo->obtener($id);
        echo $this->templates->render('../detalle_oferta', [
            'oferta' => $oferta
        ]);
    }


    //solicitudes de una oferta
    public function solicitudesOferta() {
        $empresaRepo = new RepositorioEmpresa();
        $empresa_id = $empresaRepo->getEmpresaIdPorUserId($_SESSION['user_id']);
        
        $id = $_GET['id'] ?? null;
        if (!$id || !is_numeric($id)) {
            header('Location: index.php?page=ofertas');
            exit;
        }
    
        $oferta = $this->repo->obtener($id);
        if (!$oferta) {
            header('Location: index.php?page=ofertas');
            exit;
        }
        $rol_id = $_SESSION['rol_id'] ?? null;

        if ($rol_id != 1 && ($rol_id != 3 || $oferta->empresa_id != $empresa_id)) {
            header('Location: index.php?page=ofertas');
            exit;
        }

        $repoSolicitudes = new RepositorioSolicitudes();
        $solicitudes = $repoSolicitudes->deOferta($id);

        echo $this->templates->render('../solicitudes_oferta', [
            'solicitudes' => $solicitudes
        ]);
    }

}
?>
