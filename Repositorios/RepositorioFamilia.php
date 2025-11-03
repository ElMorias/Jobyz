<?php

class RepositorioFamilia {
    private $db;

    public function __construct() {
        $this->db = DB::getConnection();
    }

    public function getAll() {
        $stmt = $this->db->prepare("SELECT id, nombre FROM familia");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}   

?>
