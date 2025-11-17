<?php
$this->layout('base_simple', ['title' => 'Empresas registradas']) ?>

<?php
// === FUNCIÓN AUXILIAR CABECERA ORDENABLE ===
function thOrden($campo, $label, $ordenActual, $sentidoActual, $buscar, $pagina) {
    $sentidoSig = ($ordenActual === $campo && strtoupper($sentidoActual) === 'ASC') ? 'desc' : 'asc';
    $params = [
        'orden' => $campo,
        'sentido' => $sentidoSig
    ];
    if ($buscar) $params['buscar'] = $buscar;
    if ($pagina) $params['pagina'] = $pagina;
    $url = '?page=tabla_empresas&' . http_build_query($params);
    $flecha = '';
    if ($ordenActual === $campo) $flecha = strtoupper($sentidoActual) === 'ASC' ? ' ↑' : ' ↓';
    echo "<th><a href='$url'>$label$flecha</a></th>";
}
?>

<?php $this->start('contenido') ?>
<section class="tablaEmpresaContainer">
  <h1>Empresas registradas</h1>

  <div class="accionesEmpresa">
    <div class="busqueda">
      <form method="GET" action="" class="form-no-form">
        <input type="hidden" name="page" value="tabla_empresas">
        <input type="text" id="buscadorEmpresas" name="buscar" placeholder="Buscar Empresa..." value="<?= htmlspecialchars($buscar ?? '') ?>">
        <button id="filtrarEmpresa" type="submit">Filtrar</button>
        <?php if (!empty($buscar)): ?>
          <a href="?page=tabla_empresas" class="btn">Quitar filtro</a>
        <?php endif; ?>
      </form>
    </div>

    <div>
      <a href="?page=crear_empresa" class="btn btn-a" id="addEmpresa">Añadir</a>
      <a href="?page=exportar_empresa_pdf" class="btn" target="_blank">Descargar Empresas PDF</a>
    </div>
    
  </div>

  <table class="tablaEmpresas">
    <thead>
      <tr>
        <?php thOrden('id', 'ID',           $orden, $sentido, $buscar, $pagina); ?>
        <?php thOrden('nombre', 'Empresa',  $orden, $sentido, $buscar, $pagina); ?>
        <?php thOrden('cif', 'CIF',         $orden, $sentido, $buscar, $pagina); ?>
        <?php thOrden('pcontactoemail', 'Correo de contacto', $orden, $sentido, $buscar, $pagina); ?>
        <?php thOrden('tlfcontacto', 'Teléfono', $orden, $sentido, $buscar, $pagina); ?>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($empresas as $empresa): ?>
      <tr id="fila-<?= htmlspecialchars($empresa['id']) ?>">
        <td><?= htmlspecialchars($empresa['id']) ?></td>
        <td><?= htmlspecialchars($empresa['nombre']) ?></td>
        <td><?= htmlspecialchars($empresa['cif']) ?></td>
        <td><?= htmlspecialchars($empresa['pcontactoemail']) ?></td>
        <td><?= htmlspecialchars($empresa['tlfcontacto']) ?></td>
        <td>
          <a href="?page=detalles_empresa&id=<?= htmlspecialchars($empresa['id']) ?>" class="btn btn-detalles">Detalles</a>
          <a href="?page=editar_empresa&id=<?= htmlspecialchars($empresa['id']) ?>" class=" btn btn-modificar">Modificar</a>
          <form method="POST" action="?page=borrar_empresa" class="form-no-form">
            <input type="hidden" name="id" value="<?= htmlspecialchars($empresa['id']) ?>">
            <button type="submit" class="btn-tabla btn-borrar">Borrar</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php if (empty($empresas)): ?>
    <div style="margin-top:1em;">No hay empresas para mostrar.</div>
  <?php endif; ?>

  <!-- Paginación -->
  <div class="paginacion-tabla" style="margin-top:1em;display:flex;align-items:center;gap:12px;">
    <?php
      $params = ["orden" => $orden, "sentido" => strtolower($sentido), "buscar" => $buscar];
      $prevParams = $params; $prevParams["pagina"] = max(1, $pagina - 1);
      $nextParams = $params; $nextParams["pagina"] = min($totalPaginas, $pagina + 1);
    ?>
    <a href="?page=tabla_empresas&<?=http_build_query($prevParams) ?>"><button <?=($pagina <= 1 ? 'disabled' : '')?>>Anterior</button></a>
    <span>Página <?=$pagina?> de <?=$totalPaginas?></span>
    <a href="?page=tabla_empresas&<?=http_build_query($nextParams) ?>"><button <?=($pagina >= $totalPaginas ? 'disabled' : '')?>>Siguiente</button></a>
  </div>

  <?php if (!empty($no_validadas)): ?>
  <h2 style="margin-top:2em;">Empresas pendientes de validación</h2>
  <table class="tablaEmpresas">
    <thead>
      <tr>
        <th>ID</th>
        <th>Empresa</th>
        <th>CIF</th>
        <th>Correo de contacto</th>
        <th>Teléfono</th>
        <th>Acción</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($no_validadas as $empresa): ?>
        <tr>
          <td><?= htmlspecialchars($empresa['id']) ?></td>
          <td><?= htmlspecialchars($empresa['nombre']) ?></td>
          <td><?= htmlspecialchars($empresa['cif']) ?></td>
          <td><?= htmlspecialchars($empresa['pcontactoemail']) ?></td>
          <td><?= htmlspecialchars($empresa['tlfcontacto']) ?></td>
          <td>
            <form method="POST" action="?page=validar_empresa" class="form-no-form">
              <input type="hidden" name="id" value="<?= htmlspecialchars($empresa['id']) ?>">
              <button type="submit" class="btn-tabla btn-validar">Validar</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php else: ?>
  <div class="mensaje-empresas">No hay empresas pendientes de validación.</div>
<?php endif; ?>

</section>
<?php $this->stop() ?>
