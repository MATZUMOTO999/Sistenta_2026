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

    public function ver()
    {
    }

    public function actualizar()
    {
    }

    public function eliminar()
    {
    }
}