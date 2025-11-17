<?php
class RepositorioUser {
    private $db;

    public function __construct() {
        $this->db = DB::getConnection();
    }

    public function findUser($correo) {
        $stmt = $this->db->prepare("SELECT id, correo, contraseña, rol_id FROM users WHERE correo = ?");
        $stmt->execute([$correo]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $usuario = new User();
            $usuario->setId($row['id']);
            $usuario->setCorreo($row['correo']);
            $usuario->setContraseña($row['contraseña']);
            $usuario->setRolId($row['rol_id']);
            return $usuario;
        } else {
            return null;
        }
    }

    public function getIdPorCorreo($correo){
        $stmt = $this->db->prepare("SELECT id FROM users WHERE correo = ?");
        $stmt->execute([$correo]);
        $id = $stmt->fetchColumn();
        return $id;
    }

    public function existeCorreo($correo) {
        $sql = "SELECT COUNT(*) FROM users WHERE correo = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$correo]);
        return $stmt->fetchColumn() > 0;
    }
    public function existeCorreoExceptoId($correo, $id) {
        $sql = "SELECT COUNT(*) FROM users WHERE correo = ? AND id != ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$correo, $id]);
        return $stmt->fetchColumn() > 0;
    }

}
?>