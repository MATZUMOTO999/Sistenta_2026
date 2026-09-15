<nav class="navbar navbar-light navbar-expand-lg topnav-menu">
    <div class="collapse navbar-collapse" id="topnav-menu-content">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>/intranet">
                    <i class="mdi mdi-view-dashboard"></i> Panel
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($_SERVER['REQUEST_URI'] === '/admision' ? 'active' : '') ?>"
                    href="<?= BASE_URL ?>/admision">
                    <i class="mdi mdi-home-analytics"></i> Inicio
                </a>
            </li>
            <!-- Menú normal solo si es administrador -->
            <?php if (\Core\Auth::tieneRolEnAdmision()): ?>
                <?php if (\Core\Auth::esAdminAdmision()): ?>
                    <li class="nav-item">
                        <a class="nav-link"
                            href="<?= BASE_URL ?>/admision/inscripciones">
                            <i class="far fa-address-book"></i> Inscripciones
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="nav-periodos" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-cog"></i> Gestión de Admisión<div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="nav-periodos">
                            <a href="<?= BASE_URL ?>/admision/procesosAdmision" class="dropdown-item">Procesos de Admisión</a>
                            <a href="<?= BASE_URL ?>/admision/procesosModalidades" class="dropdown-item">Procesos de Modalidades</a>
                            <a href="<?= BASE_URL ?>/admision/vacantes" class="dropdown-item">Vacantes</a>
                            <a href="<?= BASE_URL ?>/admision/ambientes" class="dropdown-item">Ambientes</a>
                            <a href="<?= BASE_URL ?>/admision/distribucion" class="dropdown-item">Distribución de aulas</a>
                            <a href="<?= BASE_URL ?>/admision/calificaciones" class="dropdown-item">Subir Resultados</a>
                            <a href="<?= BASE_URL ?>/admision/comision" class="dropdown-item">Comisión de Admisión</a>
                            <a href="<?= BASE_URL ?>/admision/modalidades" class="dropdown-item">Modalidades</a>
                            <a href="<?= BASE_URL ?>/admision/tipos-modalidad" class="dropdown-item">Tipo de Modalidad</a>
                        </div>
                    </li>
                <?php endif; ?>
            <?php else: ?>
                <!-- Aquí va solo lo mínimo, o un mensaje, o nada -->
                <!-- O simplemente no muestres nada más -->
            <?php endif; ?>
        </ul>
    </div>
</nav>