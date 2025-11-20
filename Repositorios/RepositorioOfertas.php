<?php
require_once dirname(__DIR__) . '/autoloader.php';

class RepositorioOfertas {
    private $db;

    public function __construct() {
        $this->db = DB::getConnection();
    }

    // Obtener todas las ofertas con nombre de empresa
    public function todas(): array {
        $sql = "SELECT o.*, e.nombre AS empresa_nombre
                FROM oferta o
                JOIN empresa e ON o.empresa_id = e.id";
        $stmt = $this->db->query($sql);
        $ofertas = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $oferta = Oferta::fromArray($row);
            $oferta->empresa_nombre = $row['empresa_nombre'];
            $ofertas[] = $oferta;
        }
        return $ofertas;
    }

    // Ofertas de una empresa
    public function deEmpresa($empresa_id): array {
        $sql = "SELECT o.*, e.nombre AS empresa_nombre
                FROM oferta o
                JOIN empresa e ON o.empresa_id = e.id
                WHERE o.empresa_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$empresa_id]);
        $ofertas = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $oferta = Oferta::fromArray($row);
            $oferta->empresa_nombre = $row['empresa_nombre'];
            $ofertas[] = $oferta;
        }
        return $ofertas;
    }

   //Devuelve un array con el nombre del ciclo y el n de ofertas que lo solicitan
    public function topCiclosEnOfertas() {
        $sql = "
            SELECT ciclo.nombre, COUNT(*) as total
            FROM oferta_has_ciclo
            JOIN ciclo ON ciclo.id = oferta_has_ciclo.ciclo_id
            GROUP BY ciclo.nombre
            ORDER BY total DESC
            LIMIT 5
        ";
        $stmt = $this->db->query($sql);
        $arr = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $arr[$row['nombre']] = (int)$row['total'];
        }
        return $arr;
    }


    // Insertar nueva oferta y devolver el ID
    public function insertarOferta($titulo, $descripcion, $empresa_id, $fechalimite): int {
        $stmt = $this->db->prepare(
            "INSERT INTO oferta (titulo, descripcion, empresa_id, fechalimite)
             VALUES (?, ?, ?, ?)");
        $stmt->execute([$titulo, $descripcion, $empresa_id, $fechalimite]);
        return (int)$this->db->lastInsertId();
    }

    public function anadirCicloAOferta($ofertaId, $cicloId): bool {
        $sql = "INSERT INTO oferta_has_ciclo (oferta_id, ciclo_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$ofertaId, $cicloId]);
    }

    // Borrar una oferta por ID
    public function borrar($id): void {
        $stmt = $this->db->prepare("DELETE FROM oferta WHERE id = ?");
        $stmt->execute([$id]);
    }

    // Obtener oferta por ID (con ciclos y empresa_nombre)
    public function obtener($id): ?Oferta {
        $stmt = $this->db->prepare("SELECT o.*, e.nombre AS empresa_nombre
                                    FROM oferta o JOIN empresa e ON o.empresa_id = e.id
                                    WHERE o.id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row){
            $oferta = Oferta::fromArray($row);
            $oferta->empresa_nombre = $row['empresa_nombre'];
            // Añadir ciclos como array de nombres
            $oferta->ciclos = $this->obtenerCiclosPorOferta($id);
            return $oferta;
        }
        return null;
    }

    // Devuelve array de nombres de ciclos de una oferta
    public function obtenerCiclosPorOferta($ofertaId): array {
        $sql = "SELECT c.nombre 
                FROM oferta_has_ciclo oc
                INNER JOIN ciclo c ON oc.ciclo_id = c.id
                WHERE oc.oferta_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$ofertaId]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'nombre');
    }

    public function actualizar($id, $titulo, $descripcion, $fechalimite): void {
        $stmt = $this->db->prepare(
            "UPDATE oferta SET titulo=?, descripcion=?, fechalimite=? WHERE id=?");
        $stmt->execute([$titulo, $descripcion, $fechalimite, $id]);
    }
}
