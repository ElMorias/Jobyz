<?php
/**
 * Helper para generación y validación de tokens persistentes (BBDD).
 */
class Security
{
    private $db;

    public function __construct() {
        $this->db = DB::getConnection();
    }

    public function generateToken($correo) {
        $fecha = date('Y-m-d H:i:s');
        $random = bin2hex(random_bytes(16));
        return hash('sha256', $correo . $fecha . $random);
    }

    public function createAndStoreToken($user_id, $correo) {
        $token = $this->generateToken($correo);
        $fecha_creacion = date('Y-m-d H:i:s');
        $fecha_caducidad = date('Y-m-d H:i:s', strtotime('+24 hours'));

        // Opcional: Borra otros tokens de ese user
        $this->db->prepare("DELETE FROM token WHERE user_id = ?")->execute([$user_id]);

        $stmt = $this->db->prepare("INSERT INTO token (token, fecha_creacion, fecha_caducidad, user_id)
                                     VALUES (?, ?, ?, ?)");
        $stmt->execute([$token, $fecha_creacion, $fecha_caducidad, $user_id]);
        return $token;
    }

    public function validateToken($user_id, $token) {
        $stmt = $this->db->prepare(
            "SELECT 1 FROM token WHERE token = ? AND user_id = ? AND fecha_caducidad > NOW()"
        );
        $stmt->execute([$token, $user_id]);
        return (bool)$stmt->fetchColumn();
    }

    public function invalidateToken($user_id, $token) {
        $stmt = $this->db->prepare("DELETE FROM token WHERE user_id = ? AND token = ?");
        $stmt->execute([$user_id, $token]);
    }

    public function cleanExpiredTokens() {
        $this->db->query("DELETE FROM token WHERE fecha_caducidad < NOW()");
    }
}
