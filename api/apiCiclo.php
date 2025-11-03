<?php
require_once dirname(__DIR__) . '/autoloader.php';
header('Content-Type: application/json');

if (!isset($_GET['familia_id'])) {
  echo json_encode([]);
  exit;
}
$repo = new RepositorioCiclo();
echo json_encode($repo->getByFamilia($_GET['familia_id']));
?>
