<?php $this->layout('base_simple', ['title' => 'Solicitudes de la oferta']) ?>

<?php $this->start('contenido') ?>
    <div class="solicitudes-oferta-div">
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
                        <th>Curriculum</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($solicitudes as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s->alumno_nombre) ?></td>
                        <td><?= htmlspecialchars($s->alumno_email) ?></td>
                        <td><?= htmlspecialchars($s->fecha_solicitud) ?></td>
                        <td>
                        <?php if (!empty($s->curriculum)): ?>
                            <a href="<?= htmlspecialchars($s->curriculum) ?>" target="_blank" rel="noopener">
                                Ver Curriculum
                            </a>
                            <?php else: ?>
                                No disponible
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($s->estado) ?></td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        <?php endif ?>

        <a href="index.php?page=ofertas" class="btn">Volver a ofertas</a>
    </div>
<?php $this->stop() ?>
