<?php
namespace App\Controllers\Admin;

use Core\Controller;

class ProductosController extends Controller
{
    public function index()
    {
        $this->view('admin/productos/index', [
            'module'    => 'admin',
            'pageTitle' => 'Productos'
        ]);
        exit;
    }
    public function nuevo(){
        $categorias = ['Electrónica', 'Ropa', 'Hogar'];
        $this->view('admin/productos/nuevo', [
            'module'    => 'admin',
            'pageTitle' => 'Nuevo Producto',
            'categorias' => $categorias
        ]);
        exit;
    }
}