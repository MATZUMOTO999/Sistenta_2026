<?php
namespace App\Models\Admin;

use Core\Model;
use PDO;

class Categorias extends Model{
    protected $table = 'categorias';

    public function guardar($data){
        $sql = "INSERT INTO {$this->table} (nombre, descripcion) VALUES (:nombre, :descripcion)";
        $params = [
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion']
        ];
        $stmt = self::$db->prepare($sql);
        return $stmt->execute($params);
    }

    public function ver(int $id)
    {
        $stmt = self::$db->prepare("SELECT id, nombre, descripcion, activo FROM {$this->table} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar(array $data)
    {
        $sql = "UPDATE {$this->table}
                SET nombre = :nombre, descripcion = :descripcion, activo = :activo
                WHERE id = :id";
        $stmt = self::$db->prepare($sql);
        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'],
            ':activo' => $data['activo'],
            ':id' => $data['id']
        ]);
    }

    public function tieneProductos(int $id)
    {
        $stmt = self::$db->prepare("SELECT 1 FROM productos WHERE categoria_id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return (bool) $stmt->fetchColumn();
    }

    public function eliminar(int $id)
    {
        $sql = "DELETE FROM {$this->table}
                WHERE id = :id
                  AND NOT EXISTS (SELECT 1 FROM productos WHERE categoria_id = :producto_categoria_id)";
        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':producto_categoria_id' => $id
        ]);
        return $stmt->rowCount() > 0;
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
