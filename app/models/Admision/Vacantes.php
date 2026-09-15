<?php

namespace App\Models\Admision;

use Core\Model;
use PDO;

class Vacantes extends Model
{
    protected $table = 'admision_vacantes';

    public function getPaginated($filters, $length, $start, $orderCol, $orderDir)
    {
        $columnas = [
            0 => 'id',
            1 => 'id_proceso_admision',
            2 => 'id_modalidad_admision',
            3 => 'id_tipo_modalidad',
            4 => 'id_programa_estudio',
            5 => 'cantidad',
        ];
        $ordenarPor = $columnas[$orderCol] ?? 'id_proceso_admision';

        $where = [];
        $params = [];

        if (!empty($filters['id_sede'])) {
            $where[] = "s.id = :id_sede";
            $params[':id_sede'] = $filters['id_sede'];
        }
        if (!empty($filters['id_periodo'])) {
            $where[] = "pa.id = :id_periodo";
            $params[':id_periodo'] = $filters['id_periodo'];
        }

        $sqlWhere = $where ? ("WHERE " . implode(" AND ", $where)) : "";

        $sql = "SELECT v.*, pa.nombre AS periodo_nombre, s.nombre AS sede_nombre, pad.nombre AS proceso_admision_nombre, t.nombre AS tipo_modalidad_nombre, pe.nombre AS programa_estudio_nombre, m.nombre AS modalidad_nombre
                FROM admision_vacantes v
                JOIN admision_proceso_admision pad ON pad.id = v.id_proceso_admision
                JOIN admision_modalidad m ON m.id = v.id_modalidad_admision
                JOIN admision_tipos_modalidad t ON t.id = m.id_tipo_modalidad
                JOIN sigi_programa_estudios pe ON pe.id = v.id_programa_estudio
                JOIN sigi_periodo_academico pa ON pa.id = pad.id_periodo
                JOIN sigi_sedes s ON s.id = pad.id_sede
                $sqlWhere
                ORDER BY $ordenarPor $orderDir
                LIMIT :limit OFFSET :offset";
        $stmt = self::$db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, PDO::PARAM_INT);
        }
        $stmt->bindValue(':limit', (int)$length, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$start, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sqlTotal = "SELECT COUNT(*) FROM admision_vacantes c
                     JOIN admision_proceso_admision pad ON pad.id = c.id_proceso_admision
                     JOIN admision_modalidad m ON m.id = c.id_modalidad_admision
                     JOIN admision_tipos_modalidad t ON t.id = m.id_tipo_modalidad
                     JOIN sigi_programa_estudios pe ON pe.id = c.id_programa_estudio
                     JOIN sigi_periodo_academico pa ON pa.id = pad.id_periodo
                     JOIN sigi_sedes s ON s.id = pad.id_sede
                     $sqlWhere";
        $stmtTotal = self::$db->prepare($sqlTotal);
        foreach ($params as $k => $v) {
            $stmtTotal->bindValue($k, $v, PDO::PARAM_INT);
        }
        $stmtTotal->execute();
        $total = $stmtTotal->fetchColumn();

        return ['data' => $data, 'total' => $total];
    }

    public function find($id)
    {
        $stmt = self::$db->prepare("SELECT v.*, pa.nombre AS periodo_nombre, s.nombre AS sede_nombre, pad.nombre AS proceso_admision_nombre, t.nombre AS tipo_modalidad_nombre, pe.nombre AS programa_estudio_nombre, m.nombre AS modalidad_nombre,m.id_tipo_modalidad AS id_tipo_modalidad
                FROM admision_vacantes v
                JOIN admision_proceso_admision pad ON pad.id = v.id_proceso_admision
                JOIN admision_modalidad m ON m.id = v.id_modalidad_admision
                JOIN admision_tipos_modalidad t ON t.id = m.id_tipo_modalidad
                JOIN sigi_programa_estudios pe ON pe.id = v.id_programa_estudio
                JOIN sigi_periodo_academico pa ON pa.id = pad.id_periodo
                JOIN sigi_sedes s ON s.id = pad.id_sede WHERE v.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function guardar($data)
    {
        if (!empty($data['id'])) {
            $sql = "UPDATE admision_vacantes SET id_proceso_admision=:id_proceso_admision, id_modalidad_admision=:id_modalidad_admision, id_programa_estudio=:id_programa_estudio, cantidad=:cantidad WHERE id=:id";
            $params = [
                ':id_proceso_admision' => $data['id_proceso_admision'],
                ':id_modalidad_admision' => $data['id_modalidad_admision'],
                ':id_programa_estudio' => $data['id_programa_estudio'],
                ':cantidad' => $data['cantidad'],
                ':id' => $data['id'],
            ];
        } else {
            $sql = "INSERT INTO admision_vacantes (id_proceso_admision, id_modalidad_admision, id_programa_estudio, cantidad) VALUES (:id_proceso_admision, :id_modalidad_admision, :id_programa_estudio, :cantidad)";
            $params = [
                ':id_proceso_admision' => $data['id_proceso_admision'],
                ':id_modalidad_admision' => $data['id_modalidad_admision'],
                ':id_programa_estudio' => $data['id_programa_estudio'],
                ':cantidad' => $data['cantidad'],
            ];
        }
        $stmt = self::$db->prepare($sql);
        return $stmt->execute($params);
    }

    public function checkDuplicate($id_proceso, $id_modalidad_admision, $id_programa_estudio, $exclude_id = null)
    {
        $sql = "SELECT COUNT(*) FROM admision_vacantes WHERE id_proceso_admision = ? AND id_modalidad_admision = ? AND id_programa_estudio = ?";
        $params = [$id_proceso, $id_modalidad_admision, $id_programa_estudio];

        if ($exclude_id) {
            $sql .= " AND id != ?";
            $params[] = $exclude_id;
        }

        $stmt = self::$db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }
}
