<?php

namespace Core;

class Auth
{
    public static function start()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
    }

    public static function login(array $user)
    {
        self::start();
        $_SESSION['session_id']   = $user['id_session'];
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['nombre'].' '.$user['apellidos'];
        $_SESSION['token'] = $user['token'];
        $_SESSION['rol_actual'] = $user['rol_id'];
        // Regenerar ID para evitar session fixation
        session_regenerate_id(true);
    }
    public static function crearPassword(int $longitud = 8)
    {
        $parteAleatoria = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $longitud);
        $password = 'Tienda.' . $parteAleatoria . '1';
        return $password;
    }

    public static function user(): ?array
    {
        self::start();
        return (isset($_SESSION['user_id']) && $_SESSION['user_id']) ? $_SESSION : null;
    }

    public static function logout()
    {
        self::start();

        if (!empty($_SESSION['session_id'])) {
            $db = (new Model())->getDB();
            $stmt = $db->prepare("UPDATE sesiones SET activa = 0 WHERE id = ?");
            $stmt->execute([$_SESSION['session_id']]);
        }
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, '/'); // Eliminar cookie de sesión
        }
        session_destroy();
    }

    public static function validarSesion()
    {
        self::start();
        if (!empty($_SESSION['session_id'])) {
            $session_id = $_SESSION['session_id'] ?? null;
            $user_id = $_SESSION['user_id'] ?? null;

            if (!$session_id || !$user_id) {
                self::logout();
                return false;
            }
            $db = (new Model())->getDB();
            $stmt = $db->prepare("SELECT * FROM sesiones 
                           WHERE id = ? AND usuario_id = ? AND activa = 1");
            $stmt->execute([$session_id, $user_id]);
            $sesion = $stmt->fetch();

            $tiempoSession = 4 * 60; // TIEMPO DE SESION ABIERTO

            if (!$sesion) {
                self::logout(); // sesión expirada o cerrada desde admin
                return false;
            }
            // Verificar inactividad (ej. 30 minutos)
            $ultima = strtotime($sesion['fecha_expiracion']);
            if ((time() - $ultima) > $tiempoSession) {
                // Expira sesión
                $db->prepare("UPDATE sesiones SET activa = 0 WHERE id = ?")
                    ->execute([$session_id]);
                self::logout();
                return false;
            }
            // Actualizar última actividad
            $db->prepare("UPDATE sesiones SET fecha_expiracion = NOW() WHERE id = ?")
                ->execute([$session_id]);
            return true;
        }
        return false;
    }

    // VALIDACION DE ROL Y PERMISOS SIGI
    public static function esAdmin()
    {
        return (isset($_SESSION['rol_actual']) && $_SESSION['rol_actual'] == 1);     // ADMINISTRADOR
    }

    public static function tieneRolEnAdmin($roles = [])
    {
        if (!isset($_SESSION['rol_actual'])) {
            return false;
        }
        
        if (empty($roles)) return true; // Cualquier rol
        return in_array($_SESSION['rol_actual'], (array)$roles);
    }

    //============ CLIENTES ===============
    public static function esCliente()
    {
        return (isset($_SESSION['rol_actual']) && $_SESSION['rol_actual'] == 2);     // CLIENTE
    }
}
