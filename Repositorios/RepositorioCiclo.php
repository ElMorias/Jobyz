<?php

class RepositorioCiclo {
    private $db;

    public function __construct() {
        $this->db = DB::getConnection();
    }
    
    public function getByFamilia($familia_id) {
        $stmt = $this->db->prepare("SELECT id, nombre FROM ciclo WHERE familia_id=?");
        $stmt->execute([$familia_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        $sql = "SELECT id, nombre FROM ciclo";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
