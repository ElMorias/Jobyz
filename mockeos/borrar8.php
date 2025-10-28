<?php

// Simulación de borrado exitoso
$response = [
    "borrado" => true,
    "id" => 3, // ID del alumno que se ha "borrado"
    "mensaje" => "Alumno borrado correctamente"
];

echo json_encode($response);
