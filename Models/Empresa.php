<?php
/**
 * Objeto de dominio para la entidad Empresa.
 */
class Empresa {
    private $id;
    private $nombre;
    private $direccion;
    private $cif;
    private $pcontacto;
    private $pcontactoemail;
    private $tlfcontacto;
    private $foto;
    private $validada;
    private $user_id;
    private $correo;

    // Métodos getter/setter
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
    public function getTlfcontacto() { return $this->tlfcontacto; }
    public function setTlfcontacto($tlfcontacto) { $this->tlfcontacto = $tlfcontacto; }
    public function getFoto() { return $this->foto; }
    public function setFoto($foto) { $this->foto = $foto; }
    public function getValidada() { return $this->validada; }
    public function setValidada($validada) { $this->validada = $validada; }
    public function getUserId() { return $this->user_id; }
    public function setUserId($user_id) { $this->user_id = $user_id; }
    public function getCorreo() { return $this->correo; }
    public function setCorreo($correo) { $this->correo = $correo; }

    // Factoria
    public static function fromArray(array $row): Empresa {
        $e = new self();
        $e->setId($row['id']);
        $e->setNombre($row['nombre']);
        $e->setDireccion($row['direccion']);
        $e->setCif($row['cif']);
        $e->setPcontacto($row['pcontacto']);
        $e->setPcontactoemail($row['pcontactoemail']);
        $e->setTlfcontacto($row['tlfcontacto'] ?? $row['tfcontacto'] ?? null);
        $e->setFoto($row['foto']);
        $e->setValidada($row['validada']);
        $e->setUserId($row['user_id']);
        $e->setCorreo($row['correo'] ?? null);
        return $e;
    }

    // Para API/vista
    public function toArray() {
        return [
            'id' => $this->getId(),
            'nombre' => $this->getNombre(),
            'direccion' => $this->getDireccion(),
            'cif' => $this->getCif(),
            'pcontacto' => $this->getPcontacto(),
            'pcontactoemail' => $this->getPcontactoemail(),
            'tlfcontacto' => $this->getTlfcontacto(),
            'foto' => $this->getFoto(),
            'validada' => $this->getValidada(),
            'user_id' => $this->getUserId(),
            'correo' => $this->getCorreo()
        ];
    }
}
