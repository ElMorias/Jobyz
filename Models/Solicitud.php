<?php
/**
 * Clase entidad para solicitud (patrón Value Object)
 */
class Solicitud
{
    public int $id;
    public int $alumno_id;
    public int $oferta_id;
    public string $fecha_solicitud;
    public string $estado;

    // Extras para enriquecimiento (pueden ser null)
    public ?string $alumno_nombre = null;
    public ?string $alumno_email = null;
    public ?string $oferta_titulo = null;
    public ?string $empresa_nombre = null;

    public function __construct(
        int $id,
        int $alumno_id,
        int $oferta_id,
        string $fecha_solicitud,
        string $estado
    ) {
        $this->id = $id;
        $this->alumno_id = $alumno_id;
        $this->oferta_id = $oferta_id;
        $this->fecha_solicitud = $fecha_solicitud;
        $this->estado = $estado;
    }

    /**
     * Fábrica/ensamblador para crear Solicitud desde un array.
     */
    public static function fromArray(array $row): Solicitud {
        return new Solicitud(
            (int)$row['id'],
            (int)$row['alumno_id'],
            (int)$row['oferta_id'],
            $row['fecha_solicitud'],
            $row['estado']
        );
    }

    /**
     * Conversión a array asociativo (exportación API)
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'alumno_id' => $this->alumno_id,
            'oferta_id' => $this->oferta_id,
            'fecha_solicitud' => $this->fecha_solicitud,
            'estado' => $this->estado,
            'alumno_nombre' => $this->alumno_nombre,
            'alumno_email' => $this->alumno_email,
            'oferta_titulo' => $this->oferta_titulo,
            'empresa_nombre' => $this->empresa_nombre,
        ];
    }
}
