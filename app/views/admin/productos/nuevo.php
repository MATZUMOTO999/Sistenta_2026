<?php require __DIR__ . '/../../layouts/header.php'; ?>
<div class="card p-3">
    <h3>Registrar Nuevo Producto</h3>
    <form action="{{ route('admin.productos.store') }}" method="POST">
        <div class="row">
            <div class="col-md-4 form-group">
                <label for="categoria_id">Categoria</label>
                <select class="form-control" name="categoria_id" required maxlength="11">
                    <option value="">Seleccionar</option>
                    @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="codigo_sku">Codigo SKU</label>
                <input type="text" class="form-control" name="codigo_sku" required maxlength="50">
            </div>
            <div class="col-md-4 form-group">
                <label for="nombre">Nombre</label>
                <input type="text" class="form-control" name="nombre" required maxlength="150">
            </div>
            <div class="col-md-4 form-group">
                <label for="descripcion">Descripcion</label>
                <input type="text" class="form-control" name="descripcion" required maxlength="1000">
            </div>
            <div class="col-md-4 form-group">
                <label for="precio_venta">Precio Venta</label>
                <input type="number" class="form-control" name="precio_venta" required maxlength="10,2">
            </div>
            <div class="col-md-4 form-group">
                <label for="stock_actual">Stock Actual</label>
                <input type="number" class="form-control" name="stock_actual" required maxlength="11">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Registrar</button>
    </form>
</div>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>