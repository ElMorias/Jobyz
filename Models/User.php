<?php

class User {
  private $id;
  private $correo;
  private $contraseña;
  private $rol_id;

  public function getId() { return $this->id; }
  public function setId($id) { $this->id = $id; }

  public function getCorreo() { return $this->correo; }
  public function setCorreo($correo) { $this->correo = $correo; }

  public function getContraseña() { return $this->contraseña; }
  public function setContraseña($contraseña) { $this->contraseña = $contraseña; }

  public function getRolId() { return $this->rol_id; }
  public function setRolId($rol_id) { $this->rol_id = $rol_id; }

}
?>