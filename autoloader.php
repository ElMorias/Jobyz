<?php
spl_autoload_register(function($clase) {
    // Rutas base donde buscar las clases (ajusta según tus carpetas)
    $rutas = [
        __DIR__ . '/api/',
        __DIR__ . '/assets/',
        __DIR__ . '/controllers/',
        __DIR__ . '/Helpers/',
        __DIR__ . '/Models/',
        __DIR__ . '/Repositorios/',
        __DIR__ . '/Views/'
    ];

    foreach ($rutas as $ruta) {
        $archivo = $ruta . $clase . '.php';
        if (file_exists($archivo)) {
            require_once $archivo;
            return;
        }
    }
});
?>
