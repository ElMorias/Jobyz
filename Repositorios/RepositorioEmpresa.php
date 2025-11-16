<?php
require_once dirname(__DIR__) . '/autoloader.php';

class RepositorioEmpresa {
    private $db;
    
    public function __construct() {
        $this->db = DB::getConnection();
    }

    public function getTodas() {
        $stmt = $this->db->query("SELECT * FROM empresa");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNoValidadas() {
        $stmt = $this->db->query('SELECT * FROM empresa WHERE validada = 0');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function validarEmpresa($id) {
    $stmt = $this->db->prepare('UPDATE empresa SET validada = 1 WHERE id = ?');
    return $stmt->execute([$id]);
}



    //--------------------------FINDBYID-----------------------------------------//
    public function getPorId($id) {
        $stmt = $this->db->prepare("SELECT e.*,u.correo
                                    FROM empresa e join users u on e.user_id = u.id
                                    WHERE e.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPorUserId($userId) {
        $stmt = $this->db->prepare("SELECT e.*, u.correo
                                    FROM empresa e JOIN users u ON e.user_id = u.id
                                    WHERE e.user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getEmpresaIdPorUserId($user_id){
        $stmt = $this->db->prepare("SELECT id FROM empresa WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $id = $stmt->fetchColumn();
        return $id;
    }

    public function getEmpresaValidadaporUserId($user_id){
        $stmt = $this->db->prepare("SELECT validada FROM empresa WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $validada = $stmt->fetchColumn();
        return $validada;
    }

    public function getEmpresasPaginadoFiltrado($pagina, $porPagina, $orden, $sentido, $buscar) {
        $camposOrden = ['id', 'nombre', 'cif', 'pcontactoemail', 'tlfcontacto'];
        if (!in_array($orden, $camposOrden)) $orden = 'id';
        $sentido = (strtoupper($sentido) === 'DESC') ? 'DESC' : 'ASC';
        $offset = ($pagina - 1) * $porPagina;
        $params = [];
        $where = '';
        if ($buscar !== '') {
            $where = "WHERE nombre LIKE :buscar";
            $params[':buscar'] = "%{$buscar}%";
        }
        $sqlTotal = "SELECT COUNT(*) FROM empresa $where";
        $stmtTotal = $this->db->prepare($sqlTotal);
        $stmtTotal->execute($params);
        $totalEmpresas = $stmtTotal->fetchColumn();
        $sql = "SELECT * FROM empresa $where ORDER BY $orden $sentido LIMIT :lim OFFSET :off";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k=>$v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':lim', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $totalPaginas = max(1, ceil($totalEmpresas/$porPagina));
        return [
            'empresas' => $empresas,
            'totalPaginas' => $totalPaginas
        ];
    }



    //------------Funcion para crear una empresa-----------------//
    public function crearEmpresa($datos, $files = []) {
        try {
            $this->db->beginTransaction();

            // 1. Insertar el usuario
            $sqlUser = "INSERT INTO users (correo, contraseña, rol_id) VALUES (?, ?, ?)";
            $hash = password_hash($datos['contrasena'], PASSWORD_DEFAULT);
            $stmtUser->execute([
                $datos['correo'],
                $hash,
                $datos['rol_id']
            ]);
            
            // Recuperar el user_id generado
            $userId = $this->db->lastInsertId();

            // 2. Procesar foto
            $fotoPathRel = null;
            if (!empty($files['foto']['name'])) {
            $dirRelFoto = 'assets/uploads/empresa_logo/';
            $dirAbsFoto = $_SERVER['DOCUMENT_ROOT'] . '/Jobyz/' . $dirRelFoto;
            $ext = strtolower(pathinfo($files['foto']['name'], PATHINFO_EXTENSION));
            $nombreFoto = 'logo_' . $userId . '.' . $ext;
            $fotoPathAbs = $dirAbsFoto . $nombreFoto;
            $fotoPathRel = $dirRelFoto . $nombreFoto;
            move_uploaded_file($files['foto']['tmp_name'], $fotoPathAbs);
            }

            // 3. Insertar la empresa
            $sqlEmpresa = "INSERT INTO empresa (nombre, direccion, cif, pcontacto, pcontactoemail, tlfcontacto, foto, validada, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtEmp = $this->db->prepare($sqlEmpresa);
            $stmtEmp->execute([
            $datos['nombre'],
            $datos['direccion'],
            $datos['cif'],
            $datos['pcontacto'],
            $datos['pcontactoemail'],
            $datos['tfcontacto'],
            $fotoPathRel,
            $datos['validada'] ?? 0,
            $userId
            ]);

            $this->db->commit();
            return true;
        } catch(Exception $e) {
            $this->db->rollBack();
            error_log('Fallo al crear empresa: ' . $e->getMessage());
            return false;
        }
    }

    //-----------------------UPDATE----------------------------------//
   public function actualizar($id, $datos, $files) {
        $empresaAnterior = $this->getPorId($id);
        $fotoPathRel = $empresaAnterior['foto'] ?? null;

        // Gestionar nuevo logo 
        if (isset($files['nuevoLogo']) && $files['nuevoLogo']['error'] === 0) {
            $dirRelFoto = 'assets/uploads/empresa_logo/';
            $dirAbsFoto = $_SERVER['DOCUMENT_ROOT'] . '/Jobyz/' . $dirRelFoto;
            $ext = strtolower(pathinfo($files['nuevoLogo']['name'], PATHINFO_EXTENSION));
            $nombreFoto = 'logo_' . $empresaAnterior['user_id'] . '.' . $ext;
            $fotoPathAbs = $dirAbsFoto . $nombreFoto;
            $fotoPathRel = $dirRelFoto . $nombreFoto;
            move_uploaded_file($files['nuevoLogo']['tmp_name'], $fotoPathAbs);
        }

        // Si se cambia el correo
        $sqlUser = "UPDATE users SET correo=? WHERE id=?";
        $stmtUser = $this->db->prepare($sqlUser);
        $stmtUser->execute([
            $datos['correo'],
            $empresaAnterior['user_id']
        ]);

        // update empresa
        $sqlEmpresa = "UPDATE empresa SET nombre=?, direccion=?, cif=?, pcontacto=?, pcontactoemail=?, tlfcontacto=?, foto=?, validada=? WHERE id=?";
        $stmtEmp = $this->db->prepare($sqlEmpresa);
        return $stmtEmp->execute([
            $datos['nombre'],
            $datos['direccion'],
            $datos['cif'],
            $datos['pcontacto'],
            $datos['pcontactoemail'],
            $datos['tlfcontacto'],
            $fotoPathRel,
            $datos['validada'] ?? 0,
            $id
        ]);
    }

    //---------------------------DELETE-------------------------------//
   public function borrarPorEmpresaId($empresa_id) {
        // 1. Buscar el user_id correspondiente a esa empresa
        $sql = "SELECT user_id FROM empresa WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$empresa_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return false; // Empresa no existe
        $user_id = $row['user_id'];
        // 2. Borrar el usuario, lo que borra empresa en cascada
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$user_id]);
    }

}
?>