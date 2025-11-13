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


}
?>