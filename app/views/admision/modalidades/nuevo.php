<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php if (\Core\Auth::esAdminAdmision()): ?>
    <div class="card p-2">
        <h4>Nueva Modalidad</h4>
        <form action="<?= BASE_URL ?>/admision/modalidades/guardar" method="post" class="card p-4 shadow-sm rounded-3" autocomplete="off">
            <?php include __DIR__ . '/form_fields.php'; ?>
            <div class="mt-3 text-end">
                <button type="submit" class="btn btn-success px-4">Guardar</button>
                <a href="<?= BASE_URL ?>/admision/modalidades" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
<?php else: ?>
    <p>Usted no cuenta con permisos para acceder a esta sección</p>
<?php endif; ?>
<?php require __DIR__ . '/../../layouts/footer.php'; ?>