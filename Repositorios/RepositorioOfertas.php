<?php
class RepositorioOfertas {
    private $db;

    public function __construct() {
        $this->db = DB::getConnection();
    }

    // Obtener todas las ofertas con nombre de empresa
    public function todas(){
        $sql = "SELECT o.*, e.nombre AS empresa_nombre
                FROM oferta o
                JOIN empresa e ON o.empresa_id = e.id";
        $stmt = $this->db->query($sql);
        $ofertas = [];
        foreach ($stmt as $row) {
            $oferta = Oferta::fromArray($row);
            $oferta->empresa_nombre = $row['empresa_nombre'];
            $ofertas[] = $oferta;
        }
        return $ofertas;
    }

    // Ofertas de una empresa
    public function deEmpresa($empresa_id){
        $sql = "SELECT o.*, e.nombre AS empresa_nombre
                FROM oferta o
                JOIN empresa e ON o.empresa_id = e.id
                WHERE o.empresa_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$empresa_id]);
        $ofertas = [];
        foreach ($stmt as $row) {
            $oferta = Oferta::fromArray($row);
            $oferta->empresa_nombre = $row['empresa_nombre'];
            $ofertas[] = $oferta;
        }
        return $ofertas;
    }

    // Insertar nueva oferta (MySQL pone fechainicio solo)
    public function insertarOferta($titulo, $descripcion, $empresa_id, $fechalimite) {
        $stmt = $this->db->prepare(
            "INSERT INTO oferta (titulo, descripcion, empresa_id, fechalimite)
             VALUES (?, ?, ?, ?)");
        $stmt->execute([$titulo, $descripcion, $empresa_id, $fechalimite]);
    }

    // Borrar una oferta por ID
    public function borrar($id) {
        $stmt = $this->db->prepare("DELETE FROM oferta WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function obtener($id): ?Oferta {
        $stmt = $this->db->prepare("SELECT o.*, e.nombre AS empresa_nombre
                                    FROM oferta o JOIN empresa e ON o.empresa_id = e.id
                                    WHERE o.id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if($row){
            $oferta = Oferta::fromArray($row);
            $oferta->empresa_nombre = $row['empresa_nombre'];
            return $oferta;
        }
        return null;
    }

  
    public function actualizar($id, $titulo, $descripcion, $fechalimite) {
        $stmt = $this->db->prepare("UPDATE oferta SET titulo=?, descripcion=?, fechalimite=? WHERE id=?");
        $stmt->execute([$titulo, $descripcion, $fechalimite, $id]);
    }
}

?>