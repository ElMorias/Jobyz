<?php
/**
 * Repositorio para la tabla familia.
 * Devuelve arrays asociativos (id, nombre).
 */
class RepositorioFamilia
{
    /** @var PDO */
    private $db;

    public function __construct()
    {
        $this->db = DB::getConnection();
    }

    /**
     * Devuelve todas las familias (solo id y nombre).
     * @return array [['id' => ..., 'nombre' => ...], ...]
     */
    public function getAll()
    {
        $stmt = $this->db->prepare("SELECT id, nombre FROM familia");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
