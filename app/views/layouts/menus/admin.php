<nav class="navbar navbar-light navbar-expand-lg topnav-menu">
    <div class="collapse navbar-collapse" id="topnav-menu-content">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>/tienda">
                    <i class="mdi mdi-view-dashboard"></i> Vista de Tienda
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($_SERVER['REQUEST_URI'] === '/admin' ? 'active' : '') ?>"
                    href="<?= BASE_URL ?>/admin">
                    <i class="mdi mdi-home-analytics"></i> Inicio
                </a>
            </li>
            <!-- Menú normal solo si es administrador -->
            <?php if (\Core\Auth::esAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link"
                        href="<?= BASE_URL ?>/admin/ventas">
                        <i class="far fa-address-book"></i> Ventas
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle arrow-none" href="#" id="nav-periodos" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-cog"></i> Gestión de Admisión<div class="arrow-down"></div>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="nav-periodos">
                        <a href="<?= BASE_URL ?>/admin/usuarios" class="dropdown-item">Usuarios</a>
                        <a href="<?= BASE_URL ?>/admin/categorias" class="dropdown-item">Categorías</a>
                        <a href="<?= BASE_URL ?>/admin/productos" class="dropdown-item">Productos</a>
                    </div>
                </li>
            <?php endif; ?>
            <!-- Aquí va solo lo mínimo, o un mensaje, o nada -->
            <!-- O simplemente no muestres nada más -->
        </ul>
    </div>
</nav>