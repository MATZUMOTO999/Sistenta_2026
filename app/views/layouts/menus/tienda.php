<nav class="navbar navbar-light navbar-expand-lg topnav-menu">
    <div class="collapse navbar-collapse" id="topnav-menu-content">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>/tienda/categorias">
                    <i class="mdi mdi-view-dashboard"></i> Categorías
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($_SERVER['REQUEST_URI'] === '/admin' ? 'active' : '') ?>"
                    href="<?= BASE_URL ?>/tienda/ofertas">
                    <i class="mdi mdi-home-analytics"></i> Ofertas
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
    <div class="d-flex align-items-center">
        <div class="dropdown d-inline-block ml-2">
            <button type="button" class="btn header-item waves-effect waves-light"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img class="rounded-circle header-profile-user"
                    src="<?= BASE_URL ?>/img/user.png"
                    alt="Header Avatar">
                <span class="d-none d-sm-inline-block ml-1"><?= $_SESSION['sigi_user_name'] ?? 'Usuario' ?></span>
                <i class="mdi mdi-chevron-down d-none d-sm-inline-block"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item d-flex align-items-center justify-content-between"
                    href="<?= BASE_URL ?>/perfil">
                    <span>Mi perfil</span>
                </a>
                <a class="dropdown-item d-flex align-items-center justify-content-between"
                    href="<?= BASE_URL ?>/resetPassword?data=<?= base64_encode($_SESSION['user_id']) ?>&back=<?= urlencode($_SERVER['REQUEST_URI']) ?>">
                    <span>Cambiar contraseña</span>
                </a>
                <a class="dropdown-item d-flex align-items-center justify-content-between text-danger"
                    href="<?= BASE_URL ?>/logout">
                    <span>Cerrar sesión</span>
                </a>
            </div>
        </div>
    </div>
</nav>