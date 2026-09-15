<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php if(\Core\Auth::esAdmin()): ?>
    <div class="card p-2">
        <h4>Crear Nueva Categoría</h4>
        <form action="<?= BASE_URL ?>/admin/categorias/guardar" method="POST">
            
            <?php include __DIR__ . '/forms.php'; ?>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="<?= BASE_URL ?>/admin/categorias" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
<?php else: ?>
    <div class="alert alert-danger" role="alert">
        No tienes permisos para acceder a esta página.
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../../layouts/footer.php'; ?>