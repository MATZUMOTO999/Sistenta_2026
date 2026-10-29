<?php
namespace App\Controllers\Admin;

include_once __DIR__ . '/../../models/Admin/Categorias.php';
include_once __DIR__ . '/../../models/Admin/Productos.php';
include_once __DIR__ . '/../../models/Admin/Ventas.php';


use Core\Controller;
use App\Models\Admin\Categorias;
use App\Models\Admin\Productos;
use App\Models\Admin\Ventas;
use PDOException;

class ProductosController extends Controller
{
    // Modelo encargado de consultar las categorías disponibles.
    protected $categoriasModel;

    // Modelo encargado de registrar los productos en la base de datos.
    protected $productosModel;

    // Modelo encargado de consultar las ventas asociadas a un producto.
    protected $ventasModel;

    // Inicializa los modelos necesarios para manejar categorías y productos.
    public function __construct()
    {
        parent::__construct();
        $this->categoriasModel = new Categorias();
        $this->productosModel = new Productos();
        $this->ventasModel = new Ventas();
    }

    // Este método valida y registra un nuevo producto en la base de datos.
    public function guardar()
    {
        $data = [
            'categoria_id' => (string) ($_POST['categoria_id'] ?? ''),
            'codigo_sku' => (string) ($_POST['codigo_sku'] ?? ''),
            'nombre' => (string) ($_POST['nombre'] ?? ''),
            'descripcion' => (string) ($_POST['descripcion'] ?? ''),
            'precio_venta' => (string) ($_POST['precio_venta'] ?? ''),
            'stock_actual' => (string) ($_POST['stock_actual'] ?? '')
        ];

        $categoriaValida = filter_var($data['categoria_id'], FILTER_VALIDATE_INT);
        $stockValido = filter_var($data['stock_actual'], FILTER_VALIDATE_INT);
        //  validacion para comprobar las condiciones 
        $precioValido = is_numeric($data['precio_venta'])
            ? (float) $data['precio_venta'] >= 0
            : false;
        $datosValidos = !in_array(false, [
            $categoriaValida !== false,
            $categoriaValida > 0,
            trim($data['codigo_sku']) !== '',
            trim($data['nombre']) !== '',
            trim($data['descripcion']) !== '',
            $precioValido,
            $stockValido !== false,
            $stockValido >= 0
        ], true);

        if ($datosValidos) {
            try {
                $resultado = $this->productosModel->registrar($data);
            } catch (PDOException $exception) {
                error_log('Error al registrar producto: ' . $exception->getMessage());
                $resultado = false;
            }

            // Redirige según el resultado del registro.
            if ($resultado) {
                $_SESSION['flash_success'] = 'Producto registrado correctamente.';
                header('Location: ' . BASE_URL . '/admin/productos');
                exit;
            }
        }

        // Si falla la validación o el guardado, devuelve al formulario con un error.
        $_SESSION['flash_error'] = 'Error al registrar el producto.';
        $this->mostrarFormularioNuevo($data);
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
        $this->mostrarFormularioNuevo([]);
        exit;
    }

    private function mostrarFormularioNuevo(array $formData)
    {
        $this->view('admin/productos/nuevo', [
            'isAdmin' => true,
            'module' => 'admin',
            'pageTitle' => 'Nuevo Producto',
            'categorias' => $this->categoriasModel->Categorias_select(),
            'formData' => $formData
        ]);
    }
    // producto editar
    //metodo editar
    public function editar($id)
    {
        $categorias = $this->categoriasModel->Categorias_select();
        $producto = $this->productosModel->ver($id);

        if (!$producto) {
            $_SESSION['flash_error'] = 'No se encontró el producto solicitado.';
            header('Location: ' . BASE_URL . '/admin/productos');
            exit;
        }

        $this->view('admin/productos/editar', [
            'isAdmin'   => true,
            'module'    => 'admin',
            'pageTitle' => 'Editar Producto',
            'categorias' => $categorias,
            'producto' => $producto
        ]);
        exit;
    }

