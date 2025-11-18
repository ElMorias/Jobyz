<?php
require_once dirname(__DIR__) . '/autoloader.php';

/**
 * Repositorio para la tabla Estudios.
 * Siempre devuelve objetos Estudio, salvo los métodos conNombres (relaciones).
 */
class RepositorioEstudio {
    private $db;

    public function __construct() {
        $this->db = DB::getConnection();
    }

    /**
     * Obtiene todos los estudios de un alumno (devuelve array de objetos Estudio)
     * @param int $alumnoId
     * @return Estudio[]
     */
    public function getPorAlumnoId($alumnoId) {
        $sql = "SELECT * FROM Estudios WHERE alumno_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$alumnoId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Estudio');
    }

    /**
     * Obtiene estudios de un alumno con los nombres de ciclo y familia
     * Devuelve array de stdClass con nombres legibles para la vista/front
     * @param int $alumnoId
     * @return object[]
     */
    public function getPorAlumnoIdConNombres($alumnoId) {
        $sql = "SELECT e.id, e.ciclo_id, e.fechainicio, e.fechafin, 
                       c.nombre AS ciclo_nombre, f.id AS familia_id, f.nombre AS familia_nombre
                FROM Estudios e
                JOIN Ciclo c ON e.ciclo_id = c.id
                JOIN Familia f ON c.familia_id = f.id
                WHERE e.alumno_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$alumnoId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ); // Para acceso directo front (.ciclo_nombre, .familia_nombre)
    }

    /**
     * Devuelve un Estudio por id (objeto Estudio o false)
     * @param int $id
     * @return Estudio|false
     */
    public function getPorId($id) {
        $sql = "SELECT * FROM Estudios WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Estudio');
        return $stmt->fetch();
    }

    /**
     * Inserta un estudio (devuelve true/false si fue correcto)
     * @param int $alumnoId
     * @param int $cicloId
     * @param string $fechainicio (YYYY-MM-DD)
     * @param string|null $fechafin (YYYY-MM-DD)
     * @return bool
     */
    public function insertar($alumnoId, $cicloId, $fechainicio, $fechafin = null) {
        $sql = "INSERT INTO Estudios (alumno_id, ciclo_id, fechainicio, fechafin) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$alumnoId, $cicloId, $fechainicio, $fechafin]);
    }

    /**
     * Borra un estudio por id (devuelve true/false)
     * @param int $id
     * @return bool
     */
    public function borrarPorId($id) {
        $sql = "DELETE FROM Estudios WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Borra todos los estudios de un alumno
     * @param int $alumnoId
     * @return bool
     */
    public function borrarPorAlumnoId($alumnoId) {
        $sql = "DELETE FROM Estudios WHERE alumno_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$alumnoId]);
    }
}
