<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php if (\Core\Auth::esAdmin()): ?>
    <div class="card p-3">
        <h4>Usuarios</h4>
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
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($usuarios)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No hay usuarios registrados.</td>
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
