<?php
require_once dirname(__DIR__) . '/autoloader.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Controlador de administración: muestra el panel principal de admin.
 */
class AdminController
{
    /**
     * Motor de plantillas para renderizar vistas.
     * @var object
     */
    private $templates;

    /**
     * Constructor.
     * @param object $templates Motor de plantillas (ejemplo: Plates, Twig...)
     */
    public function __construct($templates)
    {
        $this->templates = $templates;
    }

    /**
     * Renderiza el panel admin si el usuario tiene sesión y rol admin (rol_id=1).
     * Si no, redirige a la landing.
     */
    public function mostrarPanel()
    {
        if (
            empty($_SESSION['correo']) ||
            empty($_SESSION['rol_id']) ||
            $_SESSION['rol_id'] != 1
        ) {
            header('Location: index.php?page=landing');
            exit;
        }

        echo $this->templates->render('../admin/panel_admin', [
            'title' => 'Panel de Control'
        ]);
    }
}
