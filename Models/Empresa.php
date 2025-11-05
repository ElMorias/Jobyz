<?php
class Empresa {
    private $id;
    private $nombre;
    private $direccion;
    private $cif;
    private $pcontacto;
    private $pcontactoemail;
    private $tfcontacto;
    private $foto;
    private $validada;
    private $user_id;

    // Agrega aquí getters y setters de cada propiedad...
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getNombre() { return $this->nombre; }
    public function setNombre($nombre) { $this->nombre = $nombre; }

    public function getDireccion() { return $this->direccion; }
    public function setDireccion($direccion) { $this->direccion = $direccion; }

    public function getCif() { return $this->cif; }
    public function setCif($cif) { $this->cif = $cif; }

    public function getPcontacto() { return $this->pcontacto; }
    public function setPcontacto($pcontacto) { $this->pcontacto = $pcontacto; }

    public function getPcontactoemail() { return $this->pcontactoemail; }
    public function setPcontactoemail($pcontactoemail) { $this->pcontactoemail = $pcontactoemail; }

    public function getTfcontacto() { return $this->tfcontacto; }
    public function setTfcontacto($tfcontacto) { $this->tfcontacto = $tfcontacto; }

    public function getFoto() { return $this->foto; }
    public function setFoto($foto) { $this->foto = $foto; }

    public function getValidada() { return $this->validada; }
    public function setValidada($validada) { $this->validada = $validada; }

    public function getUserId() { return $this->user_id; }
    public function setUserId($user_id) { $this->user_id = $user_id; }
}
?>