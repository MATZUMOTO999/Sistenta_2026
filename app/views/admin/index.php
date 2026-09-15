<?php require __DIR__ . '/../layouts/header.php'; ?>

<h3 class="mb-4">PANEL DE CONTROL</h3>

<div class="row">
  <?php
  if (\Core\Auth::esAdmin()): ?>
    <div class="col-md-3">
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title">Productos</h5>
          <p class="display-5"><?= htmlspecialchars($isAdmin) ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <a href="<?= BASE_URL ?>/tutoria/miTutoria">
        <div class="card text-center">
          <div class="card-body">
            <h5 class="card-title">Clientes</h5>
            <p class="display-5"><?= $isAdmin ?></p>
          </div>
        </div>
      </a>
    </div>
    <div class="col-md-3">
      <a href="<?= BASE_URL ?>/tutoria/miTutoria">
        <div class="card text-center">
          <div class="card-body">
            <h5 class="card-title">Ventas</h5>
            <p class="display-5"><?= $isAdmin ?></p>
          </div>
        </div>
      </a>
    </div>
  <?php endif; ?>
  <?php if (\Core\Auth::esCliente()): ?>
    <div class="col-md-3">
      <a href="<?= BASE_URL ?>/academico/reportes">
        <div class="card text-center">
          <div class="card-body">
            <h5 class="card-title">reportes</h5>
            <p class="display-5"><?= htmlspecialchars($periodo) ?></p>
          </div>
        </div>
      </a>
    </div>
  <?php endif; ?>


</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>