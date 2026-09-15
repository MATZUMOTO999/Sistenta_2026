<?php

namespace App\Models\Admision;

use Core\Model;
use PDO;

class TiposModalidades extends Model
{
    protected $table = 'admision_tipos_modalidad';

    public function getPaginated($filters, $length, $start, $orderCol, $orderDir)
    {
        $columnas = [
            0 => 'id',
            1 => 'nombre',
        ];
        $ordenarPor = $columnas[$orderCol] ?? 'id';

        $sql = "SELECT m.*, t.nombre AS tipo_modalidad
                FROM admision_tipos_modalidad m
                JOIN admision_modalidad t ON t.id = m.id_tipo_modalidad
                ORDER BY $ordenarPor $orderDir
                LIMIT :limit OFFSET :offset";
        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':limit', (int)$length, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$start, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sqlTotal = "SELECT COUNT(*) FROM admision_tipos_modalidad m
                     ";
        $stmtTotal = self::$db->prepare($sqlTotal);
        $stmtTotal->execute();
        $total = $stmtTotal->fetchColumn();

        return ['data' => $data, 'total' => $total];
    }

    public function find($id)
    {
        $stmt = self::$db->prepare("SELECT * FROM admision_tipos_modalidad WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function guardar($data)
    {
        if (!empty($data['id'])) {
            $sql = "UPDATE admision_tipos_modalidad SET nombre=:nombre WHERE id=:id";
            $params = [
                ':nombre' => $data['nombre'],
                ':id' => $data['id'],
            ];
        } else {
            $sql = "INSERT INTO admision_tipos_modalidad (nombre) VALUES (:nombre)";
            $params = [
                ':nombre' => $data['nombre'],
            ];
        }
        $stmt = self::$db->prepare($sql);
        return $stmt->execute($params);
    }

    // Métodos para relación con módulos formativos
    public function getTiposModalidades()
    {
        $stmt = self::$db->prepare("SELECT * FROM admision_tipos_modalidad");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getModalidadesByIds($ids)
    {
        if (empty($ids)) return [];

        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $sql = "SELECT * FROM admision_tipos_modalidad WHERE id IN ($placeholders)";
        $stmt = self::$db->prepare($sql);
        $stmt->execute($ids);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
