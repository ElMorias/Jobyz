<?php
require_once dirname(__DIR__) . '/autoloader.php';

class SolicitudController {
  private $templates;
  private $repo;

  public function __construct($templates) {
    $this ->templates = $templates;
    $this -> repo = new RepositorioSolicitudes;
  }

    public function mostrarSolicitudes() {
        echo $this->templates->render('../solicitudes', ['title' => 'Solicitudes']);
    }

    public function solicitarOferta() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $oferta_id = $_GET['id'] ?? null;
        $user_id = $_SESSION['user_id'] ?? null;

        if (!$oferta_id || !is_numeric($oferta_id) || !$user_id) {
            header('Location: index.php?page=ofertas');
            exit;
        }

        // Busca el id de alumno que corresponde a este usuario
        $alumnorepo= new RepositorioAlumno();
        $alumno_id = $alumnorepo->getAlumnoIdPorUserId($user_id);

        if (!$alumno_id) {
            header('Location: index.php?page=ofertas');
            exit;
        }

        // Crea la solicitud solo si no existe ya
        $existe = $this->repo->buscaDuplicada($alumno_id, $oferta_id);
        if (!$existe) {
            $this->repo->insertar($alumno_id, $oferta_id);
        }

        // Redirige a la lista de solicitudes del alumno
        header('Location: index.php?page=solicitudes');
        exit;
    }

}
?>