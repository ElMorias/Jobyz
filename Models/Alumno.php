<?php
class Alumno {
  private $id;
  private $nombre;
  private $apellido1;
  private $apellido2;
  private $fnacimiento;
  private $curriculum;
  private $dni;
  private $telefono;
  private $direccion;
  private $foto;
  private $user_id;
  private $correo;
  private $estudios = [];

  public function getId() { return $this->id; }
  public function setId($id) { $this->id = $id; }

  public function getNombre() { return $this->nombre; }
  public function setNombre($nombre) { $this->nombre = $nombre; }

  public function getApellido1() { return $this->apellido1; }
  public function setApellido1($apellido1) { $this->apellido1 = $apellido1; }

  public function getApellido2() { return $this->apellido2; }
  public function setApellido2($apellido2) { $this->apellido2 = $apellido2; }

  public function getFnacimiento() { return $this->fnacimiento; }
  public function setFnacimiento($fnacimiento) { $this->fnacimiento = $fnacimiento; }

  public function getCurriculum() { return $this->curriculum; }
  public function setCurriculum($curriculum) { $this->curriculum = $curriculum; }

  public function getDni() { return $this->dni; }
  public function setDni($dni) { $this->dni = $dni; }

  public function getTelefono() { return $this->telefono; }
  public function setTelefono($telefono) { $this->telefono = $telefono; }

  public function getDireccion() { return $this->direccion; }
  public function setDireccion($direccion) { $this->direccion = $direccion; }

  public function getFoto() { return $this->foto; }
  public function setFoto($foto) { $this->foto = $foto; }

  public function getUserId() { return $this->user_id; }
  public function setUserId($user_id) { $this->user_id = $user_id; }

  public function getCorreo() { return $this->correo; }
  public function setCorreo($correo) { $this->correo = $correo; }

  public function getEstudios() { return $this->estudios; }
  public function setEstudios($estudios) { $this->estudios = $estudios; }
  public function addEstudio($estudio) { $this->estudios[] = $estudio; }

  public function __toString() {
    return "Alumno: {$this->nombre} {$this->apellido1} ({$this->dni})";
  }

  public function toArray() {
    return [
      'id' => $this->getId(),
      'user_id' => $this->getUserId(),
      'correo' => $this->getCorreo(),  // si lo tienes
      'nombre' => $this->getNombre(),
      'apellido1' => $this->getApellido1(),
      'apellido2' => $this->getApellido2(),
      'fnacimiento' => $this->getFnacimiento(),
      'curriculum' => $this->getCurriculum(),
      'dni' => $this->getDni(),
      'telefono' => $this->getTelefono(),
      'direccion' => $this->getDireccion(),
      'foto' => $this->getFoto(),
      'estudios' => array_map(
        fn($e) => method_exists($e, 'toArray') ? $e->toArray() : (array)$e,
        $this->estudios
      )
    ];
  }
}
