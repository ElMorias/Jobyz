<?php
require_once dirname(__DIR__) . '/autoloader.php';

class RepositorioEmpresa {
    private $db;
    public function __construct() {
        $this->db = DB::getConnection();
    }

    public function getTodas(): array {
        $stmt = $this->db->query("SELECT e.*, u.correo FROM empresa e JOIN users u ON e.user_id = u.id");
        $empresas = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $empresas[] = Empresa::fromArray($row);
        }
        return $empresas;
    }

    public function getNoValidadas(): array {
        $stmt = $this->db->query("SELECT e.*, u.correo FROM empresa e JOIN users u ON e.user_id = u.id WHERE e.validada = 0");
        $empresas = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $empresas[] = Empresa::fromArray($row);
        }
        return $empresas;
    }

    public function validarEmpresa($id): bool {
        $stmt = $this->db->prepare("UPDATE empresa SET validada = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function existeCorreo($correo): bool {
        $sql = "SELECT COUNT(*) FROM empresa WHERE pcontactoemail = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$correo]);
        return $stmt->fetchColumn() > 0;
    }

    public function existeCorreoExceptoId($correo, $id): bool {
        $sql = "SELECT COUNT(*) FROM empresa WHERE pcontactoemail = ? AND id != ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$correo, $id]);
        return $stmt->fetchColumn() > 0;
    }

    public function existeCif($cif): bool {
        $sql = "SELECT COUNT(*) FROM empresa WHERE cif = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$cif]);
        return $stmt->fetchColumn() > 0;
    }

    public function existeCifExceptoId($cif, $id): bool {
        $sql = "SELECT COUNT(*) FROM empresa WHERE cif = ? AND id != ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$cif, $id]);
        return $stmt->fetchColumn() > 0;
    }

    public function existeTelefono($telefono): bool {
        $sql = "SELECT COUNT(*) FROM empresa WHERE tlfcontacto = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$telefono]);
        return $stmt->fetchColumn() > 0;
    }

    public function existeTelefonoExceptoId($telefono, $id): bool {
        $sql = "SELECT COUNT(*) FROM empresa WHERE tlfcontacto = ? AND id != ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$telefono, $id]);
        return $stmt->fetchColumn() > 0;
    }

    public function getPorId($id): ?Empresa {
        $stmt = $this->db->prepare("SELECT e.*, u.correo FROM empresa e JOIN users u ON e.user_id = u.id WHERE e.id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? Empresa::fromArray($row) : null;
    }

    public function getPorUserId($userId): ?Empresa {
        $stmt = $this->db->prepare("SELECT e.*, u.correo FROM empresa e JOIN users u ON e.user_id = u.id WHERE e.user_id = ?");
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? Empresa::fromArray($row) : null;
    }

    public function getEmpresaIdPorUserId($user_id) {
        $stmt = $this->db->prepare("SELECT id FROM empresa WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    }

    public function getEmpresaValidadaporUserId($user_id){
        $stmt = $this->db->prepare("SELECT validada FROM empresa WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    }

    public function getEmpresasPaginadoFiltrado($pagina, $porPagina, $orden, $sentido, $buscar): array {
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
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':lim', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $empresasArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $empresasObjs = array_map(fn($row) => Empresa::fromArray($row), $empresasArr);
        $totalPaginas = max(1, ceil($totalEmpresas / $porPagina));
        return [
            'empresas' => $empresasObjs,
            'totalPaginas' => $totalPaginas
        ];
    }

    public function crearEmpresa($datos, $files = []): ?Empresa {
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare("INSERT INTO users (correo, contraseña, rol_id) VALUES (?, ?, ?)");
            $hash = password_hash($datos['contrasena'], PASSWORD_DEFAULT);
            $stmt->execute([
                $datos['correo'],
                $hash,
                $datos['rol_id']
            ]);
            $userId = $this->db->lastInsertId();

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
            $sql = "INSERT INTO empresa (nombre, direccion, cif, pcontacto, pcontactoemail, tlfcontacto, foto, validada, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
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
            $empresaId = $this->db->lastInsertId();

            $this->db->commit();
            return $this->getPorId($empresaId);
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Fallo al crear empresa: ' . $e->getMessage());
            return null;
        }
    }

    public function actualizar($id, $datos, $files): bool {
        $empresaAnterior = $this->getPorId($id);
        $fotoPathRel = $empresaAnterior ? $empresaAnterior->getFoto() : null;

        if (isset($files['nuevoLogo']) && $files['nuevoLogo']['error'] === 0) {
            $dirRelFoto = 'assets/uploads/empresa_logo/';
            $dirAbsFoto = $_SERVER['DOCUMENT_ROOT'] . '/Jobyz/' . $dirRelFoto;
            $ext = strtolower(pathinfo($files['nuevoLogo']['name'], PATHINFO_EXTENSION));
            $nombreFoto = 'logo_' . ($empresaAnterior ? $empresaAnterior->getUserId() : 'unknown') . '.' . $ext;
            $fotoPathAbs = $dirAbsFoto . $nombreFoto;
            $fotoPathRel = $dirRelFoto . $nombreFoto;
            move_uploaded_file($files['nuevoLogo']['tmp_name'], $fotoPathAbs);
        }

        $userId = $empresaAnterior ? $empresaAnterior->getUserId() : null;
        $sqlUser = "UPDATE users SET correo=? WHERE id=?";
        $stmtUser = $this->db->prepare($sqlUser);
        $stmtUser->execute([
            $datos['correo'],
            $userId
        ]);

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

    public function borrarPorEmpresaId($empresa_id): bool {
        $sql = "SELECT user_id FROM empresa WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$empresa_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return false;
        $user_id = $row['user_id'];

        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$user_id]);
    }
}
