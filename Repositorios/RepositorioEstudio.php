<?php
require_once dirname(__DIR__) . '/autoloader.php';

class RepositorioEstudio {
    private $db;
    
    public function __construct() {
        $this->db = DB::getConnection();
    }
    
    // Obtiene todos los estudios de un alumno (array de objetos Estudio)
    public function getPorAlumnoId($alumnoId) {
        $sql = "SELECT * FROM Estudios WHERE alumno_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$alumnoId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Estudio');
    }

    // Devuelve array de objetos stdClass con ids y nombres
    public function getPorAlumnoIdConNombres($alumnoId) {
        $sql = "SELECT e.id, e.ciclo_id, e.fechainicio, e.fechafin, 
                    c.nombre AS ciclo_nombre, f.id AS familia_id, f.nombre AS familia_nombre
                FROM Estudios e
                JOIN Ciclo c ON e.ciclo_id = c.id
                JOIN Familia f ON c.familia_id = f.id
                WHERE e.alumno_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$alumnoId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ); // Así puedes usar .familia_nombre y .ciclo_nombre en JS
    }
    
    // Obtiene un estudio por ID (objeto Estudio o false)
    public function getPorId($id) {
        $sql = "SELECT * FROM Estudios WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Estudio');
        return $stmt->fetch();
    }
    
    // Inserta un estudio (devuelve true/false)
    public function insertar($alumnoId, $cicloId, $fechainicio, $fechafin = null) {
        $sql = "INSERT INTO Estudios (alumno_id, ciclo_id, fechainicio, fechafin) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$alumnoId, $cicloId, $fechainicio, $fechafin]);
    }
    
    // Elimina un estudio por id
    public function borrarPorId($id) {
        $sql = "DELETE FROM Estudios WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    // Elimina todos los estudios de un alumno
    public function borrarPorAlumnoId($alumnoId) {
        $sql = "DELETE FROM Estudios WHERE alumno_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$alumnoId]);
    }
}
?>
