<?php
namespace App\Models\Admin;

use Core\Model;
use PDO;

class Categorias extends Model{
    protected $table = 'categorias';

    public function guardar($data){
        if(!empty($data['id'])){

        }else{
            $sql = "INSERT INTO {$this->table} (nombre, descripcion) VALUES (:nombre, :descripcion)";
            $params = [
                ':nombre' => $data['nombre'],
                ':descripcion' => $data['descripcion']
            ];
        }
        $stmt = self::$db->prepare($sql);
        return $stmt->execute($params);
    }

    public function obtenerCategorias($start, $length, $orderColumnIndex, $orderDir, $filters){
        $columns = ['id', 'nombre', 'descripcion', 'activo'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'nombre';
        $where = [];
        $params = [];
        $sql = "SELECT id, nombre, descripcion, CASE WHEN activo = 1 THEN 'Activo' ELSE 'Inactivo' END AS estado_text FROM {$this->table} ORDER BY {$orderColumn} {$orderDir} LIMIT :start, :length";
        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
        $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ['data' => $data, 'total' => count($data)];
    }

    //categoria select para el formulario de productos
    public function Categorias_select(){
        // obtiene todas las categorias activas para el select de productos, ordenadas alfabéticamente
        $sql = "SELECT id, nombre FROM {$this->table} WHERE activo = 1 ORDER BY nombre ASC";
        $stmt = self::$db->prepare($sql);
        $stmt->execute();
        // muestra los resultados como un array asociativo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
