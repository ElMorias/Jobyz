<?php
/**
 * Objeto de dominio para la entidad Estudio
 */
class Estudio {
    private $id;
    private $alumno_id;
    private $ciclo_id;
    private $fechainicio;
    private $fechafin;

    public function getId()            { return $this->id; }
    public function setId($id)         { $this->id = $id; }
    public function getAlumnoId()      { return $this->alumno_id; }
    public function setAlumnoId($a)    { $this->alumno_id = $a; }
    public function getCicloId()       { return $this->ciclo_id; }
    public function setCicloId($c)     { $this->ciclo_id = $c; }
    public function getFechainicio()   { return $this->fechainicio; }
    public function setFechainicio($f) { $this->fechainicio = $f; }
    public function getFechafin()      { return $this->fechafin; }
    public function setFechafin($f)    { $this->fechafin = $f; }

    public function __toString() {
        return "Estudio: {$this->ciclo_id} ({$this->fechainicio} - {$this->fechafin})";
    }

    public function toArray() {
        return [
            'id'          => $this->getId(),
            'alumno_id'   => $this->getAlumnoId(),
            'ciclo_id'    => $this->getCicloId(),
            'fechainicio' => $this->getFechainicio(),
            'fechafin'    => $this->getFechafin()
        ];
    }
}
