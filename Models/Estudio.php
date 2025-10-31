<?php

class Estudio {
  private $id;
  private $ciclo_id;
  private $nombre_ciclo;
  private $fechainicio;
  private $fechafin;

  public function getId() { return $this->id; }
  public function setId($id) { $this->id = $id; }

  public function getCicloId() { return $this->ciclo_id; }
  public function setCicloId($ciclo_id) { $this->ciclo_id = $ciclo_id; }

  public function getNombreCiclo() { return $this->nombre_ciclo; }
  public function setNombreCiclo($nombre_ciclo) { $this->nombre_ciclo = $nombre_ciclo; }

  public function getFechainicio() { return $this->fechainicio; }
  public function setFechainicio($fechainicio) { $this->fechainicio = $fechainicio; }

  public function getFechafin() { return $this->fechafin; }
  public function setFechafin($fechafin) { $this->fechafin = $fechafin; }

  public function __toString() {
    return "Estudio: {$this->nombre_ciclo} ({$this->fechainicio} - {$this->fechafin})";
  }
}