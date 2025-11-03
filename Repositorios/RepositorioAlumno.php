<?php
require_once dirname(__DIR__) . '/autoloader.php';

class RepositorioAlumno {
  private $db;

  public function __construct() {
    $this->db = DB::getConnection();
  }

  //Obtener un alumno completo por ID
  public function getAlumnoCompleto($id) {
    // 1. Obtener datos del alumno + correo
    $sql = "SELECT a.*, u.correo
            FROM Alumno a
            JOIN Users u ON a.user_id = u.id
            WHERE a.id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$id]);
    $datos = $stmt->fetch();

    if (!$datos) return null;

    // 2. Construir objeto Alumno
    $alumno = new Alumno();
    $alumno->setId($datos['id']);
    $alumno->setNombre($datos['nombre']);
    $alumno->setApellido1($datos['apellido1']);
    $alumno->setApellido2($datos['apellido2']);
    $alumno->setFnacimiento($datos['fnacimiento']);
    $alumno->setCurriculum($datos['curriculum']);
    $alumno->setDni($datos['dni']);
    $alumno->setDireccion($datos['direccion']);
    $alumno->setFoto($datos['foto']);
    $alumno->setUserId($datos['user_id']);

    // 3. Obtener estudios del alumno
    $sqlEstudios = "SELECT e.*, c.nombre AS nombre_ciclo
                    FROM Estudios e
                    JOIN Ciclo c ON e.ciclo_id = c.id
                    WHERE e.alumno_id = ?";
    $stmtEstudios = $this->db->prepare($sqlEstudios);
    $stmtEstudios->execute([$id]);
    $estudios = $stmtEstudios->fetchAll();

    foreach ($estudios as $e) {
      $estudio = new Estudio();
      $estudio->setId($e['id']);
      $estudio->setCicloId($e['ciclo_id']);
      $estudio->setNombreCiclo($e['nombre_ciclo']);
      $estudio->setFechainicio($e['fechainicio']);
      $estudio->setFechafin($e['fechafin']);
      $alumno->addEstudio($estudio);
    }

    return $alumno;
  }

  // Obtener todos los alumnos (solo datos básicos)
  public function getTodos() {
    $sql = "SELECT a.id, a.nombre, a.apellido1, a.apellido2, u.correo
            FROM Alumno a
            JOIN Users u ON a.user_id = u.id";
    $stmt = $this->db->query($sql);
    return $stmt->fetchAll();
  }

  //Borrar alumno por ID
  public function borrar($id) {
    $sql = "DELETE FROM Alumno WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$id]);
  }

  public function actualizar($id, $datos) {
    $sql = "UPDATE Alumno SET nombre = ?, apellido1 = ?, apellido2 = ?, fnacimiento = ?, curriculum = ?, dni = ?, direccion = ?, foto = ? WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
      $datos['nombre'],
      $datos['apellido1'],
      $datos['apellido2'],
      $datos['fnacimiento'],
      $datos['curriculum'],
      $datos['dni'],
      $datos['direccion'],
      $datos['foto'],
      $id
    ]);
  }


  // Crear alumno (requiere crear usuario primero)
  /* public function crear($datos) {
    $this->db->beginTransaction();

    foreach(['correo','contrasena','rol_id','nombre','apellido1','apellido2','fnacimiento','dni','direccion'] as $key) {
      if(!isset($datos[$key]) || $datos[$key]==='') {
        echo json_encode(['status'=>'error','mensaje'=>"Falta el campo obligatorio: $key"]); exit;
      }
    }

    file_put_contents("debug_datos.txt", print_r($datos, true));

    try {
      // Crear usuario
      $sqlUser = "INSERT INTO Users (correo, contraseña, rol_id)
                  VALUES (?, ?, ?)";
      $stmtUser = $this->db->prepare($sqlUser);
      $stmtUser->execute([
        $datos['correo'],
        $datos['contrasena'],
        $datos['rol_id']
      ]);
      $userId = $this->db->lastInsertId();

      
      $fotoPath = null;
      $cvPath = null;

      // ---- Guardar FOTO ----
      if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
          $extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
          $fotoPath = "assets/uploads/alumnos_foto/foto_" . $userId . "." . $extension;
          move_uploaded_file($_FILES['foto']['tmp_name'], $fotoPath);
      }

      // ---- Guardar CURRICULUM ----
      if (isset($_FILES['curriculum']) && $_FILES['curriculum']['error'] == 0) {
          $extension = strtolower(pathinfo($_FILES['curriculum']['name'], PATHINFO_EXTENSION));
          $cvPath = "assets/uploads/alumnos_cv/cv_" . $userId . "." . $extension;
          move_uploaded_file($_FILES['curriculum']['tmp_name'], $cvPath);
      }

      // Crear alumno
      $sqlAlumno = "INSERT INTO Alumno (nombre, apellido1, apellido2, fnacimiento, curriculum, dni, direccion, foto, user_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
      $stmtAlumno = $this->db->prepare($sqlAlumno);
      $stmtAlumno->execute([
        $datos['nombre'],
        $datos['apellido1'],
        $datos['apellido2'],
        $datos['fnacimiento'],
        $cvPath,
        $datos['dni'],
        $datos['direccion'],
        $fotoPath,
        $userId
      ]);

      // Obtener el id del alumno insertado
      $alumnoId = $this->db->lastInsertId();


      $numEstudios = count($datos['familia']); // O el que quieras, si todos igual de largos

      if(!empty($datos['familia'])){
          for ($i = 0; $i < $numEstudios; $i++) {
          $familia = $datos['familia'][$i];
          $ciclo = $datos['ciclo'][$i];
          $inicio = $datos['fechainicio'][$i];
          $fin = $datos['fechafin'][$i];
            
          $sqlEst = "INSERT INTO Estudios (alumno_id, ciclo_id, fechainicio, fechafin) VALUES (?, ?, ?, ?)";
          $stmtEst = $this->db->prepare($sqlEst);

          $stmtEst->execute([
            $alumnoId,
            $ciclo,
            $inicio,
            $fin
          ]);
        }
      }
     
       
      $alumnoId = $this->db->lastInsertId();
      $alumno = $this->getAlumnoCompleto($alumnoId); // esto trae el array completo
      $this->db->commit();
      return $alumno->toArray();

    } catch (Exception $e) {
       $this->db->rollBack();
       echo json_encode([
        'status' => 'error',
        'mensaje' => $e->getMessage()
      ]);
      exit;
    }
  } */

  public function crear($datos) {
    foreach(['correo','contrasena','rol_id','nombre','apellido1','apellido2','fnacimiento','dni','direccion'] as $campo){
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
        $sqlAlumno = "INSERT INTO Alumno (nombre, apellido1, apellido2, fnacimiento, curriculum, dni, direccion, foto, user_id)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtAlumno = $this->db->prepare($sqlAlumno);
        $stmtAlumno->execute([
            $datos['nombre'],
            $datos['apellido1'],
            $datos['apellido2'],
            $datos['fnacimiento'],
            $cvPathRel,
            $datos['dni'],
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
            $fechafin = $_POST['fechafin'][$i];

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
        return [
            'id' => $alumnoId,
            'nombre' => $datos['nombre'],
            'apellido1' => $datos['apellido1'],
            'apellido2' => $datos['apellido2'],
            'correo' => $datos['correo']
        ];
    } catch (Exception $e) {
        $this->db->rollBack();
        echo json_encode(['status'=>'error','mensaje'=>$e->getMessage()]); exit;
    }
}

}