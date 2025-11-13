<?php $this->layout('base_simple', ['title' => 'Modificar Oferta']) ?>

<?php $this->start('contenido') ?>
<h2>Modificar oferta</h2>

<?php if (!empty($errores)): ?>
    <div class="errores-form">
        <?php foreach ($errores as $err): ?>
            <div class="error"><?= htmlspecialchars($err) ?></div>
        <?php endforeach ?>
    </div>
<?php endif; ?>

<form action="index.php?page=modificar_oferta&id=<?= $oferta->id ?>" method="post" class="form-oferta">
    <label>
        Título<br>
        <input type="text" name="titulo" required value="<?= htmlspecialchars($_POST['titulo'] ?? $oferta->titulo) ?>">
    </label>
    <br>
    <label>
        Descripción<br>
        <textarea name="descripcion" rows="4" required><?= htmlspecialchars($_POST['descripcion'] ?? $oferta->descripcion) ?></textarea>
    </label>
    <br>
    <label>
        Fecha límite<br>
        <input type="date" name="fechalimite" required value="<?= htmlspecialchars($_POST['fechalimite'] ?? $oferta->fechalimite) ?>">
    </label>
    <br>
    <button type="submit" class="btn-principal">Guardar cambios</button>
    <a href="index.php?page=ofertas" class="btn-secundario">Volver</a>
</form>
<?php $this->stop() ?>
