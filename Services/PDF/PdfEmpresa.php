<?php
use Dompdf\Dompdf;

/**
 * PdfEmpresa
 *
 * Servicio estático para exportar listados de empresas en PDF usando Dompdf.
 */
class PdfEmpresa
{
    /**
     * Genera y envía al navegador un PDF con el listado pasado como parámetro.
     * También guarda el HTML generado en disco para poder depurarlo visualmente.
     *
     * @param array $empresas Array de arrays asociativos con los datos de cada empresa.
     * Cada elemento debe contener al menos: id, nombre, cif, pcontactoemail, tlfcontacto.
     * Puedes adaptar/añadir columnas según tu modelo.
     */
    public static function exportEmpresas($empresas)
    {
        $dompdf = new Dompdf();
        $html = self::getHtmlEmpresas($empresas);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();

        // Guarda una copia del HTML generado, útil para debugear estilos o contenido
        file_put_contents(__DIR__.'/debug_dompdf.html', $html);

        // Envía el PDF al navegador, sin forzar descarga
        $dompdf->stream("empresas.pdf", ["Attachment" => false]);
    }

    /**
     * Devuelve el HTML para renderizar el listado de empresas.
     *
     * @param array $empresas Array de arrays asociativos (id, nombre, cif, etc.)
     * @return string HTML para ser convertido en PDF
     */
    private static function getHtmlEmpresas($empresas)
    {
        $html = '<html>
<head>
  <style>
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #000000ff; padding: 8px; }
    th { background-color: #bdbabaff; }
  </style>
</head>
<body>
  <h2>Listado de Empresas</h2>
  <table>
    <thead><tr>
    <th>ID</th>
    <th>Nombre</th>
    <th>CIF</th>
    <th>Email contacto</th>
    <th>Teléfono</th>
    </tr></thead>
    <tbody>';
        foreach ($empresas as $empresa) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($empresa['id']) . '</td>';
            $html .= '<td>' . htmlspecialchars($empresa['nombre']) . '</td>';
            $html .= '<td>' . htmlspecialchars($empresa['cif']) . '</td>';
            $html .= '<td>' . htmlspecialchars($empresa['pcontactoemail']) . '</td>';
            $html .= '<td>' . htmlspecialchars($empresa['tlfcontacto']) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>
</body>
</html>';
        return $html;
    }
}
