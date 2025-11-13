<?php
class Oferta {
    public int $id;
    public string $titulo;
    public string $descripcion;
    public string $fechainicio;
    public int $empresa_id;
    public string $fechalimite;


    public ?string $empresa_nombre = null;

    public function __construct(
        int $id,
        string $titulo,
        string $descripcion,
        string $fechainicio,
        int $empresa_id,
        string $fechalimite
    ) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->descripcion = $descripcion;
        $this->fechainicio = $fechainicio;
        $this->empresa_id = $empresa_id;
        $this->fechalimite = $fechalimite;
    }

    public static function fromArray(array $row): Oferta {
        return new Oferta(
            (int)$row['id'],
            $row['titulo'],
            $row['descripcion'],
            $row['fechainicio'],
            (int)$row['empresa_id'],
            $row['fechalimite']
        );
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'fechainicio' => $this->fechainicio,
            'empresa_id' => $this->empresa_id,
            'fechalimite' => $this->fechalimite,
            'empresa_nombre' => $this->empresa_nombre
        ];
    }
}
?>