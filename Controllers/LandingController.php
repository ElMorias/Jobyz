<?php
require_once dirname(__DIR__) . '/autoloader.php';

// Si no hay sesión iniciada, la inicia
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * LandingController
 *
 * Controlador responsable de gestionar la pantalla de bienvenida ("landing page") para usuarios.
 * Según si el usuario está logueado o no, muestra una vista diferente.
 */
class LandingController {
    /** @var object Motor de plantillas (Plates, Twig, etc.) */
    private $templates;

    /**
     * Constructor
     * @param object $templates Motor de plantillas a usar para renderizar las vistas
     */
    public function __construct($templates) {
        $this->templates = $templates;
    }

    /**
     * Muestra la landing principal.
     * - Si el usuario está logueado (hay 'correo' en sesión), muestra landing para logueados.
     * - Si no, muestra la landing sin login.
     */
    public function mostrarLanding() {
        if (isset($_SESSION['correo'])) {
            echo $this->templates->render('../landing_logueado', [
                'title' => 'Bienvenido a Jobyz'
            ]);
        } else {
            echo $this->templates->render('../landing', [
                'title' => 'Bienvenido a Jobyz'
            ]);
        }
    }
}
