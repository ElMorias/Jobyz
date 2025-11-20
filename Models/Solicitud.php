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

    // Extras para enriquecimiento (pueden ser null, no forman parte de la tabla base directamente)
    public ?string $alumno_nombre = null;
    public ?string $alumno_email = null;
    public ?string $curriculum = null;
    public ?string $oferta_titulo = null;
    public ?string $empresa_nombre = null;

    /**
     * Constructor con solo los atributos base (los que estén en la tabla solicitud)
     */
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
     * Puedes rellenar aquí solo los base. Los extras se pueden asignar a posteriori.
     */
    public static function fromArray(array $row): Solicitud {
        $instancia = new Solicitud(
            (int)$row['id'],
            (int)$row['alumno_id'],
            (int)$row['oferta_id'],
            $row['fecha_solicitud'],
            $row['estado']
        );
        // Extras de enriquecimiento si existen en el array (por join)
        $instancia->alumno_nombre = $row['alumno_nombre'] ?? null;
        $instancia->alumno_email = $row['alumno_email'] ?? null;
        $instancia->curriculum = $row['curriculum'] ?? null;
        $instancia->oferta_titulo = $row['oferta_titulo'] ?? null;
        $instancia->empresa_nombre = $row['empresa_nombre'] ?? null;
        return $instancia;
    }

    /**
     * Conversión a array asociativo (exportación API, vistas, etc)
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'alumno_id' => $this->alumno_id,
            'oferta_id' => $this->oferta_id,
            'fecha_solicitud' => $this->fecha_solicitud,
            'estado' => $this->estado,
            // Extras, pueden venir null
            'alumno_nombre' => $this->alumno_nombre,
            'alumno_email' => $this->alumno_email,
            'alumno_curriculum' => $this->curriculum,
            'oferta_titulo' => $this->oferta_titulo,
            'empresa_nombre' => $this->empresa_nombre,
        ];
    }
}
