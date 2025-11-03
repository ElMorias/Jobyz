<?php
require_once dirname(__DIR__) . '/autoloader.php';
header('Content-Type: application/json');

$repo = new RepositorioFamilia();
echo json_encode($repo->getAll());
?>
