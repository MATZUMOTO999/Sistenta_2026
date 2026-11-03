<?php
namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\Admin\Usuarios;

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
}