<?php
require_once dirname(__DIR__) . '/autoloader.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Controlador para la gestión de alumnos.
 * Orquesta vistas y operaciones utilizando siempre objetos Alumno.
 */
class AlumnoController
{
    /** @var RepositorioAlumno */
    private $repo;

    /** @var object Motor de plantillas */
    private $templates;

    /**
     * Constructor.
     * @param object $templates Motor de plantillas
     */
    public function __construct($templates)
    {
        $this->repo = new RepositorioAlumno();
        $this->templates = $templates;
    }

    /**
     * Muestra la tabla/listado de alumnos solo a administradores.
     */
    public function mostrarTabla()
    {
        if (empty($_SESSION['correo']) || empty($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
            header('Location: index.php?page=landing');
        }
        echo $this->templates->render('../admin/tabla_alumnos', [
            'title' => 'Usuarios registrados'
        ]);
    }

    /**
     * Muestra el formulario de registro de un nuevo alumno.
     */
    public function registrarAlumno()
    {
        echo $this->templates->render('../registro_alumno', [
            'title' => 'Registro de alumno'
        ]);
    }

    /**
     * Muestra el perfil del alumno.
     */
    public function mostrarPerfil()
    {
        echo $this->templates->render('../perfil_alumno', [
            'title' => 'Perfil del alumno'
        ]);
    }

    /**
     * Exporta el listado de alumnos a PDF (conversión a arrays).
     */
    public function exportarAlumnoPDF()
    {
        $alumnosObjs = $this->repo->getTodos(); // Devuelve objetos Alumno
        $alumnosArray = array_map(fn($a) => $a->toArray(), $alumnosObjs);
        PdfAlumnos::exportAlumnos($alumnosArray);
        exit;
    }
}
