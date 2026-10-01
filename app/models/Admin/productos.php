<?php
namespace App\Controllers\Admin;
use Core\Model;
use PDO;

// herencia abstracción encapsulación
// modelo de datos de productos
class Productos extends Model{
    protected $table = 'productos';
    public function registrar($data){
        $sql = "INSERT INTO {$this->table} (categoria_id, codigo_sku, nombre, descripcion, precio_venta, stock_actual, activo, fecha_creacion) VALUES (:categoria_id, :codigo_sku, :nombre, :descripcion, :precio_venta, :stock_actual, :activo, :fecha_creacion)";
        // Preparar la consulta
        $params = [
            ':categoria_id' => $data['categoria_id'],
            ':codigo_sku' => $data['codigo_sku'],
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'],
            ':precio_venta' => $data['precio_venta'],
            ':stock_actual' => $data['stock_actual'],
            ':activo' => $data['activo'],
            ':fecha_creacion' => date('Y-m-d H:i:s')
        ];
        $preparado = self::$db->prepare($sql);
        return $preparado->execute($params);
    }
    public function buscar(){
    }
    public function ver(){
    }
    public function actualizar(){
    }
    public function eliminar(){
    }
}