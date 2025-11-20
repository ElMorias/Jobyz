<?php

/**
 * Repositorio para la tabla ciclo.
 * Acceso solo para consulta simple, devuelve arrays asociativos.
 */
class RepositorioCiclo
{
    /** @var PDO */
    private $db;

    public function __construct()
    {
        $this->db = DB::getConnection();
    }

    /**
     * Devuelve todos los ciclos de una familia dada
     * @param int $familia_id
     * @return array [ ['id'=>..., 'nombre'=>...], ... ]
     */
    public function getByFamilia($familia_id)
    {
        $stmt = $this->db->prepare("SELECT id, nombre FROM ciclo WHERE familia_id = ?");
        $stmt->execute([$familia_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Devuelve todos los ciclos (id y nombre)
     * @return array [ ['id'=>..., 'nombre'=>...], ... ]
     */
    public function getAll()
    {
        $sql = "SELECT id, nombre FROM ciclo";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Devuelvve los ciclos que en los que mas alunmos estan matruculados
    public function topCiclosPorAlumnos() {
        $sql = "
            SELECT ciclo.nombre, COUNT(*) as total
            FROM estudios
            JOIN ciclo ON ciclo.id = estudios.ciclo_id
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

}

