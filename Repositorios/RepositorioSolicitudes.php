<?php
class RepositorioSolicitudes {
    private $db;

    public function __construct() {
        $this->db = DB::getConnection();
    }

    // Ejemplo: todas las solicitudes (admin), con campos enriquecidos por join
    public function todas(){
        $sql = "SELECT s.*, 
                    a.nombre AS alumno_nombre, u.correo AS user_correo, 
                    o.titulo AS oferta_titulo, e.nombre AS empresa_nombre
                FROM solicitud s
                JOIN alumno a ON a.id = s.alumno_id
                JOIN users u ON u.id = a.user_id
                JOIN oferta o ON o.id = s.oferta_id
                JOIN empresa e ON e.id = o.empresa_id";
        $stmt = $this->db->query($sql);
        $solicitudes = [];
        foreach ($stmt as $row) {
            $s = Solicitud::fromArray($row);
            $s->alumno_nombre = $row['alumno_nombre'];
            $s->alumno_email = $row['user_correo'];
            $s->oferta_titulo = $row['oferta_titulo'];
            $s->empresa_nombre = $row['empresa_nombre'];
            $solicitudes[] = $s;
        }
        return $solicitudes;
    }

    public function obtenerPorId($solicitudId)
    {
        $sql = "SELECT s.*, 
                    a.nombre AS alumno_nombre, 
                    a.apellido1 AS alumno_apellido1,
                    a.apellido2 AS alumno_apellido2,
                    u.correo AS alumno_email,
                    o.titulo AS oferta_titulo,
                    o.empresa_id AS empresa_id
                FROM solicitud s
                JOIN alumno a ON a.id = s.alumno_id
                JOIN users u ON u.id = a.user_id
                JOIN oferta o ON o.id = s.oferta_id
                WHERE s.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$solicitudId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Devuelve un objeto stdClass con todos los campos necesarios
            $solicitud = new stdClass();
            $solicitud->id = $row['id'];
            $solicitud->alumno_email = $row['alumno_email'];
            $solicitud->alumno_nombre = trim($row['alumno_nombre'] . ' ' . $row['alumno_apellido1'] . ' ' . $row['alumno_apellido2']);
            $solicitud->oferta_titulo = $row['oferta_titulo'];
            $solicitud->empresa_id = $row['empresa_id'];
            // Añade más propiedades si las necesitas
            return $solicitud;
        }
        return null;
    }

    // Ejemplo: solicitudes del alumno
    public function deAlumno($alumnoId){


        $sql = "SELECT s.*, o.titulo AS oferta_titulo, e.nombre AS empresa_nombre
                FROM solicitud s
                JOIN oferta o ON o.id = s.oferta_id
                JOIN empresa e ON e.id = o.empresa_id
                WHERE s.alumno_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$alumnoId]);
        $solicitudes = [];
        foreach ($stmt as $row) {
            $s = Solicitud::fromArray($row);
            $s->oferta_titulo = $row['oferta_titulo'];
            $s->empresa_nombre = $row['empresa_nombre'];
            $solicitudes[] = $s;
        }
        return $solicitudes;
    }

    // Ejemplo: solicitudes de una empresa (a sus ofertas)
    public function deEmpresa($empresaId){
        $sql = "SELECT s.*, a.nombre AS alumno_nombre, u.correo AS user_correo, 
                    o.titulo AS oferta_titulo
                FROM solicitud s
                JOIN oferta o ON o.id = s.oferta_id
                JOIN alumno a ON a.id = s.alumno_id
                JOIN users u ON u.id = a.user_id
                WHERE o.empresa_id = ? AND s.estado = 'pendiente'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$empresaId]);
        $solicitudes = [];
        foreach ($stmt as $row) {
            $s = Solicitud::fromArray($row);
            $s->alumno_nombre = $row['alumno_nombre'];
            $s->alumno_email = $row['user_correo'];
            $s->oferta_titulo = $row['oferta_titulo'];
            $solicitudes[] = $s;
        }
        return $solicitudes;
    }

    public function deOferta($oferta_id){
        $sql = "SELECT s.*, a.nombre AS alumno_nombre, u.correo AS user_correo
                FROM solicitud s
                JOIN alumno a ON a.id = s.alumno_id
                JOIN users u ON u.id = a.user_id
                WHERE s.oferta_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$oferta_id]);
        $res = [];
        foreach ($stmt as $row) {
            $s = Solicitud::fromArray($row);
            $s->alumno_nombre = $row['alumno_nombre'];
            $s->alumno_email = $row['user_correo'];
            $res[] = $s;
        }
        return $res;
    }

    public function insertar($alumno_id, $oferta_id) {
        $stmt = $this->db->prepare("INSERT INTO solicitud (alumno_id, oferta_id, estado) VALUES (?, ?, 'pendiente')");
        $stmt->execute([$alumno_id, $oferta_id]);
    }

    //(para evitar duplicidades)
    public function buscaDuplicada($alumno_id, $oferta_id) {
        $stmt = $this->db->prepare("SELECT id FROM solicitud WHERE alumno_id = ? AND oferta_id = ?");
        $stmt->execute([$alumno_id, $oferta_id]);
        return $stmt->fetchColumn();
    }



    public function eliminar($id){
    $stmt = $this->db->prepare("DELETE FROM solicitud WHERE id = ?");
    $stmt->execute([$id]);
    }

    public function aceptar($id){
        $stmt = $this->db->prepare("UPDATE solicitud SET estado = 'aceptada' WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function rechazar($id){
        $stmt = $this->db->prepare("UPDATE solicitud SET estado = 'rechazada' WHERE id = ?");
        $stmt->execute([$id]);
    }
}
?>