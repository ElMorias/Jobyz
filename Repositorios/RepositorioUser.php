<?php
/**
 * Repositorio para gestión de usuarios (login/autenticación y comprobaciones únicas).
 * Devuelve siempre objetos User, nunca arrays planos.
 */
class RepositorioUser {
    /** @var PDO */
    private $db;

    public function __construct() {
        $this->db = DB::getConnection();
    }

    /**
     * Busca un usuario por correo.
     * @param string $correo
     * @return User|null Objeto User o null si no existe
     */
    public function findUser($correo): ?User {
        $stmt = $this->db->prepare("SELECT id, correo, contraseña, rol_id FROM users WHERE correo = ?");
        $stmt->execute([$correo]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? User::fromArray($row) : null;
    }

    /**
     * Obtiene el ID de usuario por su correo.
     * @param string $correo
     * @return mixed Id de usuario o false si no existe
     */
    public function getIdPorCorreo($correo) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE correo = ?");
        $stmt->execute([$correo]);
        return $stmt->fetchColumn();
    }

    /**
     * Comprueba si existe el correo (para registro/edición).
     * @param string $correo
     * @return bool
     */
    public function existeCorreo($correo): bool {
        $sql = "SELECT COUNT(*) FROM users WHERE correo = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$correo]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Comprueba si un correo existe excluyendo un ID concreto (para edición).
     * @param string $correo
     * @param int $id
     * @return bool
     */
    public function existeCorreoExceptoId($correo, $id): bool {
        $sql = "SELECT COUNT(*) FROM users WHERE correo = ? AND id != ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$correo, $id]);
        return $stmt->fetchColumn() > 0;
    }
}
