<?php
namespace App\Controllers\Admin;

use Core\Controller;

class UsuariosController extends Controller
{
    public function index()
    {
        $this->view('admin/usuarios/index', [
            'module'    => 'admin',
            'pageTitle' => 'Usuarios'
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
}