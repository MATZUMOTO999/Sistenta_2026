<?php
namespace App\Models\Admin;

use Core\Model;
use PDO;

class Ventas extends Model
{
    protected $table = 'ventas';
    protected $table_detalle = 'detalle_ventas';

    public function ver_detalle_ventas_Id_producto(int $id)
    {
        $sql = "SELECT * FROM {$this->table_detalle} WHERE producto_id = :id";
        $params = [':id' => $id];
        $preparado = self::$db->prepare($sql);
        $preparado->execute($params);
        return $preparado->fetchAll(PDO::FETCH_ASSOC);
    }
}
