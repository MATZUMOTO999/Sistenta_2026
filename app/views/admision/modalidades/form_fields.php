<div class="mb-3">
    <label class="form-label">Tipo *</label>
    <select name="id_tipo_modalidad" class="form-control" required>
        <option value="">Seleccione...</option>
        <?php foreach ($tiposModalidad as $tipo): ?>
            <option value="<?= $tipo['id'] ?>"
                <?= (!empty($modalidad['id_tipo_modalidad']) && $modalidad['id_tipo_modalidad'] == $tipo['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($tipo['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
<div class="mb-3">
    <label class="form-label">Nombre *</label>
    <input type="text" name="nombre" class="form-control" maxlength="100" required value="<?= htmlspecialchars($modalidad['nombre'] ?? '') ?>">
</div>