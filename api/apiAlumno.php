<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    obtenerAlumno();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    crearAlumno();
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    actualizarAlumno();
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    eliminarAlumno();
} else {
    http_response_code(405); // Método no permitido
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

function obtenerAlumno(){
    //aqui se recogen los datos del body o del header o de dodne sea
    // se hace un converter si hace falta
    // hago el json y se llama al repositorio para que obtenga datos o lo que sea
}


?>