<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php if (\Core\Auth::esAdmin()): ?>
    <div class="card p-3">
        <h3>Registrar Nuevo Producto</h3>
        <form action="<?= BASE_URL ?>/admin/productos/guardar" method="POST">
            <div class="row">
                <div class="col-md-4 form-group">
                    <label for="categoria_id">Categoria</label>
                    <select class="form-control" name="categoria_id" id="categoria_id" required>
                        <option value="" selected disabled>Seleccionar</option>
                        <?php foreach ($categorias ?? [] as $categoria): ?>
                            <option value="<?= htmlspecialchars((string) $categoria['id'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($categoria['nombre'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 form-group">
                    <label for="codigo_sku">Codigo</label>
                    <input type="text" class="form-control" name="codigo_sku" required maxlength="50" id="codigo_sku">
                </div>
                <div class="col-md-4 form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" class="form-control" name="nombre" required maxlength="150" id="nombre">
                </div>
                <div class="col-md-4 form-group">
                    <label for="descripcion">Descripcion</label>
                    <input type="text" class="form-control" name="descripcion" required maxlength="1000" id="descripcion">
                </div>
                <div class="col-md-4 form-group">
                    <label for="precio_venta">Precio</label>
                    <input type="number" class="form-control" name="precio_venta" required id="precio_venta" min="0" step="0.01">
                </div>
                <div class="col-md-4 form-group">
                    <label for="stock_actual">Stock</label>
                    <input type="number" class="form-control" name="stock_actual" required id="stock_actual" min="0" value="1">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Registrar</button>
            <button type="reset" class="btn btn-secondary">Limpiar</button>
            <a href="<?= BASE_URL ?>/admin/productos" class="btn btn-danger">Cancelar</a>
        </form>
    </div>
<?php else: ?>
    <div class="alert alert-danger" role="alert">
        No tienes permisos para acceder a esta página.
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>