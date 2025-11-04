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

  //Borrar alumno por ID
    public function borrarPorAlumnoId($alumnoId) {
        // Buscar el user_id asociado al alumno
        $stmt = $this->db->prepare("SELECT user_id FROM Alumno WHERE id = ?");
        $stmt->execute([$alumnoId]);
        $userId = $stmt->fetchColumn();

        if ($userId) {
            // Borrar directamente el usuario (cascade hará el resto)
            $stmtDel = $this->db->prepare("DELETE FROM Users WHERE id = ?");
            $stmtDel->execute([$userId]);
            return $stmtDel->rowCount() > 0;
        }
        return false;
    }


  public function actualizar($id, $datos) {
    $sql = "UPDATE Alumno SET nombre = ?, apellido1 = ?, apellido2 = ?, fnacimiento = ?, curriculum = ?, dni = ?, telefono = ? ,direccion = ?, foto = ? WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
      $datos['nombre'],
      $datos['apellido1'],
      $datos['apellido2'],
      $datos['fnacimiento'],
      $datos['curriculum'],
      $datos['dni'],
      $datos['telefono'],
      $datos['direccion'],
      $datos['foto'],
      $id
    ]);
  }

  public function crear($datos) {
    foreach(['correo','contrasena','rol_id','nombre','apellido1','apellido2','fnacimiento','dni','telefono','direccion'] as $campo){
        if(!isset($datos[$campo]) || $datos[$campo]==='') {
            echo json_encode(['status'=>'error', 'mensaje'=>"Falta campo $campo"]);
            exit;
        }
    }
    $this->db->beginTransaction();
    try {
        $sqlUser = "INSERT INTO Users (correo, contraseña, rol_id) VALUES (?, ?, ?)";
        $stmtUser = $this->db->prepare($sqlUser);
        $stmtUser->execute([
            $datos['correo'],
            $datos['contrasena'],
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
        $sqlAlumno = "INSERT INTO Alumno (nombre, apellido1, apellido2, fnacimiento, curriculum, dni,telefono, direccion, foto, user_id)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
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
            $fotoPathRel,
            $userId
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
                $alumnoId,      // o el id que corresponda
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

}