<?php
namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\Admin\Categorias;
use App\Models\Admin\Productos;

class ProductosController extends Controller
{
    // Modelo encargado de consultar las categorías disponibles.
    protected $categoriasModel;

    // Modelo encargado de registrar los productos en la base de datos.
    protected $productosModel;

    // Inicializa los modelos necesarios para manejar categorías y productos.
    public function __construct()
    {
        parent::__construct();
        $this->categoriasModel = new Categorias();
        $this->productosModel = new Productos();
    }

    // Este método valida y registra un nuevo producto en la base de datos.
    public function guardar()
    {
        // Recupera los datos enviados desde el formulario de creación del producto.
        $data = $_POST;

        // Verifica que todos los campos requeridos estén completos antes de guardar.
        if (!empty($data['categoria_id']) && !empty($data['codigo_sku']) && !empty($data['nombre']) && !empty($data['descripcion']) && !empty($data['precio_venta']) && !empty($data['stock_actual'])) {
            // Llama al modelo para guardar el producto en la base de datos.
            $resultado = $this->productosModel->registrar($data);

            // Redirige según el resultado del registro.
            if ($resultado) {
                $_SESSION['flash_success'] = 'Producto registrado correctamente.';
                header('Location: ' . BASE_URL . '/admin/productos');
                exit;
            }
        }

        // Si falla la validación o el guardado, devuelve al formulario con un error.
        $_SESSION['flash_error'] = 'Error al registrar el producto.';
        header('Location: ' . BASE_URL . '/admin/productos/nuevo');
        exit;
    }

    // Muestra la vista principal con el listado de productos del módulo admin.
    public function index()
    {
        $productos = $this->productosModel->buscar();
        $this->view('admin/productos/index', [
            'isAdmin'   => true,
            'module'    => 'admin',
            'pageTitle' => 'Productos',
            'productos' => $productos
        ]);
        exit;
    }

    // Carga el formulario para registrar un nuevo producto y sus categorías disponibles.
    public function nuevo()
    {
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
