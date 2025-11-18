<?php
use Dompdf\Dompdf;

/**
 * PdfAlumnos
 *
 * Servicio estático para exportar listados de alumnos a PDF usando Dompdf.
 */
class PdfAlumnos
{
    /**
     * Genera y envía al navegador un PDF con el listado de alumnos.
     *
     * @param array $alumnos Array de arrays asociativos con los datos de cada alumno.
     * Cada elemento debe contener al menos: id, nombre, correo, telefono.
     * Puedes adaptar/añadir columnas según las necesidades del modelo.
     */
    public static function exportAlumnos($alumnos)
    {
        $dompdf = new Dompdf();
        $html = self::getHtmlAlumnos($alumnos);

        // Carga el HTML, define tamaño DIN-A4 y renderiza el PDF
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();

        // Envía el PDF al navegador (sin forzar descarga)
        $dompdf->stream("alumnos.pdf", ["Attachment" => false]);
    }

    /**
     * Monta el HTML para el PDF de alumnos.
     *
     * @param array $alumnos Lista de arrays con los datos de los alumnos.
     * @return string HTML para ser convertido en PDF
     */
    private static function getHtmlAlumnos($alumnos)
    {
        $html = '<html>
<head>
  <meta charset="UTF-8">
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000000ff; padding: 8px; }
        th { background-color: #bdbabaff; }
    </style>
</head>
<body>
  <h2>Listado de Alumnos</h2>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Email</th>
        <th>Teléfono</th>
        <!-- Añade más columnas según tu modelo -->
      </tr>
    </thead>
    <tbody>';
        foreach ($alumnos as $alumno) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($alumno['id'] ?? '') . '</td>';
            $html .= '<td>' . htmlspecialchars($alumno['nombre'] ?? '') . '</td>';
            $html .= '<td>' . htmlspecialchars($alumno['correo'] ?? '') . '</td>';
            $html .= '<td>' . htmlspecialchars($alumno['telefono'] ?? '') . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>
</body>
</html>';
        return $html;
    }
}
