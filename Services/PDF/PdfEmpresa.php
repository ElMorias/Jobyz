<?php
use Dompdf\Dompdf;

class PdfEmpresa
{
    public static function exportEmpresas($empresas)
    {
        $dompdf = new Dompdf();
        $html = self::getHtmlEmpresas($empresas);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();
        file_put_contents(__DIR__.'/debug_dompdf.html', $html);
        $dompdf->stream("empresas.pdf", ["Attachment" => false]);
    }

    // Devuelve el html del pdf como string
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
