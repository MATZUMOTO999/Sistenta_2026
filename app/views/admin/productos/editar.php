<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php
$categoriaSeleccionada = (string) ($producto['categoria_id'] ?? '');
$categoriaDisponible = false;
foreach ($categorias as $categoria) {
    if ((string) $categoria['id'] === $categoriaSeleccionada) {
        $categoriaDisponible = true;
        break;
    }
}
?>
<div class="card p-3">
    <h3>Editar Producto</h3>
    <form action="<?= BASE_URL ?>/admin/productos/actualizar" method="post">
        <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($producto['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        <div class="row">
            <div class="col-md-4 form-group">
                <label for="categoria_id">Categoría :</label>
                <select name="categoria_id" id="categoria_id" class="form-control" required>
                    <option value="" disabled <?= $categoriaSeleccionada === '' ? 'selected' : '' ?>>Seleccionar</option>
                    <?php if ($categoriaSeleccionada !== '' && !$categoriaDisponible): ?>
                        <option value="<?= htmlspecialchars($categoriaSeleccionada, ENT_QUOTES, 'UTF-8') ?>" selected>Categoría no disponible</option>
                    <?php endif; ?>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= htmlspecialchars((string) $categoria['id'], ENT_QUOTES, 'UTF-8') ?>" <?= (string) $categoria['id'] === $categoriaSeleccionada ? 'selected' : '' ?>><?= htmlspecialchars($categoria['nombre'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="codigo_sku">Código :</label>
                <input type="text" class="form-control" name="codigo_sku" id="codigo_sku" maxlength="50" value="<?= htmlspecialchars((string) ($producto['codigo_sku'] ?? '')) ?>" required>
            </div>
            <div class="col-md-5 form-group">
                <label for="nombre">Nombre :</label>
                <input type="text" class="form-control" name="nombre" id="nombre" maxlength="150" value="<?= htmlspecialchars((string) ($producto['nombre'] ?? '')) ?>" required>
            </div>
            <div class="col-md-8 form-group">
                <label for="descripcion">Descripción :</label>
                <textarea name="descripcion" id="descripcion" required class="form-control" maxlength="500" rows="3"><?= htmlspecialchars((string) ($producto['descripcion'] ?? '')) ?></textarea>
            </div>
            <div class="col-md-2 form-group">
                <label for="precio_venta">Precio :</label>
                <input type="number" class="form-control" name="precio_venta" id="precio_venta" min="0" step="0.01" value="<?= htmlspecialchars((string) ($producto['precio_venta'] ?? '')) ?>" required>
            </div>
            <div class="col-md-2 form-group">
                <label for="stock_actual">Stock :</label>
                <input type="number" class="form-control" name="stock_actual" id="stock_actual" min="0" value="<?= htmlspecialchars((string) ($producto['stock_actual'] ?? '')) ?>" required>
            </div>
            <div class="col-md-2 form-group">
                <label for="activo">Estado :</label>
                <select name="activo" id="activo" class="form-control">
                    <option value="1" <?= (string) ($producto['activo'] ?? '') === '1' ? 'selected' : '' ?>>Activo</option>
                    <option value="0" <?= (string) ($producto['activo'] ?? '') === '0' ? 'selected' : '' ?>>Inactivo</option>
                </select>
            </div>
        </div>
        <div>
            <a class="btn btn-danger" href="<?= BASE_URL ?>/admin/productos"><i class="fas fa-times"></i> Cancelar</a>
            <button class="btn btn-info" type="reset"><i class="fas fa-trash"></i> Limpiar</button>
            <button class="btn btn-success" type="submit"><i class="fas fa-save"></i> Actualizar</button>
        </div>
    </form>
</div>
<?php require __DIR__ . '/../../layouts/footer.php'; ?>
