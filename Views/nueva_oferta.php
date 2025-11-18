<?php $this->layout('base_simple', ['title' => 'Nueva Oferta']) ?>

<?php $this->start('contenido') ?>
<h2>Crear nueva oferta</h2>

<?php if (!empty($errores)): ?>
    <div class="errores-form">
        <?php foreach ($errores as $err): ?>
            <div class="error"><?= htmlspecialchars($err) ?></div>
        <?php endforeach ?>
    </div>
<?php endif; ?>

<form action="index.php?page=nueva_oferta" method="post" class="form-oferta">
    <label>
        Título<br>
        <input type="text" name="titulo" required value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>">
    </label>
    <br>
    <label>
        Descripción<br>
        <textarea name="descripcion" rows="4" required><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
    </label>
    <br>
    <label for="ciclo1">Ciclo requerido 1:</label>
    <select name="ciclos[]" id="ciclo1" required>
        <option value="">-- Seleccione ciclo --</option>
        <?php foreach ($ciclos as $ciclo): ?>
            <option value="<?= $ciclo['id'] ?>"><?= htmlspecialchars($ciclo['nombre']) ?></option>
        <?php endforeach ?>
    </select>

    <label for="ciclo2">Ciclo requerido 2 (opcional):</label>
    <select name="ciclos[]" id="ciclo2">
        <option value="">-- Seleccione ciclo --</option>
        <?php foreach ($ciclos as $ciclo): ?>
            <option value="<?= $ciclo['id'] ?>"><?= htmlspecialchars($ciclo['nombre']) ?></option>
        <?php endforeach ?>
    </select>
    <label>
        Fecha límite<br>
        <input type="date" name="fechalimite" required value="<?= htmlspecialchars($_POST['fechalimite'] ?? '') ?>">
    </label>
    <br>
    <button type="submit" class="btn-principal">Crear oferta</button>
    <a href="index.php?page=ofertas" class="btn-secundario btn">Volver</a>
</form>
<?php $this->stop() ?>