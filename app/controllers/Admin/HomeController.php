<?php

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Model;
use PDO;

class HomeController extends Controller
{
    public function __construct()
    {
        parent::__construct();

    }

    public function index()
    {
        $db = (new Model())->getDB();
        // 1. Periodo actual
        /*$periodo = $db->query("SELECT nombre FROM sigi_periodo_academico ORDER BY fecha_inicio DESC LIMIT 1")
            ->fetchColumn();
        $docentes = $db->query("SELECT COUNT(*) FROM sigi_usuarios WHERE id_rol IN (SELECT id FROM sigi_roles WHERE nombre LIKE '%DOCENTE%')")->fetchColumn();
*/
        $this->view('admin/index', [
            //'periodo'   => $periodo,
            //'docentes'  => $docentes,
            'isAdmin'  => true,
            'pageTitle' => 'Sistema de Administración',
            'module'    => 'admin'
        ]);
    }
}
