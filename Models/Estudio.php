<?php
class Estudio {
  private $id;
  private $alumno_id;     // <- Añadido
  private $ciclo_id;
  private $fechainicio;
  private $fechafin;

  public function getId() { return $this->id; }
  public function setId($id) { $this->id = $id; }

  public function getAlumnoId() { return $this->alumno_id; }
  public function setAlumnoId($alumno_id) { $this->alumno_id = $alumno_id; }

  public function getCicloId() { return $this->ciclo_id; }
  public function setCicloId($ciclo_id) { $this->ciclo_id = $ciclo_id; }

  public function getFechainicio() { return $this->fechainicio; }
  public function setFechainicio($fechainicio) { $this->fechainicio = $fechainicio; }

  public function getFechafin() { return $this->fechafin; }
  public function setFechafin($fechafin) { $this->fechafin = $fechafin; }

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
?>