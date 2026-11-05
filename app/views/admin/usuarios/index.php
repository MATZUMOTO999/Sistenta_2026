<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php if (\Core\Auth::esAdmin()): ?>
    <div class="card p-3">
        <h4>Usuarios</h4>
        <div class="col-md-2 mb-2">
            <a href="<?= BASE_URL ?>/admin/usuarios/nuevo" class="btn btn-success">+ Nuevo Usuario</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nro</th>
                        <th>Nombre</th>
                        <th>Correo electrónico</th>
                        <th>Teléfono</th>
                        <th>Rol</th>
                        <th>Fecha de registro</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($usuarios)): ?>
                        <tr>
                            <td colspan="8" class="text-center">No hay usuarios registrados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($usuarios as $indice => $usuario): ?>
                            <tr>
                                <td><?= $indice + 1 ?></td>
                                <td><?= htmlspecialchars(trim($usuario['nombre'] . ' ' . $usuario['apellidos']), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($usuario['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($usuario['telefono'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($usuario['rol'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($usuario['fecha_registro'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <?= !empty($usuario['activo']) ? 'Activo' : 'Inactivo' ?>
                                </td>
                                <td>
                                    <div class="d-inline-flex">
                                        <a class="btn btn-outline-primary mr-1" title="Editar usuario" aria-label="Editar usuario" data-no-loader href="<?= BASE_URL ?>/admin/usuarios/editar/<?= (int) $usuario['id'] ?>">
                                            <i class="fa fa-edit" aria-hidden="true"></i>
                                        </a>
                                        <?php if (!empty($usuario['activo'])): ?>
                                            <form action="<?= BASE_URL ?>/admin/usuarios/eliminar" method="post" class="d-inline" data-no-loader onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario? El usuario será desactivado y sus sesiones cerradas.');">
                                                <input type="hidden" name="id" value="<?= (int) $usuario['id'] ?>">
                                                <button class="btn btn-outline-danger" type="submit" title="Eliminar usuario" aria-label="Eliminar usuario">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-danger" role="alert">
        No tienes permisos para acceder a esta página.
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../../layouts/footer.php'; ?>
