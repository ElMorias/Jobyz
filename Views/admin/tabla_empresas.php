<?php $this->layout('base_simple', ['title' => 'Empresas registradas']) ?>

<?php $this->start('contenido') ?>
<section class="tablaEmpresaContainer">
  <h1>Empresas registradas</h1>

  <div class="accionesEmpresa">
    <div class="busqueda">
      <form method="GET" action="">
        <input type="text" id="buscadorEmpresas" name="buscar" placeholder="Buscar Empresa..." value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>">
        <button id="filtrarEmpresa" type="submit">Filtrar</button>
        <?php if (!empty($_GET['buscar'])): ?>
          <a href="tabla_empresas.php">Quitar filtro</a>
        <?php endif; ?>
      </form>
    </div>
    <a href="?page=crear_empresa" class="btn" id="addEmpresa">Añadir</a>
  </div>

  <table class="tablaEmpresas">
    <thead>
      <tr>
        <th>ID</th>
        <th>Empresa</th>
        <th>CIF</th>
        <th>Correo de contacto</th>
        <th>Teléfono</th>
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
          <a href="?page=detalles_empresa&id=<?= htmlspecialchars($empresa['id']) ?>" class="btn-tabla btn-detalles">Detalles</a>
          <a href="?page=editar_empresa&id=<?= htmlspecialchars($empresa['id']) ?>" class="btn-tabla btn-modificar">Modificar</a>
          <form method="POST" action="?page=borrar_empresa" class="form-borrar-empre">
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
            <form method="POST" action="?page=validar_empresa" class="form-borrar-empre">
              <input type="hidden" name="id" value="<?= htmlspecialchars($empresa['id']) ?>">
              <button type="submit" class="btn-tabla btn-validar">Validar</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php else: ?>
  <div style="margin-top:1em;">No hay empresas pendientes de validación.</div>
<?php endif; ?>

</section>
<?php $this->stop() ?>