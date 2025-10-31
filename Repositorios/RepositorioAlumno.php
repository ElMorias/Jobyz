<?php

require_once 'DB.php';
require_once 'models/Alumno.php';
require_once 'models/Estudio.php';

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

  // Crear alumno (requiere crear usuario primero)
  public function crear($datos) {
    $this->db->beginTransaction();

    try {
      // Crear usuario
      $sqlUser = "INSERT INTO Users (nombreuser, correo, contraseña, rol_id)
                  VALUES (?, ?, ?, ?)";
      $stmtUser = $this->db->prepare($sqlUser);
      $stmtUser->execute([
        $datos['nombreuser'],
        $datos['correo'],
        $datos['contraseña'],
        $datos['rol_id']
      ]);
      $userId = $this->db->lastInsertId();

      // Crear alumno
      $sqlAlumno = "INSERT INTO Alumno (nombre, apellido1, apellido2, fnacimiento, curriculum, dni, direccion, foto, user_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
      $stmtAlumno = $this->db->prepare($sqlAlumno);
      $stmtAlumno->execute([
        $datos['nombre'],
        $datos['apellido1'],
        $datos['apellido2'],
        $datos['fnacimiento'],
        $datos['curriculum'],
        $datos['dni'],
        $datos['direccion'],
        $datos['foto'],
        $userId
      ]);

      $this->db->commit();
      return true;

    } catch (Exception $e) {
      $this->db->rollBack();
      return false;
    }
  }
}