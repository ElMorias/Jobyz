<?php $this->layout('base_simple', ['title' => 'Solicitudes de la oferta']) ?>

<?php $this->start('contenido') ?>
<h2>Solicitudes de la oferta</h2>

<?php if (empty($solicitudes)): ?>
    <p>No hay solicitudes todavía.</p>
<?php else: ?>
    <table class="tabla-solicitudes">
        <thead>
            <tr>
                <th>Alumno</th>
                <th>Email</th>
                <th>Fecha solicitud</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($solicitudes as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s->alumno_nombre) ?></td>
                <td><?= htmlspecialchars($s->alumno_email) ?></td>
                <td><?= htmlspecialchars($s->fecha_solicitud) ?></td>
                <td><?= htmlspecialchars($s->estado) ?></td>
                <td>
                    <form action="index.php?page=gestionar_solicitud" method="POST" style="display:inline;">
                        <input type="hidden" name="solicitud_id" value="<?= $s->id ?>">
                        <button type="submit" name="accion" value="aceptar" class="btn-card btn-aceptar">Aceptar</button>
                        <button type="submit" name="accion" value="rechazar" class="btn-card btn-rechazar">Rechazar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
<?php endif ?>

<a href="index.php?page=ofertas" class="btn-secundario">Volver a ofertas</a>
<?php $this->stop() ?>
