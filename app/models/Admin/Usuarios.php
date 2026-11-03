<?php
namespace App\Models\Admin;

use Core\Model;
use PDO;

class Usuarios extends Model
{
    protected $table = 'usuarios';

    public function buscar()
    {
        $sql = "SELECT u.id, u.nombre, u.apellidos, u.email, u.telefono,
                       u.fecha_registro, u.activo, r.nombre AS rol
                FROM {$this->table} AS u
                JOIN roles AS r ON r.id = u.rol_id
                ORDER BY u.apellidos, u.nombre";
        $stmt = self::$db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}