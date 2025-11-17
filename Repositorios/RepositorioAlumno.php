<?php
require_once dirname(__DIR__) . '/autoloader.php';

class RepositorioAlumno {
  private $db;

  public function __construct() {
    $this->db = DB::getConnection();
  }

  //Obtener un alumno completo por ID
  public function getAlumnoCompleto($alumnoId) {
      // Obtener datos del alumno + usuario
      $sql = "SELECT a.*, u.correo
              FROM Alumno a
              JOIN Users u ON a.user_id = u.id
              WHERE a.id = ?";
      $stmt = $this->db->prepare($sql);
      $stmt->execute([$alumnoId]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if (!$row) return null;

      // Crear objeto Alumno y settear propiedades
      $alumno = new Alumno();
      $alumno->setId($row['id']);
      $alumno->setUserId($row['user_id']);
      $alumno->setNombre($row['nombre']);
      $alumno->setApellido1($row['apellido1']);
      $alumno->setApellido2($row['apellido2']);
      $alumno->setFnacimiento($row['fnacimiento']);
      $alumno->setCurriculum($row['curriculum']);
      $alumno->setDni($row['dni']);
      $alumno->setTelefono($row['telefono']);
      $alumno->setDireccion($row['direccion']);
      $alumno->setFoto($row['foto']);
      $alumno->setValidado($row['validado']);
      $alumno->setCorreo($row['correo']);

      // Obtener estudios relacionados
      $sqlEst = "SELECT * FROM Estudios WHERE alumno_id = ?";
      $stmtEst = $this->db->prepare($sqlEst);
      $stmtEst->execute([$alumnoId]);
      $estudios = [];
      foreach ($stmtEst->fetchAll(PDO::FETCH_ASSOC) as $rowE) {
          $e = new Estudio();
          $e->setId($rowE['id']);
          $e->setAlumnoId($rowE['alumno_id']);
          $e->setCicloId($rowE['ciclo_id']);
          $e->setFechainicio($rowE['fechainicio']);
          $e->setFechafin($rowE['fechafin']);
          $estudios[] = $e;
      }
      $alumno->setEstudios($estudios);

      return $alumno;
  }
  // Obtener todos los alumnos (ojo lo he cambiado a ver si no falla), no tiene estudios
  public function getTodos() {
    $sql = "SELECT a.*, u.correo
            FROM Alumno a
            JOIN Users u ON a.user_id = u.id";
    $stmt = $this->db->query($sql);
    return $stmt->fetchAll();
  }

    public function getAlumnoIdPorUserId($user_id){
        $stmt = $this->db->prepare("SELECT id FROM alumno WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn() ?: null;
    }

    public function existeDni($dni) {
        $sql = "SELECT COUNT(*) FROM alumno WHERE dni = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$dni]);
        return $stmt->fetchColumn() > 0;
    }

    public function existeDniExceptoId($dni, $id) {
        $sql = "SELECT COUNT(*) FROM alumno WHERE dni = ? AND id != ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$dni, $id]);
        return $stmt->fetchColumn() > 0;
    }

    public function existeTelefono($telefono) {
        $sql = "SELECT COUNT(*) FROM alumno WHERE telefono = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$telefono]);
        return $stmt->fetchColumn() > 0;
    }

    public function existeTelefonoExceptoId($telefono, $id) {
        $sql = "SELECT COUNT(*) FROM alumno WHERE telefono = ? AND id != ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$telefono, $id]);
        return $stmt->fetchColumn() > 0;
    }
    
  //Borrar alumno por ID
    public function borrarPorAlumnoId($alumnoId) {
        // Buscar el user_id asociado al alumno
        $stmt = $this->db->prepare("SELECT user_id FROM Alumno WHERE id = ?");
        $stmt->execute([$alumnoId]);
        $userId = $stmt->fetchColumn();

        if ($userId) {
            // Borrar directamente el usuario
            $stmtDel = $this->db->prepare("DELETE FROM Users WHERE id = ?");
            $stmtDel->execute([$userId]);
            return $stmtDel->rowCount() > 0;
        }
        return false;
    }


    public function actualizar($id, $datos) {
        // Obtener el alumno anterior
        $alumnoAnterior = $this->getAlumnoCompleto($id);
        $userId = $alumnoAnterior->getUserId();

         if (!empty($datos['contrasena'])) {
            $hash = password_hash($datos['contrasena'], PASSWORD_DEFAULT);
            // Actualiza la contraseña en la tabla de usuarios
            $sqlUser = "UPDATE users SET contraseña = ? WHERE id = ?";
            $stmtUser = $this->db->prepare($sqlUser);
            $stmtUser->execute([$hash, $userId]);
        }

        // Para la foto y el cv
        $dirRelFoto = 'assets/uploads/alumnos_foto/';
        $dirAbsFoto = $_SERVER['DOCUMENT_ROOT'] . '/Jobyz/' . $dirRelFoto;
        $dirRelCv = 'assets/uploads/alumnos_cv/';
        $dirAbsCv = $_SERVER['DOCUMENT_ROOT'] . '/Jobyz/' . $dirRelCv;

        // FOTO
        $fotoPathRel = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
            $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $nombreFoto = 'foto_' . $userId . '.' . $ext;
            $fotoPathAbs = $dirAbsFoto . $nombreFoto;
            $fotoPathRel = $dirRelFoto . $nombreFoto;
            move_uploaded_file($_FILES['foto']['tmp_name'], $fotoPathAbs);
        } else {
            // Mantener foto anterior si no se sube nueva
            $fotoPathRel = $alumnoAnterior->getFoto();
        }

        // CURRICULUM
        $cvPathRel = null;
        if (isset($_FILES['curriculum']) && $_FILES['curriculum']['error'] === 0) {
            $ext = strtolower(pathinfo($_FILES['curriculum']['name'], PATHINFO_EXTENSION));
            $nombreCv = 'cv_' . $userId . '.' . $ext;
            $cvPathAbs = $dirAbsCv . $nombreCv;
            $cvPathRel = $dirRelCv . $nombreCv;
            move_uploaded_file($_FILES['curriculum']['tmp_name'], $cvPathAbs);
        } else {
            // Mantener cv anterior si no se sube nuevo
            $cvPathRel = $alumnoAnterior->getCurriculum();
        }



        $sql = "UPDATE Alumno SET nombre = ?, apellido1 = ?, apellido2 = ?, fnacimiento = ?, curriculum = ?, dni = ?, telefono = ?, direccion = ?, foto = ?, validado = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $datos['nombre'],
            $datos['apellido1'],
            $datos['apellido2'],
            $datos['fnacimiento'],
            $cvPathRel,
            $datos['dni'],
            $datos['telefono'],
            $datos['direccion'],
            $fotoPathRel,
            $datos['validado'],
            $id
        ]);

