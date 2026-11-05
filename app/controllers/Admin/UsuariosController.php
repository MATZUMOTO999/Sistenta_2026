<?php
namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\Admin\Usuarios;
use PDOException;

class UsuariosController extends Controller
{
    protected $usuariosModel;

    public function __construct()
    {
        parent::__construct();
        $this->usuariosModel = new Usuarios();
    }

    public function index()
    {
        $this->view('admin/usuarios/index', [
            'isAdmin'   => true,
            'module'    => 'admin',
            'pageTitle' => 'Usuarios',
            'usuarios'  => $this->usuariosModel->buscar()
        ]);
        exit;
    }
    public function nuevo(){
        $this->view('admin/usuarios/nuevo', [
            'module'    => 'admin',
            'pageTitle' => 'Nuevo Usuario'
        ]);
        exit;
    }

    public function editar($id)
    {
        $this->verificarPermisos();
        $id = filter_var($id, FILTER_VALIDATE_INT);
        $usuario = $id !== false && $id > 0 ? $this->usuariosModel->ver($id) : false;

        if (!$usuario) {
            $_SESSION['flash_error'] = 'No se encontró el usuario solicitado.';
            header('Location: ' . BASE_URL . '/admin/usuarios');
            exit;
        }

        $this->mostrarFormularioEdicion($usuario);
    }

    public function actualizar()
    {
        $this->verificarPermisos();

        $usuario = [
            'id' => trim((string) ($_POST['id'] ?? '')),
            'nombre' => trim((string) ($_POST['nombre'] ?? '')),
            'apellidos' => trim((string) ($_POST['apellidos'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'telefono' => trim((string) ($_POST['telefono'] ?? '')),
            'rol_id' => trim((string) ($_POST['rol_id'] ?? '')),
            'activo' => trim((string) ($_POST['activo'] ?? ''))
        ];

        $idValido = filter_var($usuario['id'], FILTER_VALIDATE_INT);
        $rolValido = filter_var($usuario['rol_id'], FILTER_VALIDATE_INT);
        $roles = $this->usuariosModel->rolesDisponibles();
        $rolDisponible = false;
        foreach ($roles as $rol) {
            if ((string) $rol['id'] === $usuario['rol_id']) {
                $rolDisponible = true;
                break;
            }
        }

        $datosValidos = $idValido !== false && $idValido > 0
            && trim($usuario['nombre']) !== ''
            && trim($usuario['apellidos']) !== ''
            && filter_var($usuario['email'], FILTER_VALIDATE_EMAIL) !== false
            && $rolValido !== false && $rolValido > 0 && $rolDisponible
            && in_array($usuario['activo'], ['0', '1'], true);

        if (!$datosValidos) {
            $_SESSION['flash_error'] = 'Verifique los datos ingresados e inténtelo nuevamente.';
            $this->mostrarFormularioEdicion($usuario);
            exit;
        }

        if (!$this->usuariosModel->ver($idValido)) {
            $_SESSION['flash_error'] = 'No se encontró el usuario solicitado.';
            header('Location: ' . BASE_URL . '/admin/usuarios');
            exit;
        }

        try {
            $actualizado = $this->usuariosModel->actualizar($usuario);
        } catch (PDOException $exception) {
            error_log('Error al actualizar usuario: ' . $exception->getMessage());
            $_SESSION['flash_error'] = 'No se pudo actualizar el usuario. Revise los datos e inténtelo nuevamente.';
            $this->mostrarFormularioEdicion($usuario);
            exit;
        }

        if (!$actualizado) {
            $_SESSION['flash_error'] = 'No se pudo actualizar el usuario. Revise los datos e inténtelo nuevamente.';
            $this->mostrarFormularioEdicion($usuario);
            exit;
        }

        $_SESSION['flash_success'] = 'Usuario actualizado correctamente.';
        header('Location: ' . BASE_URL . '/admin/usuarios');
        exit;
    }

    public function eliminar()
    {
        $this->verificarPermisos();

        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        if ($id === false || $id === null || $id < 1) {
            $_SESSION['flash_error'] = 'No se pudo identificar al usuario que desea eliminar.';
            header('Location: ' . BASE_URL . '/admin/usuarios');
            exit;
        }

        if ((int) ($_SESSION['user_id'] ?? 0) === $id) {
            $_SESSION['flash_error'] = 'No puedes eliminar tu propio usuario mientras tienes sesión iniciada.';
            header('Location: ' . BASE_URL . '/admin/usuarios');
            exit;
        }

        if (!$this->usuariosModel->ver($id)) {
            $_SESSION['flash_error'] = 'No se encontró el usuario solicitado.';
            header('Location: ' . BASE_URL . '/admin/usuarios');
            exit;
        }

        try {
            $this->usuariosModel->desactivar($id);
        } catch (PDOException $exception) {
            error_log('Error al desactivar usuario: ' . $exception->getMessage());
            $_SESSION['flash_error'] = 'No se pudo eliminar el usuario. Inténtelo nuevamente.';
            header('Location: ' . BASE_URL . '/admin/usuarios');
            exit;
        }

        $_SESSION['flash_success'] = 'Usuario eliminado correctamente. El usuario fue desactivado y sus sesiones cerradas.';
        header('Location: ' . BASE_URL . '/admin/usuarios');
        exit;
    }

    private function mostrarFormularioEdicion(array $usuario)
    {
        $this->view('admin/usuarios/editar', [
            'isAdmin' => true,
            'module' => 'admin',
            'pageTitle' => 'Editar Usuario',
            'usuario' => $usuario,
            'roles' => $this->usuariosModel->rolesDisponibles()
        ]);
    }

    private function verificarPermisos()
    {
        if (!\Core\Auth::esAdmin()) {
            http_response_code(403);
            echo 'No tienes permisos para realizar esta acción.';
            exit;
        }
    }
}