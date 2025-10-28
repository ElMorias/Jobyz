<?php
// Simulación de datos recibidos (en un caso real vendrían por POST)
$usuario = [
    "id" => 7,
    "nombre" => "Elena",
    "apellidos" => "Navarro Ruiz",
    "mail" => "elena.navarro@example.com"
];

// Simulación de respuesta de creación exitosa
$response = [
    "creado" => true,
    "mensaje" => "Usuario creado correctamente",
    "usuario" => $usuario
];

echo json_encode($response);
?>