        // Array con todos los estudios recibidos del formulario
        $numEstudios = isset($_POST['familia']) ? count($_POST['familia']) : 0;

        for ($i = 0; $i < $numEstudios; $i++) {
            $familia = $_POST['familia'][$i];
            $ciclo = $_POST['ciclo'][$i];
            $fechainicio = $_POST['fechainicio'][$i];
            $fechafin = empty($_POST['fechafin'][$i]) ? null : $_POST['fechafin'][$i];


            $sqlEst = "INSERT INTO Estudios (alumno_id, ciclo_id, fechainicio, fechafin)
                    VALUES (?, ?, ?, ?)";
            $stmtEst = $this->db->prepare($sqlEst);
            $stmtEst->execute([
                $id,     
                $ciclo,
                $fechainicio,
                $fechafin
            ]);
        }

        return true;

    }


    public function crear($datos) {
        foreach(['correo','contrasena','rol_id','nombre','apellido1','fnacimiento','dni','telefono','direccion','validado'] as $campo){
            if(!isset($datos[$campo]) || $datos[$campo]==='') {
                echo json_encode(['status'=>'error', 'mensaje'=>"Falta campo $campo"]);
                exit;
            }
        }
        $this->db->beginTransaction();
        try {
            $sqlUser = "INSERT INTO Users (correo, contraseña, rol_id) VALUES (?, ?, ?)";
            $stmtUser = $this->db->prepare($sqlUser);
            $hash = password_hash($datos['contrasena'], PASSWORD_DEFAULT);
            $stmtUser->execute([
                $datos['correo'],
                $hash,
                $datos['rol_id']
            ]);

            $userId = $this->db->lastInsertId();

            // Definir rutas relativas y absolutas 
            // esto es para guardar en la bd la relativa y asi poder usarla en la web
            // y en servidor la absoluta para mover el archivo
            $dirRelFoto = 'assets/uploads/alumnos_foto/';
            $dirAbsFoto = $_SERVER['DOCUMENT_ROOT'] . '/Jobyz/' . $dirRelFoto;

            $dirRelCv = 'assets/uploads/alumnos_cv/';
            $dirAbsCv = $_SERVER['DOCUMENT_ROOT'] . '/Jobyz/' . $dirRelCv;

            // Crear carpetas si no existen
            // siempres se hace por si acaso
            if (!is_dir($dirAbsFoto)) mkdir($dirAbsFoto, 0777, true);
            if (!is_dir($dirAbsCv)) mkdir($dirAbsCv, 0777, true);

            // todo a null pri
            $fotoPathRel = null;
            $cvPathRel = null;

            // Guardar FOTO
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
                $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                $nombreFoto = 'foto_' . $userId . '.' . $ext;
                $fotoPathAbs = $dirAbsFoto . $nombreFoto;
                $fotoPathRel = $dirRelFoto . $nombreFoto;
                move_uploaded_file($_FILES['foto']['tmp_name'], $fotoPathAbs);
            }

            // Guardar CURRICULUM
            if (isset($_FILES['curriculum']) && $_FILES['curriculum']['error'] === 0) {
                $ext = strtolower(pathinfo($_FILES['curriculum']['name'], PATHINFO_EXTENSION));
                $nombreCv = 'cv_' . $userId . '.' . $ext;
                $cvPathAbs = $dirAbsCv . $nombreCv;
                $cvPathRel = $dirRelCv . $nombreCv;
                move_uploaded_file($_FILES['curriculum']['tmp_name'], $cvPathAbs);
            }

            // Al hacer el INSERT en la tabla Alumno:
            $sqlAlumno = "INSERT INTO Alumno (nombre, apellido1, apellido2, fnacimiento, curriculum, dni,telefono, direccion, user_id, foto, validado)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtAlumno = $this->db->prepare($sqlAlumno);
            $stmtAlumno->execute([
                $datos['nombre'],
                $datos['apellido1'],
                $datos['apellido2'],
                $datos['fnacimiento'],
                $cvPathRel,
                $datos['dni'],
                $datos['telefono'],
                $datos['direccion'],
                $userId,
                $fotoPathRel,
                $datos['validado']
                
            ]);
            $alumnoId = $this->db->lastInsertId();

            // Array con todos los estudios recibidos del formulario
            $numEstudios = isset($_POST['familia']) ? count($_POST['familia']) : 0;

            for ($i = 0; $i < $numEstudios; $i++) {
                $familia = $_POST['familia'][$i];
                $ciclo = $_POST['ciclo'][$i];
                $fechainicio = $_POST['fechainicio'][$i];
                $fechafin = empty($_POST['fechafin'][$i]) ? null : $_POST['fechafin'][$i];


                $sqlEst = "INSERT INTO Estudios (alumno_id, ciclo_id, fechainicio, fechafin)
                        VALUES (?, ?, ?, ?)";
                $stmtEst = $this->db->prepare($sqlEst);
                $stmtEst->execute([
                    $alumnoId,     
                    $ciclo,
                    $fechainicio,
                    $fechafin
                ]);
            }


            $this->db->commit();
            // Devuelvo ONLY datos puros, nada de objetos:
            /*return [
                'id' => $alumnoId,
                'nombre' => $datos['nombre'],
                'apellido1' => $datos['apellido1'],
                'apellido2' => $datos['apellido2'],
                'correo' => $datos['correo']
            ];*/
            $alumno = $this->getAlumnoCompleto($alumnoId);
            return $alumno;
        } catch (Exception $e) {
            $this->db->rollBack();
            echo json_encode(['status'=>'error','mensaje'=>$e->getMessage()]); exit;
        }
    }

    public function cargaMasiva($alumnos, $familia, $ciclo) {
        $insertados = 0;
        $fallos = 0;
        $fallosEmails = [];
        $alumnosOk = [];  
        $rolAlumno = 2;

        try {
            $this->db->beginTransaction();

            foreach ($alumnos as $user) {
                $nombre   = $user['nombre']   ?? '';
                $apellido = $user['apellido'] ?? '';
                $correo   = $user['correo']   ?? '';
                $dni      = $user['dni']      ?? '';
                $telefono = '';

                if (!$nombre || !$apellido || !$correo || !$dni) {
                    $fallos++;
                    $fallosEmails[] = $correo;
                    continue;
                }

                $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE correo = ?");
                $stmt->execute([$correo]);
                if ($stmt->fetchColumn() > 0) {
                    $fallos++;
                    $fallosEmails[] = $correo;
                    continue;
                }

                $contrasena = password_hash("temp_pass", PASSWORD_DEFAULT);


                $userStmt = $this->db->prepare("INSERT INTO users (correo, contraseña, rol_id) VALUES (?, ?, ?)");
                if (!$userStmt->execute([$correo, $contrasena, $rolAlumno])) {
                    $fallos++; $fallosEmails[] = $correo; continue;
                }
                $userId = $this->db->lastInsertId();

                $alumStmt = $this->db->prepare("INSERT INTO alumno
                    (user_id, nombre, apellido1, apellido2, fnacimiento, curriculum, dni, telefono, direccion, foto, validado)
                    VALUES (?, ?, ?, '', CURDATE(), null, ?, ?, '', null, 0)");
                if (!$alumStmt->execute([$userId, $nombre, $apellido, $dni, $telefono])) {
                    $fallos++; $fallosEmails[] = $correo;
                    $this->db->prepare("DELETE FROM users WHERE id = ?")->execute([$userId]);
                    continue;
                }

                $alumnoId = $this->db->lastInsertId();

                $estStmt = $this->db->prepare("INSERT INTO estudios (alumno_id, ciclo_id, fechainicio, fechafin) VALUES (?, ?, CURDATE(), CURDATE())");
                if (!$estStmt->execute([$alumnoId, $ciclo])) {
                    $fallos++; $fallosEmails[] = $correo;
                    continue;
                }

                // Recupera el objeto Alumno ya montado
                $alumnoObj = $this->getAlumnoCompleto($alumnoId);
                if ($alumnoObj) {
                    $alumnosOk[] = $alumnoObj->toArray();
                }

                $insertados++;
            }

            if ($fallos > 0 && $insertados == 0) {
                $this->db->rollBack();
            } else {
                $this->db->commit();
            }

            return [
                'ok'           => true,
                'insertados'   => $insertados,
                'fallos'       => $fallos,
                'fallosEmails' => $fallosEmails,
                'alumnos'      => $alumnosOk
            ];
        } catch (\Exception $e) {
            $this->db->rollBack();
            return [
                'ok' => false,
                'insertados' => 0,
                'fallos' => count($alumnos),
                'fallosEmails' => ['Excepción: ' . $e->getMessage()],
                'alumnos' => []
            ];
        }
    }




}