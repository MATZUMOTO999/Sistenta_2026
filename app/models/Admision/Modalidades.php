<?php

namespace App\Models\Admision;

use Core\Model;
use PDO;

class Modalidades extends Model
{
    protected $table = 'admision_modalidad';

    public function getPaginated($filters, $length, $start, $orderCol, $orderDir)
    {
        $columnas = [
            0 => 'id',
            1 => 'id_tipo_modalidad',
            2 => 'nombre'
        ];
        $ordenarPor = $columnas[$orderCol] ?? 'id';

        $sql = "SELECT m.*, t.nombre AS tipo_modalidad
                FROM admision_modalidad m
                JOIN admision_tipos_modalidad t ON t.id = m.id_tipo_modalidad
                ORDER BY $ordenarPor $orderDir
                LIMIT :limit OFFSET :offset";
        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':limit', (int)$length, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$start, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sqlTotal = "SELECT COUNT(*) FROM admision_modalidad m
                     JOIN admision_tipos_modalidad t ON t.id = m.id_tipo_modalidad
                     ";
        $stmtTotal = self::$db->prepare($sqlTotal);
        $stmtTotal->execute();
        $total = $stmtTotal->fetchColumn();

        return ['data' => $data, 'total' => $total];
    }

    public function find($id)
    {
        $stmt = self::$db->prepare("SELECT * FROM admision_modalidad WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function guardar($data)
    {
        if (!empty($data['id'])) {
            $sql = "UPDATE admision_modalidad SET id_tipo_modalidad=:id_tipo_modalidad, nombre=:nombre WHERE id=:id";
            $params = [
                ':id_tipo_modalidad' => $data['id_tipo_modalidad'],
                ':nombre' => $data['nombre'],
                ':id' => $data['id'],
            ];
        } else {
            $sql = "INSERT INTO admision_modalidad (id_tipo_modalidad, nombre) VALUES (:id_tipo_modalidad, :nombre)";
            $params = [
                ':id_tipo_modalidad' => $data['id_tipo_modalidad'],
                ':nombre' => $data['nombre'],
            ];
        }
        $stmt = self::$db->prepare($sql);
        return $stmt->execute($params);
    }
    public function getModalidades()
    {
        $stmt = self::$db->prepare("SELECT * FROM admision_modalidad");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getModalidadesByTipoModalidad($id_tipo_modalidad)
    {
        $stmt = self::$db->prepare("SELECT * FROM admision_modalidad WHERE id_tipo_modalidad = ?");
        $stmt->execute([$id_tipo_modalidad]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
