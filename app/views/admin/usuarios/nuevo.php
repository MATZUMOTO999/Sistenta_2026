<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php if (\Core\Auth::esAdmin()): ?>
    <div class="card p-3">
        <h3>Registrar Nuevo Usuario</h3>
        <form action="<?= BASE_URL ?>/admin/usuarios/guardar" method="post">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" class="form-control" name="nombre" id="nombre" value="<?= htmlspecialchars((string) ($usuario['nombre'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="col-md-6 form-group">
                    <label for="apellidos">Apellidos:</label>
                    <input type="text" class="form-control" name="apellidos" id="apellidos" value="<?= htmlspecialchars((string) ($usuario['apellidos'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="col-md-6 form-group">
                    <label for="email">Correo electrónico:</label>
                    <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars((string) ($usuario['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="col-md-6 form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="text" class="form-control" name="telefono" id="telefono" value="<?= htmlspecialchars((string) ($usuario['telefono'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="col-md-6 form-group">
                    <label for="rol_id">Rol:</label>
                    <select class="form-control" name="rol_id" id="rol_id" required>
                        <option value="">Seleccionar</option>
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?= htmlspecialchars((string) $rol['id'], ENT_QUOTES, 'UTF-8') ?>" <?= (string) ($usuario['rol_id'] ?? '') === (string) $rol['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars((string) $rol['nombre'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label for="password">Contraseña inicial:</label>
                    <input type="password" class="form-control" name="password" id="password" minlength="8" autocomplete="new-password" required>
                    <small class="form-text text-muted">Debe tener al menos 8 caracteres.</small>
                </div>
            </div>
            <div>
                <a class="btn btn-danger" href="<?= BASE_URL ?>/admin/usuarios"><i class="fas fa-times"></i> Cancelar</a>
                <button class="btn btn-success" type="submit"><i class="fas fa-save"></i> Registrar</button>
            </div>
        </form>
    </div>
<?php else: ?>
    <div class="alert alert-danger" role="alert">
        No tienes permisos para acceder a esta página.
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../../layouts/footer.php'; ?>
