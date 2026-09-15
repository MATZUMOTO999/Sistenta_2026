<?php
namespace App\Controllers\Admin;

use Core\Controller;

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
        $this->view('admin/categorias/nuevo', [
            'isAdmin'  => true,
            'module'    => 'admin',
            'pageTitle' => 'Nueva Categoría'
        ]);
        exit;
    }
    public function guardar()
    {
        if(\Core\Auth::esAdmin()): 
            $data =[
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion']
            ];
            $this->categoriaModel->guardar($data);
        endif;
        header('Location: ' . BASE_URL . '/admin/categorias');
        exit;
    }
}