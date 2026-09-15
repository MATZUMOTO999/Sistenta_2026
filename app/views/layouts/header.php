<?php

use Core\Auth;

Auth::start();
$logueado = Auth::user() !== null;

if ($logueado):
  $db = (new \Core\Model())->getDB();
  $userLogin = $_SESSION['user_id'] ?? null;
  // Definir el id de admin en una variable por claridad
  $rolAdmin = 1;
  $rolActual = $_SESSION['rol_actual'] ?? null;
endif;
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <title><?= $pageTitle ?? 'VENTAS' ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="<?= BASE_URL ?>/assets/css/bootstrap.min.css" rel="stylesheet" />
  <link href="<?= BASE_URL ?>/assets/css/sweetalert2.min.css" rel="stylesheet" />
  <link href="<?= BASE_URL ?>/assets/css/icons.min.css" rel="stylesheet" />
  <link href="<?= BASE_URL ?>/assets/css/theme.min.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">

  <!-- Si usas Responsive de DataTables, descomenta estas dos líneas -->
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">


  <?php
  if (isset($_SESSION['favicon']) && $_SESSION['favicon'] != '') {
    $ruta_favicon = BASE_URL . '/images/' . $_SESSION['favicon'];
  } else {
    $ruta_favicon = BASE_URL . '/img/favicon.ico';
  }
  ?>
  <link rel="icon" type="image/x-icon" href="<?= $ruta_favicon; ?>">
</head>

<body data-sidebar="light">
  <div id="layout-wrapper">
    <!-- SIEMPRE muestra el header y logo -->
    <?php if ($logueado && isset($isAdmin) && $isAdmin === true): ?>
      <header id="page-topbar">
        <div class="navbar-header">
          <div class="navbar-brand-box d-flex align-items-left">
            <a href="<?= BASE_URL . '/' . $_SESSION['modulo_vista']; ?>" class="logo">
              <?php
              if ($_SESSION['logo'] != '') {
                $ruta_logo = BASE_URL . '/images/' . $_SESSION['logo'];
              } else {
                $ruta_logo = BASE_URL . '/img/logo_completo.png';
              }
              ?>
              <i class="mdi"><img src="<?= $ruta_logo ?>" alt="" width="100px" height="30px"></i>
              <span>VENTAS</span>
            </a>
            <button type="button" class="btn btn-sm mr-2 font-size-16 d-lg-none header-item waves-effect waves-light"
              data-toggle="collapse" data-target="#topnav-menu-content">
              <i class="fa fa-fw fa-bars"></i>
            </button>
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
                  href="<?= BASE_URL ?>/resetPassword?data=<?= base64_encode($_SESSION['sigi_user_id']) ?>&back=<?= urlencode($_SERVER['REQUEST_URI']) ?>">
                  <span>Cambiar contraseña</span>
                </a>
                <a class="dropdown-item d-flex align-items-center justify-content-between text-danger"
                  href="<?= BASE_URL ?>/logout">
                  <span>Cerrar sesión</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </header>
    <?php
    elseif (isset($isTienda) && $isTienda === true):
    ?>
      <!-- Header para la tienda -->
      <header id="page-topbar">
        <div class="navbar-header">
          <div class="navbar-brand-box d-flex align-items-left">
            <a href="<?= BASE_URL . '/tienda'; ?>" class="logo">
              <?php
              if (isset($_SESSION['logo']) && $_SESSION['logo'] != '') {
                $ruta_logo = BASE_URL . '/images/' . $_SESSION['logo'];
              } else {
                $ruta_logo = BASE_URL . '/img/logo_completo.png';
              }
              ?>
              <i class="mdi"><img src="<?= $ruta_logo ?>" alt="" width="100px" height="30px"></i>
            </a>
              <!-- Botón para colapsar el menú en pantallas pequeñas -->
          </div>
          <div class="d-flex align-items-center col-6">
            <div class="input-group">
              <input type="text" class="form-control" placeholder="Buscar...">
              <div class="input-group-append">
                <button class="btn btn-outline-info" type="button">
                  <i class="mdi mdi-magnify"></i>
                </button>
              </div>
            </div>
          </div>
          <div class="d-flex align-items-center">

          </div>
        </div>
      </header>
    <?php
    endif;
    ?>
    <?php if ($logueado || (isset($isTienda) && $isTienda === true)): ?>
      <div class="topnav">
        <div class="container-fluid">
          <?php
          $module = strtolower($module ?? 'tienda');
          include __DIR__ . "/menus/{$module}.php";
          ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Contenido principal -->
    <div class="main-content">
      <div class="page-content">
        <div class="container-fluid">
          <?php if (!empty($errores)): ?>
            <div class="alert alert-danger">
              <ul>
                <?php foreach ($errores as $e): ?>
                  <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>
          <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="alert alert-success alert-dismissible">
              <?= $_SESSION['flash_success'] ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php unset($_SESSION['flash_success']); ?>
          <?php endif; ?>
          <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger alert-dismissible">
              <?= $_SESSION['flash_error'] ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
          <?php endif; ?>