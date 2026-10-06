<?php
namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\Admin\Categorias;

class ProductosController extends Controller
{

    protected $categoriasModel;
    public function __construct()
    {
        // Cargar el modelo de Categorias
        $this->categoriasModel = new Categorias(); // NEW objeto es una instancia de la clase Categorias
    }

    public function index()
    {
        $this->view('admin/productos/index', [
            'isAdmin'   => true,
            'module'    => 'admin',
            'pageTitle' => 'Productos'
        ]);
        exit;
    }
    public function nuevo(){
        $categorias = $this->categoriasModel->Categorias_select();
        $this->view('admin/productos/nuevo', [
            'isAdmin'   => true,
            'module'    => 'admin',
            'pageTitle' => 'Nuevo Producto',
            'categorias' => $categorias
        ]);
        exit;
    }
}
