<?php

namespace Core;

use Core\Auth;
use Core\Model;

// 1. Movemos la función fuera de la clase para evitar errores de re-declaración
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle)
    {
        return strpos($haystack, $needle) === 0;
    }
}

class Controller
{
    public function __construct()
    {
        Auth::start();
        
        // Rutas públicas exactas (login, recuperación, etc.)
        $allowedNoAuth = ['logout', 'login', 'login/acceder', 'recuperar', 'reestablecer']; 

        $current = $_GET['url'] ?? '';

        // 2. Lógica de enrutamiento: Identificamos si es tienda o auth
        $isAuthRoute = str_starts_with(static::class, 'App\Controllers\Auth');
        $isTiendaRoute = str_starts_with($current, 'tienda'); // <-- Esto hace pública TODA la tienda (NO ES NESESARIO INICIAR SESION )
        
        $isRutaPublica = in_array($current, $allowedNoAuth) || $isTiendaRoute || $isAuthRoute;
        $isUserLogged = Auth::user() !== null;

        // 3. Proteger las rutas privadas (Módulo de Administración)
        if (!$isRutaPublica) {
            if (!$isUserLogged || !Auth::validarSesion()) {
                header('Location: ' . BASE_URL . '/login');
                exit;
            }
        }

        // 4. Refresca permisos y logos SOLO si está logueado y la ruta es privada
        if ($isUserLogged && !$isRutaPublica && isset($_SESSION['sigi_user_id'])) {
            $this->cargarDatosDeSesion();
        }
    }

    /**
     * Método para cargar datos en sesión una sola vez, 
     * evitando saturar la base de datos en cada petición.
     */
    private function cargarDatosDeSesion()
    {
        // Solo consultamos la BD si los datos NO existen en la sesión
        if (!isset($_SESSION['permisos_usuario']) || !isset($_SESSION['logo'])) {
            $id_usuario = $_SESSION['user_id'];
            $db = (new Model())->getDB();

            // Consultar permisos
            /*$sql = "SELECT psu.id_sistema, s.nombre as sistema, psu.id_rol, r.nombre as rol
                FROM sigi_permisos_usuarios psu
                INNER JOIN sigi_sistemas_integrados s ON s.id = psu.id_sistema
                INNER JOIN sigi_roles r ON r.id = psu.id_rol
                WHERE psu.id_usuario = ? ORDER BY r.id DESC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$id_usuario]);
            $_SESSION['permisos_usuario'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);*/

            // Obtener información de logos
            /*$sql2 = "SELECT favicon, logo FROM sigi_datos_sistema WHERE id=1";
            $stmt2 = $db->query($sql2); // Usamos query directo porque no hay parámetros
            $datos_logos = $stmt2->fetch(\PDO::FETCH_ASSOC);*/

            /*if ($datos_logos) {
                $_SESSION['favicon'] = $datos_logos['favicon'];
                $_SESSION['logo'] = $datos_logos['logo'];
            }*/
        }
    }

    public function modelo($modelo)
    {
        $modelo = str_replace('/', '\\', $modelo);
        $clase = "App\\Models\\" . $modelo;
        return new $clase();
    }

    /**
     * Cargar una vista y pasarle datos (array keys = variables)
     */
    public function view(string $view, array $data = [])
    {
        extract($data);
        require __DIR__ . '/../app/views/' . $view . '.php';
    }
}