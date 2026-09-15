<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - SIGI</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <?php
    /*if ($datosSistema['favicon'] != '') {
        $ruta_favicon = BASE_URL . '/images/' . $datosSistema['favicon'];
    } else {*/
        $ruta_favicon = BASE_URL . '/img/favicon.ico';
    /*}*/
    ?>
    <link rel="icon" type="image/x-icon" href="<?= $ruta_favicon ?>">
    <style>
        body {

            min-height: 100vh;
        }

        .login-container {
            min-height: 100vh;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }

        .logo {
            width: 200px;
            margin-bottom: 10px;
        }
    </style>
</head>
<?php
//echo password_hash('',PASSWORD_DEFAULT);

?>

<body style="background-color: <?= $datosSistema['color_correo'] ?>;">
    <div class="container d-flex align-items-center justify-content-center login-container">
        <div class="col-md-6 col-lg-6">
            <div class="card p-4">
                <div class="text-center">
                    <?php
                    if ($datosSistema['logo'] != '') {
                        $ruta_logo = BASE_URL . '/images/' . $datosSistema['logo'];
                    } else {
                        $ruta_logo = BASE_URL . '/img/logo_completo.png';
                    }
                    ?>
                    <img src="<?= $ruta_logo ?>" alt="Logo SIGI" class="logo">
                    <h4 class="mb-2">SISVENTAS</h4>
                    <p class="text-muted mb-4">Sistema de Ventas</p>
                </div>
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">Credenciales incorrectas</div>
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
                <form action="<?= BASE_URL ?>/login/acceder" method="post">
                    <div class="form-group">
                        <label for="correo">Correo</label>
                        <input type="email" name="correo" id="correo" class="form-control" placeholder="Ingrese su correo" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control" placeholder="Ingrese su contraseña" required>
                            <div class="input-group-append">
                                <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Ingresar</button>
                </form>
                <p class="mt-3 text-center">
                    <a href="<?= BASE_URL ?>/recuperar">¿Olvidaste tu contraseña?</a>
                </p>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');
            const icon = togglePassword.querySelector('i');

            togglePassword.addEventListener('click', function(e) {
                // toggle the type attribute
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);

                // toggle the eye icon
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });
        });
    </script>
</body>

</html>