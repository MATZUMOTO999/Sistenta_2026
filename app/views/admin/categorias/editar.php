<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php if (\Core\Auth::esAdmin()): ?>
    <div class="card p-3">
        <h3>Editar Categoría</h3>
        <form action="<?= BASE_URL ?>/admin/categorias/actualizar" method="post">
            <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($categoria['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" class="form-control" name="nombre" id="nombre" value="<?= htmlspecialchars((string) ($categoria['nombre'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="col-md-6 form-group">
                    <label for="activo">Estado:</label>
                    <select class="form-control" name="activo" id="activo" required>
                        <option value="1" <?= (string) ($categoria['activo'] ?? '') === '1' ? 'selected' : '' ?>>Activo</option>
                        <option value="0" <?= (string) ($categoria['activo'] ?? '') === '0' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
                <div class="col-12 form-group">
                    <label for="descripcion">Descripción:</label>
                    <textarea class="form-control" name="descripcion" id="descripcion" rows="3"><?= htmlspecialchars((string) ($categoria['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
            </div>
            <div>
                <a class="btn btn-danger" href="<?= BASE_URL ?>/admin/categorias"><i class="fas fa-times"></i> Cancelar</a>
                <button class="btn btn-success" type="submit"><i class="fas fa-save"></i> Actualizar</button>
            </div>
        </form>
    </div>
<?php else: ?>
    <div class="alert alert-danger" role="alert">
        No tienes permisos para acceder a esta página.
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../../layouts/footer.php'; ?>
