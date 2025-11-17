<?php
use Dompdf\Dompdf;

class PdfAlumnos
{
    public static function exportAlumnos($alumnos)
    {
        $dompdf = new Dompdf();
        $html = self::getHtmlAlumnos($alumnos);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();
        $dompdf->stream("alumnos.pdf", ["Attachment" => false]);
    }

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
