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
        $this->repo = new RepositorioOfertas();
    }

    // Listado de ofertas, borra y prepara array para la vista
    public function mostrarOfertas() {
        $rol_id = $_SESSION['rol_id'] ?? null;
        $empresaRepo = new RepositorioEmpresa();
        $empresa_id = $empresaRepo->getEmpresaIdPorUserId($_SESSION['user_id'] ?? null);

        // Borrado (admin o empresa)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrar'])) {
            if ($rol_id != 2) {
                $this->repo->borrar((int)$_POST['id']);
                header('Location: index.php?page=ofertas');
                exit;
            }
        }

        // Cargar según rol
        if ($rol_id == 3) {
            $ofertasObj = $this->repo->deEmpresa($empresa_id);
        } else {
            $ofertasObj = $this->repo->todas();
        }

        // Añadir ciclos en array de salida
        $ofertas = [];
        foreach ($ofertasObj as $oferta) {
            $arr = $oferta->toArray();
            if (!isset($arr['ciclos']) || empty($arr['ciclos'])) {
                $arr['ciclos'] = $this->repo->obtenerCiclosPorOferta($arr['id']);
            }
            $ofertas[] = $arr;
        }

        echo $this->templates->render('../ofertas', [
            'title' => 'Ofertas',
            'ofertas' => $ofertas
        ]);
    }

    // Crear nueva oferta - solo empresas
    public function nuevaOferta() {
        $empresaRepo = new RepositorioEmpresa();
        $empresa_id = $empresaRepo->getEmpresaIdPorUserId($_SESSION['user_id'] ?? null);

        if ($_SESSION['rol_id'] != 3) {
            header('Location: index.php?page=ofertas');
            exit;
        }

        $cicloRepo = new RepositorioCiclo();
        $ciclos = $cicloRepo->getAll();

        $errores = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo = trim($_POST['titulo'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $fechalimite = $_POST['fechalimite'] ?? '';
            $ciclosSelecionados = $_POST['ciclos'] ?? [];

            if (!$titulo) $errores[] = "El título es obligatorio";
            if (!$descripcion) $errores[] = "La descripción es obligatoria";
            if (!$fechalimite) $errores[] = "La fecha límite es obligatoria";

            $ciclosFiltrados = array_unique(array_filter($ciclosSelecionados));
            if (empty($ciclosFiltrados)) {
                $errores[] = "Debes seleccionar al menos un ciclo requerido";
            }
            if (count($ciclosFiltrados) > 2) {
                $errores[] = "No puedes seleccionar más de 2 ciclos";
            }

            if (!$errores) {
                $ofertaId = $this->repo->insertarOferta($titulo, $descripcion, $empresa_id, $fechalimite);
                foreach ($ciclosFiltrados as $cicloId) {
                    $this->repo->anadirCicloAOferta($ofertaId, $cicloId);
                }
                header('Location: index.php?page=ofertas&creada=1');
                exit;
            }
        }

        echo $this->templates->render('../nueva_oferta', [
            'errores' => $errores,
            'ciclos' => $ciclos
        ]);
    }

    // Modificar oferta (solo empresa dueña)
    public function modificarOferta() {
        $empresaRepo = new RepositorioEmpresa();
        $empresa_id = $empresaRepo->getEmpresaIdPorUserId($_SESSION['user_id'] ?? null);
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
            'oferta' => $oferta->toArray()
        ]);
    }

    // Ver detalle de una oferta (admin)
    public function detalleOferta() {
        $id = $_GET['id'] ?? null;
        if ($_SESSION['rol_id'] != 1 || !$id || !is_numeric($id)) {
            header('Location: index.php?page=ofertas');
            exit;
        }
        $oferta = $this->repo->obtener($id);
        echo $this->templates->render('../detalle_oferta', [
            'oferta' => $oferta ? $oferta->toArray() : null
        ]);
    }

    public function solicitudesOferta() {
        $empresaRepo = new RepositorioEmpresa();
        $empresa_id = $empresaRepo->getEmpresaIdPorUserId($_SESSION['user_id'] ?? null);
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
