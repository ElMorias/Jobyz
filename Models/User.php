<?php
/**
 * Clase de dominio User.
 *
 * Representa a un usuario del sistema (login/autenticación).
 */
class User {
    /** @var int Identificador único de usuario */
    private $id;
    /** @var string Correo electrónico (login) */
    private $correo;
    /** @var string Contraseña cifrada */
    private $contraseña;
    /** @var int Identificador de rol (1=admin, 2=alumno, 3=empresa, etc.) */
    private $rol_id;

    // --- GETTERS & SETTERS ---

    /** @return int */
    public function getId() { return $this->id; }
    /** @param int $id */
    public function setId($id) { $this->id = $id; }

    /** @return string */
    public function getCorreo() { return $this->correo; }
    /** @param string $correo */
    public function setCorreo($correo) { $this->correo = $correo; }

    /** @return string */
    public function getContraseña() { return $this->contraseña; }
    /** @param string $contraseña */
    public function setContraseña($contraseña) { $this->contraseña = $contraseña; }

    /** @return int */
    public function getRolId() { return $this->rol_id; }
    /** @param int $rol_id */
    public function setRolId($rol_id) { $this->rol_id = $rol_id; }

    /**
     * Crea objeto User desde array asociativo (opcional utilidad/fábrica)
     * @param array $row
     * @return User
     */
    public static function fromArray(array $row): User {
        $user = new self();
        $user->setId($row['id']);
        $user->setCorreo($row['correo']);
        $user->setContraseña($row['contraseña']);
        $user->setRolId($row['rol_id']);
        return $user;
    }

    /**
     * Exporta el usuario como array asociativo (opcional para salida o debug)
     * @return array
     */
    public function toArray(): array {
        return [
            'id' => $this->getId(),
            'correo' => $this->getCorreo(),
            'contraseña' => $this->getContraseña(),
            'rol_id' => $this->getRolId()
        ];
    }
}
