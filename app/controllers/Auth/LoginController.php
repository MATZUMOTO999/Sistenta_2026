<?php

namespace App\Controllers\Auth;

//require_once __DIR__ . '/../../../app/models/Sigi/DatosInstitucionales.php';
require_once __DIR__ . '/../../../vendor/autoload.php';

use Core\Controller;
use PDO;

class LoginController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }
    /* Formulario */
    public function index()
    {
        //$datosSistema = $this->objDatosSistema->buscar();
        $this->view('auth/login', [
            //'datosSistema' => $datosSistema,
            'pageTitle' => 'Iniciar Sesión',
            'module'    => 'auth'
        ]);
    }

    /* POST credenciales */
    public function acceder()
    {

        $correo  = $_POST['correo'] ?? '';
        $pass = $_POST['password'] ?? '';

        $db = (new \Core\Model())->getDB();

        $sql = "SELECT * FROM usuarios WHERE email = :correo AND activo = 1 LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':correo' => $correo]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);



        if ($user && password_verify($pass, $user['password_hash'])) {
            //guardamos el registro de session
            $llave = bin2hex(random_bytes(5));
            $token = password_hash($llave, PASSWORD_DEFAULT);

            $sql = "INSERT INTO sesiones (usuario_id, token, ip_address, user_agent, fecha_expiracion) VALUES (?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$user['id'], $llave, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], date('Y-m-d H:i:s', strtotime('+4 hour'))]);

            $user['id_session'] = $db->lastInsertId();
            $user['token'] = $token;

            \Core\Auth::login($user);               // ← guarda sigi_user_id y sigi_user_name

            // --- CARGAR PERMISOS DEL USUARIO Y SETEAR MODULO/ROL ACTIVO ---
            $sql = "SELECT rol_id
                FROM usuarios
                WHERE id = ? ORDER BY rol_id DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute([$user['id']]);
            $_SESSION['permisos_usuario'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($user['rol_id'] == 1) {
                header('Location: ' . BASE_URL . '/admin');  // ← redirige a dashboard
            }elseif ($user['rol_id'] == 2) {
                header('Location: ' . BASE_URL . '/tienda');  // ← redirige a dashboard
            }
            
            exit;                                   // ← imprescindible
        }

        header('Location: ' . BASE_URL . '/login?error=1');
        exit;
    }
    public function salir()
    {
        \Core\Auth::logout();
        header('Location: ' . BASE_URL . '/login');
        exit;
    }



    public function recuperar()
    {
        $datosSistema = $this->objDatosSistema->buscar();
        // Form para pedir correo/DNI
        $this->view('auth/recuperar', ['datosSistema' => $datosSistema, 'pageTitle' => 'Recuperar contraseña']);
    }

    public function resetPassword()
    {
        if (!\Core\Auth::user()) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
        $back = $_GET['back'];
        if ($back == '') {
            $back = "/intranet";
        }
        $id_user = base64_decode($_GET['data']);
        if (is_numeric($id_user)) {
            $user = $this->objDocente->find($id_user);
            if (!$user) {
                // Para no revelar existencia del usuario, damos siempre OK
                $_SESSION['flash_error'] = 'Error, Fallo al buscar Usuario';
                header('Location: ' . BASE_URL . $back);
                exit;
            }
            // token 24 chars (cabe en VARCHAR(30))
            $token = bin2hex(random_bytes(12));
            $okTok = $this->objDocente->setResetToken((int)$user['id'], $token);
            if (!$okTok) { // si no tocó filas, algo va mal
                $_SESSION['flash_error'] = 'No se pudo generar el token de recuperación.';
                header('Location: ' . BASE_URL . $back);
                exit;
            }
            $cfg = $this->objDatosSistema->buscar();
            $datosIes = $this->objDatosIes->buscar();
            $data = base64_encode($user['id']);
            $resetUrl = BASE_URL . '/restablecer?data=' . $data . '&token=' . urlencode($token);
            // Plantilla simple (puedes llevarla a una vista)
            if ($cfg['logo'] != '') {
                $ruta_logo = BASE_URL . '/images/' . $cfg['logo'];
            } else {
                $ruta_logo = BASE_URL . '/img/logo_completo.png';
            }
            $html = $this->objDatosSistema->sigi_email_template([
                'brand'     => 'SIGI ' . $cfg['nombre_corto'],
                'logo'      => $ruta_logo,
                'primary'   => htmlspecialchars($cfg['color_correo'] ?: '#062758ff'), // celeste
                'title'     => 'Recuperación de contraseña',
                'intro'     => 'Hola ' . $user['apellidos_nombres'] . ', recibimos una solicitud para restablecer tu contraseña.',
                'body_html' => 'Haz clic en el botón para crear una nueva contraseña.',
                'cta_text'  => 'Restablecer contraseña',
                'cta_url'   => $resetUrl,
                'note'      => 'Si no solicitaste este cambio, ignora este mensaje. Tu cuenta se mantendrá segura.',
                'address'   => $datosIes['direccion'],
                'preheader' => 'Crea una nueva contraseña para tu cuenta SIGI',
            ]);
            $alt = "Recuperación de contraseña\n\n" .
                "Abre este enlace para restablecerla: {$resetUrl}\n\n" .
                "Si no solicitaste este cambio, ignora este mensaje.";

            $mail = new Mailer();
            $res = $mail->send($user['correo'], $user['apellidos_nombres'], 'Restablecer contraseña', $html, $alt);

            $_SESSION['flash_success'] = 'se envió un correo con instrucciones.' . $res['error'];
            header('Location: ' . BASE_URL . $back);
            exit;
        }
    }

    public function enviarRecuperacion()
    {
        $email = trim($_POST['email'] ?? '');
        $dni = trim($_POST['dni'] ?? '');
        if ($email == '' || $dni == '') {
            $_SESSION['flash_error'] = 'Ingrese su correo y DNI.';
            header('Location: ' . BASE_URL . '/recuperar');
            exit;
        }
        $user = $this->objDocente->findByEmailAndDni($email, $dni);
        if (!$user) {
            // Para no revelar existencia del usuario, damos siempre OK
            $_SESSION['flash_error'] = 'No se encontró Usuario';
            header('Location: ' . BASE_URL . '/recuperar');
            exit;
        }
        // token 24 chars (cabe en VARCHAR(30))
        $token = bin2hex(random_bytes(12));
        $okTok = $this->objDocente->setResetToken((int)$user['id'], $token);
        if (!$okTok) { // si no tocó filas, algo va mal
            $_SESSION['flash_error'] = 'No se pudo generar el token de recuperación.';
            header('Location: ' . BASE_URL . '/recuperar');
            exit;
        }

        $cfg = $this->objDatosSistema->buscar();
        $datosIes = $this->objDatosIes->buscar();
        $data = base64_encode($user['id']);
        $resetUrl = BASE_URL . '/restablecer?data=' . $data . '&token=' . urlencode($token);
        // Plantilla simple (puedes llevarla a una vista)
        if ($cfg['logo'] != '') {
            $ruta_logo = BASE_URL . '/images/' . $cfg['logo'];
        } else {
            $ruta_logo = BASE_URL . '/img/logo_completo.png';
        }
        $html = $this->objDatosSistema->sigi_email_template([
            'brand'     => 'SIGI ' . $cfg['nombre_corto'],
            'logo'      => $ruta_logo,
            'primary'   => htmlspecialchars($cfg['color_correo'] ?: '#062758ff'), // celeste
            'title'     => 'Recuperación de contraseña',
            'intro'     => 'Hola ' . $user['apellidos_nombres'] . ', recibimos una solicitud para restablecer tu contraseña.',
            'body_html' => 'Haz clic en el botón para crear una nueva contraseña.',
            'cta_text'  => 'Restablecer contraseña',
            'cta_url'   => $resetUrl,
            'note'      => 'Si no solicitaste este cambio, ignora este mensaje. Tu cuenta se mantendrá segura.',
            'address'   => $datosIes['direccion'],
            'preheader' => 'Crea una nueva contraseña para tu cuenta SIGI',
        ]);

        $alt = "Recuperación de contraseña\n\n" .
            "Abre este enlace para restablecerla: {$resetUrl}\n\n" .
            "Si no solicitaste este cambio, ignora este mensaje.";

        $res = Mailer::send($user['correo'], $user['apellidos_nombres'], 'Restablecer contraseña', $html, $alt);

        $_SESSION['flash_success'] = 'Si existe una cuenta, se envió un correo con instrucciones.' . $res['error'];
        header('Location: ' . BASE_URL . '/recuperar');
        exit;
    }

    public function restablecer()
    {
        $datosSistema = $this->objDatosSistema->buscar();
        $token = trim($_GET['token'] ?? '');
        $data = trim($_GET['data']) ?? '';
        $id_user = base64_decode($data);

        if ($token === '') {
            $_SESSION['flash_error'] = 'Token inválido.';
            header('Location: ' . BASE_URL . '/recuperar');
            exit;
        }

        $user = $this->objDocente->findByResetToken($token, $id_user);
        if (!$user) {
            $_SESSION['flash_error'] = 'Token inválido o ya utilizado.';
            header('Location: ' . BASE_URL . '/recuperar');
            exit;
        }

        $this->view('auth/restablecer', [
            'datosSistema' => $datosSistema,
            'token' => $token,
            'data' => $data,
            'usuario' => $user,
            'pageTitle' => 'Nueva contraseña'
        ]);
    }

    public function guardarNuevaPassword()
    {
        $token = trim($_POST['token'] ?? '');
        $data = trim($_POST['data']) ?? '';
        $id_user = base64_decode($data);
        $pass1 = trim($_POST['password'] ?? '');
        $pass2 = trim($_POST['password_confirm'] ?? '');

        if ($token == '' || $data == '' || $pass1 == '' || $pass2 == '' || $pass1 !== $pass2) {
            $_SESSION['flash_error'] = 'Revise la contraseña y la confirmación.';
            header('Location: ' . BASE_URL . '/restablecer?data=' . $data . '&token=' . urlencode($token));
            exit;
        }
        $user = $this->objDocente->findByResetToken($token, $id_user);
        if (!$user) {
            $_SESSION['flash_error'] = 'Token inválido o ya utilizado.';
            header('Location: ' . BASE_URL . '/recuperar');
            exit;
        }

        // Seguridad mínima de clave (ajusta a tu política)
        if (strlen($pass1) < 8) {
            $_SESSION['flash_error'] = 'La contraseña debe tener al menos 8 caracteres.';
            header('Location: ' . BASE_URL . '/restablecer?data=' . $data . '&token=' . urlencode($token));
            exit;
        }
        // Opcional: Validar complejidad para evitar rechazo de Moodle
        if (!preg_match('/[A-Z]/', $pass1) || !preg_match('/[0-9]/', $pass1) || !preg_match('/[\W]/', $pass1)) {
            $_SESSION['flash_error'] = 'La contraseña debe tener una Mayúscula, un Número y un Símbolo.';
            header('Location: ' . BASE_URL . '/restablecer?data=' . $data . '&token=' . urlencode($token));
            exit;
        }

        $hash = password_hash($pass1, PASSWORD_BCRYPT);
        $this->objDocente->updatePassword((int)$user['id'], $hash);
        // Lógica de separación de nombres (Igual que en DocentesController)
        $parts = explode('_', $user['apellidos_nombres']);
        if (count($parts) >= 3) {
            $lastname = $parts[0] . ' ' . $parts[1]; // Apellido Paterno + Materno
            $firstname = $parts[2]; // Nombres restantes
        } else {
            $lastname = $parts[0];
            $firstname = isset($parts[1]) ? $parts[1] : '-';
        }
        $programa_est = $this->objPrograma->find($user['id']);
        $nombre_programa = $programa_est['nombre'];

        $_SESSION['flash_success'] .= ' Contraseña actualizada. Ahora puedes iniciar sesión.';
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}
