<?php

class Alumno {
  private $id;
  private $nombre;
  private $apellido1;
  private $apellido2;
  private $fnacimiento;
  private $curriculum;
  private $dni;
  private $direccion;
  private $foto;
  private $user_id;
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

  public function getDireccion() { return $this->direccion; }
  public function setDireccion($direccion) { $this->direccion = $direccion; }

  public function getFoto() { return $this->foto; }
  public function setFoto($foto) { $this->foto = $foto; }

  public function getUserId() { return $this->user_id; }
  public function setUserId($user_id) { $this->user_id = $user_id; }

  public function getEstudios() { return $this->estudios; }
  public function setEstudios($estudios) { $this->estudios = $estudios; }
  public function addEstudio($estudio) { $this->estudios[] = $estudio; }

  public function __toString() {
    return "Alumno: {$this->nombre} {$this->apellido1} ({$this->dni})";
  }
}