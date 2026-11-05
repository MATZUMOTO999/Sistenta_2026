<<<<<<< HEAD
<?php
require __DIR__ . '/../../layouts/header.php';

$categoriaSeleccionada = (string) ($formData['categoria_id'] ?? '');
$categoriasDisponibles = $categorias ?? [];
$categoriaDisponible = false;

foreach ($categoriasDisponibles as $categoria) {
=======
<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php $categoriaSeleccionada = (string) ($formData['categoria_id'] ?? ''); ?>
<?php
$categoriaDisponible = false;
foreach ($categorias ?? [] as $categoria) {
>>>>>>> 577edcd5900f480f7b7177669e9aa4babf77dc5d
    if ((string) $categoria['id'] === $categoriaSeleccionada) {
        $categoriaDisponible = true;
        break;
    }
}
?>
<<<<<<< HEAD

<?php if (\Core\Auth::esAdmin()): ?>
    <div class="card p-3">
        <h3>Registrar Nuevo Producto</h3>

=======
<?php if (\Core\Auth::esAdmin()): ?>
    <div class="card p-3">
        <h3>Registrar Nuevo Producto</h3>
>>>>>>> 577edcd5900f480f7b7177669e9aa4babf77dc5d
        <form action="<?= BASE_URL ?>/admin/productos/guardar" method="POST">
            <div class="row">
                <div class="col-md-4 form-group">
                    <label for="categoria_id">Categoria</label>
                    <select class="form-control" name="categoria_id" id="categoria_id" required>
<<<<<<< HEAD
                        <option value="" disabled <?= $categoriaSeleccionada === '' ? 'selected' : '' ?>>
                            Seleccionar
                        </option>
                        <?php if ($categoriaSeleccionada !== '' && !$categoriaDisponible): ?>
                            <option value="<?= htmlspecialchars($categoriaSeleccionada, ENT_QUOTES, 'UTF-8') ?>" selected>
                                Categoria no disponible
                            </option>
                        <?php endif; ?>
                        <?php foreach ($categoriasDisponibles as $categoria): ?>
                            <option
                                value="<?= htmlspecialchars((string) $categoria['id'], ENT_QUOTES, 'UTF-8') ?>"
                                <?= (string) $categoria['id'] === $categoriaSeleccionada ? 'selected' : '' ?>>
                                <?= htmlspecialchars($categoria['nombre'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4 form-group">
                    <label for="codigo_sku">Codigo</label>
                    <input
                        type="text"
                        class="form-control"
                        name="codigo_sku"
                        id="codigo_sku"
                        maxlength="50"
                        required
                        value="<?= htmlspecialchars((string) ($formData['codigo_sku'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="col-md-4 form-group">
                    <label for="nombre">Nombre</label>
                    <input
                        type="text"
                        class="form-control"
                        name="nombre"
                        id="nombre"
                        maxlength="150"
                        required
                        value="<?= htmlspecialchars((string) ($formData['nombre'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="col-md-4 form-group">
                    <label for="descripcion">Descripcion</label>
                    <input
                        type="text"
                        class="form-control"
                        name="descripcion"
                        id="descripcion"
                        maxlength="1000"
                        required
                        value="<?= htmlspecialchars((string) ($formData['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="col-md-4 form-group">
                    <label for="precio_venta">Precio</label>
                    <input
                        type="number"
                        class="form-control"
                        name="precio_venta"
                        id="precio_venta"
                        min="0"
                        step="0.01"
                        required
                        value="<?= htmlspecialchars((string) ($formData['precio_venta'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="col-md-4 form-group">
                    <label for="stock_actual">Stock</label>
                    <input
                        type="number"
                        class="form-control"
                        name="stock_actual"
                        id="stock_actual"
                        min="0"
                        required
                        value="<?= htmlspecialchars((string) ($formData['stock_actual'] ?? '1'), ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>

=======
                        <option value="" disabled <?= $categoriaSeleccionada === '' ? 'selected' : '' ?>>Seleccionar</option>
                        <?php if ($categoriaSeleccionada !== '' && !$categoriaDisponible): ?>
                            <option value="<?= htmlspecialchars($categoriaSeleccionada, ENT_QUOTES, 'UTF-8') ?>" selected>Categoria no disponible</option>
                        <?php endif; ?>
                        <?php foreach ($categorias ?? [] as $categoria): ?>
                            <option value="<?= htmlspecialchars((string) $categoria['id'], ENT_QUOTES, 'UTF-8') ?>" <?= (string) $categoria['id'] === $categoriaSeleccionada ? 'selected' : '' ?>><?= htmlspecialchars($categoria['nombre'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 form-group">
                    <label for="codigo_sku">Codigo</label>
                    <input type="text" class="form-control" name="codigo_sku" required maxlength="50" id="codigo_sku" value="<?= htmlspecialchars((string) ($formData['codigo_sku'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="col-md-4 form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" class="form-control" name="nombre" required maxlength="150" id="nombre" value="<?= htmlspecialchars((string) ($formData['nombre'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="col-md-4 form-group">
                    <label for="descripcion">Descripcion</label>
                    <input type="text" class="form-control" name="descripcion" required maxlength="1000" id="descripcion" value="<?= htmlspecialchars((string) ($formData['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="col-md-4 form-group">
                    <label for="precio_venta">Precio</label>
                    <input type="number" class="form-control" name="precio_venta" required id="precio_venta" min="0" step="0.01" value="<?= htmlspecialchars((string) ($formData['precio_venta'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="col-md-4 form-group">
                    <label for="stock_actual">Stock</label>
                    <input type="number" class="form-control" name="stock_actual" required id="stock_actual" min="0" value="<?= htmlspecialchars((string) ($formData['stock_actual'] ?? '1'), ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>
>>>>>>> 577edcd5900f480f7b7177669e9aa4babf77dc5d
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

<<<<<<< HEAD
<?php require __DIR__ . '/../../layouts/footer.php'; ?>
=======
<?php require __DIR__ . '/../../layouts/footer.php'; ?>
>>>>>>> 577edcd5900f480f7b7177669e9aa4babf77dc5d
