<?php
namespace App\Controllers\Admin;

use Core\Controller;
use PDOException;

//Inluir los modelos necesarios
require_once __DIR__ . '/../../models/Admin/Categorias.php';

use App\Models\Admin\Categorias;

class CategoriasController extends Controller
{
    protected $categoriaModel;

    public function __construct()
    {
        parent::__construct();
        $this->categoriaModel = new Categorias();
    }
    public function index()
    {
        $this->view('admin/categorias/index', [
            'isAdmin'  => true,
            'module'    => 'admin',
            'pageTitle' => 'Categorías'
        ]);
        exit;
    }
    public function datatable(){
        header('Content-Type: application/json; charset=utf-8');
        $draw = isset($_GET['draw']) ? intval($_GET['draw']) : 1;
        $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
        $length = isset($_GET['length']) ? intval($_GET['length']) : 10;
        $orderColumnIndex = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 0;
        $orderDir = isset($_GET['order'][0]['dir']) ? $_GET['order'][0]['dir'] : 'asc';

        $filters = [];
        $resultado = $this->categoriaModel->obtenerCategorias($start, $length, $orderColumnIndex, $orderDir, $filters);

        echo json_encode([
            'draw' => $draw,
            'recordsTotal' => $resultado['total'],
            'recordsFiltered' => $resultado['total'],
            'data' => $resultado['data']
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function nuevo()
    {
        $this->verificarPermisos();
        $this->view('admin/categorias/nuevo', [
            'isAdmin'  => true,
            'module'    => 'admin',
            'pageTitle' => 'Nueva Categoría'
        ]);
        exit;
    }
    public function guardar()
    {
        $this->verificarPermisos();
        $data = [
            'nombre' => trim((string) ($_POST['nombre'] ?? '')),
            'descripcion' => trim((string) ($_POST['descripcion'] ?? ''))
        ];
        if ($data['nombre'] === '') {
            $_SESSION['flash_error'] = 'El nombre de la categoría es obligatorio.';
            header('Location: ' . BASE_URL . '/admin/categorias/nuevo');
            exit;
        }

        try {
            $this->categoriaModel->guardar($data);
        } catch (PDOException $exception) {
            error_log('Error al crear categoría: ' . $exception->getMessage());
            $_SESSION['flash_error'] = 'No se pudo guardar la categoría.';
            header('Location: ' . BASE_URL . '/admin/categorias/nuevo');
            exit;
        }

        $_SESSION['flash_success'] = 'Categoría creada correctamente.';
        header('Location: ' . BASE_URL . '/admin/categorias');
        exit;
    }

    public function editar($id)
    {
        $this->verificarPermisos();
        $id = filter_var($id, FILTER_VALIDATE_INT);
        $categoria = $id !== false && $id > 0 ? $this->categoriaModel->ver($id) : false;

        if (!$categoria) {
            $_SESSION['flash_error'] = 'No se encontró la categoría solicitada.';
            header('Location: ' . BASE_URL . '/admin/categorias');
            exit;
        }

        $this->mostrarFormularioEdicion($categoria);
    }

    public function actualizar()
    {
        $this->verificarPermisos();
        $categoria = [
            'id' => trim((string) ($_POST['id'] ?? '')),
            'nombre' => trim((string) ($_POST['nombre'] ?? '')),
            'descripcion' => trim((string) ($_POST['descripcion'] ?? '')),
            'activo' => trim((string) ($_POST['activo'] ?? ''))
        ];
        $id = filter_var($categoria['id'], FILTER_VALIDATE_INT);
        $datosValidos = $id !== false && $id > 0
            && $categoria['nombre'] !== ''
            && in_array($categoria['activo'], ['0', '1'], true);

        if (!$datosValidos) {
            $_SESSION['flash_error'] = 'Verifique los datos ingresados e inténtelo nuevamente.';
            $this->mostrarFormularioEdicion($categoria);
            exit;
        }

        if (!$this->categoriaModel->ver($id)) {
            $_SESSION['flash_error'] = 'No se encontró la categoría solicitada.';
            header('Location: ' . BASE_URL . '/admin/categorias');
            exit;
        }

        try {
            $actualizado = $this->categoriaModel->actualizar($categoria);
        } catch (PDOException $exception) {
            error_log('Error al actualizar categoría: ' . $exception->getMessage());
            $_SESSION['flash_error'] = 'No se pudo actualizar la categoría.';
            $this->mostrarFormularioEdicion($categoria);
            exit;
        }

        if (!$actualizado) {
            $_SESSION['flash_error'] = 'No se pudo actualizar la categoría.';
            $this->mostrarFormularioEdicion($categoria);
            exit;
        }

        $_SESSION['flash_success'] = 'Categoría actualizada correctamente.';
        header('Location: ' . BASE_URL . '/admin/categorias');
        exit;
    }

    public function eliminar()
    {
        $this->verificarPermisos();
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

        if ($id === false || $id === null || $id < 1) {
            $_SESSION['flash_error'] = 'No se pudo identificar la categoría que desea eliminar.';
            header('Location: ' . BASE_URL . '/admin/categorias');
            exit;
        }

        if (!$this->categoriaModel->ver($id)) {
            $_SESSION['flash_error'] = 'No se encontró la categoría solicitada.';
            header('Location: ' . BASE_URL . '/admin/categorias');
            exit;
        }

        try {
            $eliminada = $this->categoriaModel->eliminar($id);
        } catch (PDOException $exception) {
            error_log('Error al eliminar categoría: ' . $exception->getMessage());
            $_SESSION['flash_error'] = 'No se pudo eliminar la categoría.';
            header('Location: ' . BASE_URL . '/admin/categorias');
            exit;
        }

        if (!$eliminada) {
            $_SESSION['flash_error'] = $this->categoriaModel->tieneProductos($id)
                ? 'No se puede eliminar la categoría porque tiene productos asociados.'
                : 'No se pudo eliminar la categoría.';
            header('Location: ' . BASE_URL . '/admin/categorias');
            exit;
        }

        $_SESSION['flash_success'] = 'Categoría eliminada correctamente.';
        header('Location: ' . BASE_URL . '/admin/categorias');
        exit;
    }

    private function mostrarFormularioEdicion(array $categoria)
    {
        $this->view('admin/categorias/editar', [
            'isAdmin' => true,
            'module' => 'admin',
            'pageTitle' => 'Editar Categoría',
            'categoria' => $categoria
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