<?php require __DIR__ . '/../../layouts/header.php'; ?>
<div class="card p-3">
    <h3>Registrar Nuevo Producto</h3>
    <form action="{{ route('admin.productos.store') }}" method="POST">
        <div class="row">
            <div class="col-md-4 form-group">
                <label for="categoria_id">Categoria</label>
                <select class="form-control" name="categoria_id" required maxlength="11">
                    <option value="">Seleccionar</option>
                    <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= htmlspecialchars((string) $categoria['id'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($categoria['nombre'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="codigo_sku">Codigo SKU</label>
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
                <label for="precio_venta">Precio Venta</label>
                <input type="number" class="form-control" name="precio_venta" required maxlength="10,2" id="precio_venta" min="0" step="0.01" required>
            </div>
            <div class="col-md-4 form-group">
                <label for="stock_actual">Stock Actual</label>
                <input type="number" class="form-control" name="stock_actual" required maxlength="11" id="stock_actual" min="0" required values="1">
            </div>
        </div>
        <button type="submit" class="btn btn-primary" class="fa fa-save">Registrar</button>
        <button type="reset" class="btn btn-secondary" class="fa fa-trash">Limpiar</button>
        <button type="button" class="btn btn-danger" class="fa fa-times">Cancelar</button>
    </form>
</div>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>