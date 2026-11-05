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

    public function ver(int $id)
    {
        $sql = "SELECT id, nombre, apellidos, email, telefono, rol_id, activo
                FROM {$this->table}
                WHERE id = :id";
        $stmt = self::$db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function rolesDisponibles()
    {
        $stmt = self::$db->prepare("SELECT id, nombre FROM roles ORDER BY nombre");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizar(array $data)
    {
        $sql = "UPDATE {$this->table}
                SET nombre = :nombre,
                    apellidos = :apellidos,
                    email = :email,
                    telefono = :telefono,
                    rol_id = :rol_id,
                    activo = :activo
                WHERE id = :id";
        $stmt = self::$db->prepare($sql);
        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':apellidos' => $data['apellidos'],
            ':email' => $data['email'],
            ':telefono' => $data['telefono'],
            ':rol_id' => $data['rol_id'],
            ':activo' => $data['activo'],
            ':id' => $data['id']
        ]);
    }

    public function desactivar(int $id)
    {
        self::$db->beginTransaction();
        try {
            $stmt = self::$db->prepare("UPDATE {$this->table} SET activo = 0 WHERE id = :id");
            $stmt->execute([':id' => $id]);

            $stmt = self::$db->prepare("UPDATE sesiones SET activa = 0 WHERE usuario_id = :id AND activa = 1");
            $stmt->execute([':id' => $id]);

            self::$db->commit();
            return true;
        } catch (\Throwable $exception) {
            if (self::$db->inTransaction()) {
                self::$db->rollBack();
            }
            throw $exception;
        }
    }
}