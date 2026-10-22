<?php
namespace App\Models\Admin;

use Core\Model;
use PDO;

// herencia abstracción encapsulación
// modelo de datos de productos
class Productos extends Model
{
    protected $table = 'productos';

    public function registrar($data)
    {
        $sql = "INSERT INTO {$this->table} (categoria_id, codigo_sku, nombre, descripcion, precio_venta, stock_actual) VALUES (:categoria_id, :codigo_sku, :nombre, :descripcion, :precio_venta, :stock_actual)";
        $params = [
            ':categoria_id' => $data['categoria_id'],
            ':codigo_sku' => $data['codigo_sku'],
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'],
            ':precio_venta' => $data['precio_venta'],
            ':stock_actual' => $data['stock_actual']
        ];
        $preparado = self::$db->prepare($sql);
        return $preparado->execute($params);
    }

    public function buscar(){
        $sql = "SELECT p.*, c.nombre AS categoria_nombre FROM {$this->table} p INNER JOIN categorias c ON p.categoria_id = c.id";
        $preparado = self::$db->prepare($sql);
        $preparado->execute();
        return $preparado->fetchAll(PDO::FETCH_ASSOC); 
    }

    public function actualizar(array $data)
    {
        $sql = "UPDATE {$this->table}
                SET categoria_id = :categoria_id,
                    codigo_sku = :codigo_sku,
                    nombre = :nombre,
                    descripcion = :descripcion,
                    precio_venta = :precio_venta,
                    stock_actual = :stock_actual,
                    activo = :activo
                WHERE id = :id";
        $preparado = self::$db->prepare($sql);
        return $preparado->execute([
            ':categoria_id' => $data['categoria_id'],
            ':codigo_sku' => $data['codigo_sku'],
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'],
            ':precio_venta' => $data['precio_venta'],
            ':stock_actual' => $data['stock_actual'],
            ':activo' => $data['activo'],
            ':id' => $data['id']
        ]);
    }

    public function ver(Int $id){
        $params = [':id' => $id];
        $sql = "SELECT * FROM {$this->table} WHERE id=:id";
        $preparado = self::$db->prepare($sql);
        $preparado->execute($params);
        return $preparado->fetch(PDO::FETCH_ASSOC);
    }
    //metodo editar
    public function editar($id){
        // Estas categorías se utilizan para llenar el selector del formulario.
        $categorias = $this->categoriasModel->categorias_select();
        //buscar un producto por id
        $producto = $this->productosModel->ver($id);
        //var_dump($producto); 
        // Envía a la vista los datos necesarios para renderizar el formulario.
        $this->view('admin/productos/editar', [
            'isAdmin'   => true,
            'module'    => 'admin',
            'pageTitle' => 'editar Producto',
            'categorias' => $categorias,
            'producto' => $producto
        ]);
        exit;
    }

    public function eliminar()
    {
    }
}