    // Recibe el formulario de edición, valida sus datos y solicita al modelo actualizar el producto.
    public function actualizar()
    {
        // Normaliza los campos del formulario antes de validarlos y enviarlos al modelo.
        $producto = [
            'id' => (string) ($_POST['id'] ?? ''),
            'categoria_id' => (string) ($_POST['categoria_id'] ?? ''),
            'codigo_sku' => (string) ($_POST['codigo_sku'] ?? ''),
            'nombre' => (string) ($_POST['nombre'] ?? ''),
            'descripcion' => (string) ($_POST['descripcion'] ?? ''),
            'precio_venta' => (string) ($_POST['precio_venta'] ?? ''),
            'stock_actual' => (string) ($_POST['stock_actual'] ?? ''),
            'activo' => (string) ($_POST['activo'] ?? '')
        ];

        $idValido = filter_var($producto['id'], FILTER_VALIDATE_INT);
        $categoriaValida = filter_var($producto['categoria_id'], FILTER_VALIDATE_INT);
        $stockValido = filter_var($producto['stock_actual'], FILTER_VALIDATE_INT);
        //validacion para comprobar las condiciones8888
        $precioValido = is_numeric($producto['precio_venta'])
            ? (float) $producto['precio_venta'] >= 0
            : false;
        $datosValidos = !in_array(false, [
            $idValido !== false,
            $idValido > 0,
            $categoriaValida !== false,
            $categoriaValida > 0,
            trim($producto['codigo_sku']) !== '',
            trim($producto['nombre']) !== '',
            trim($producto['descripcion']) !== '',
            $precioValido,
            $stockValido !== false,
            $stockValido >= 0,
            in_array($producto['activo'], ['0', '1'], true)
        ], true);

        if (!$datosValidos) {
            // Si los datos no pasan la validación, conserva lo escrito y vuelve a mostrar el formulario.
            $_SESSION['flash_error'] = 'Verifique los datos ingresados e inténtelo nuevamente.';
            $this->mostrarFormularioEdicion($producto);
            exit;
        }

        try {
            // La persistencia se realiza en Productos::actualizar().
            $actualizado = $this->productosModel->actualizar($producto);
        } catch (PDOException $exception) {
            error_log('Error al actualizar producto: ' . $exception->getMessage());
            $_SESSION['flash_error'] = 'No se pudo actualizar el producto. Revise los datos e inténtelo nuevamente.';
            $this->mostrarFormularioEdicion($producto);
            exit;
        }

        if (!$actualizado) {
            $_SESSION['flash_error'] = 'No se pudo actualizar el producto. Revise los datos e inténtelo nuevamente.';
            $this->mostrarFormularioEdicion($producto);
            exit;
        }

        $_SESSION['flash_success'] = 'Producto actualizado correctamente.';
        header('Location: ' . BASE_URL . '/admin/productos');
        exit;
    }

    private function mostrarFormularioEdicion(array $producto)
    {
        $this->view('admin/productos/editar', [
            'isAdmin' => true,
            'module' => 'admin',
            'pageTitle' => 'Editar Producto',
            'categorias' => $this->categoriasModel->Categorias_select(),
            'producto' => $producto
        ]);
        
    }
    public function eliminar($id){
        $detalle_ventas = $this->ventasModel->ver_detalle_ventas_Id_producto($id);
        if($detalle_ventas){
            $_SESSION['flash_error'] = "No se puede eliminar el producto porque tiene ventas asociadas";
            header('Location: ' . BASE_URL . '/admin/productos');
        }else{
            $eliminar = $this->productosModel->eliminar($id);
            if($eliminar){
                $_SESSION['flash_success'] = "Producto eliminado correctamente";
                header('Location: ' . BASE_URL . '/admin/productos');
            }else{
                $_SESSION['flash_error'] = "Error al eliminar el producto";
                header('Location: ' . BASE_URL . '/admin/productos');
            }
        }
    }
}
