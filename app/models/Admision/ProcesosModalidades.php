<?php

namespace App\Models\Admision;

use Core\Model;
use PDO;

class ProcesosModalidades extends Model
{
    protected $table = 'admision_procesos_modalidades';

    public function getPaginated($filters, $length, $start, $orderCol, $orderDir)
    {
        $columnas = [
            0 => 'id',
            1 => 'id_proceso_admision',
            2 => 'id_periodo',
            3 => 'fecha_inicio',
            4 => 'fecha_fin',
            5 => 'fecha_cierre_inscripcion',
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

        $sql = "SELECT c.*, pa.nombre AS periodo_nombre, s.nombre AS sede_nombre, pad.nombre AS proceso_admision_nombre, t.nombre AS tipo_modalidad_nombre
                FROM admision_procesos_modalidades c
                JOIN admision_proceso_admision pad ON pad.id = c.id_proceso_admision
                JOIN admision_tipos_modalidad t ON t.id = c.id_tipo_modalidad
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

        $sqlTotal = "SELECT COUNT(*) FROM admision_procesos_modalidades c
                     JOIN admision_proceso_admision pad ON pad.id = c.id_proceso_admision
                     JOIN admision_tipos_modalidad t ON t.id = c.id_tipo_modalidad
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
        $stmt = self::$db->prepare("SELECT * FROM admision_procesos_modalidades WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function guardar($data)
    {
        if (!empty($data['id'])) {
            $sql = "UPDATE admision_procesos_modalidades SET id_proceso_admision=:id_proceso_admision, id_tipo_modalidad=:id_tipo_modalidad, fecha_inicio=:fecha_inicio, fecha_fin=:fecha_fin, fecha_cierre_inscripcion=:fecha_cierre_inscripcion WHERE id=:id";
            $params = [
                ':id_proceso_admision' => $data['id_proceso_admision'],
                ':id_tipo_modalidad' => $data['id_tipo_modalidad'],
                ':fecha_inicio' => $data['fecha_inicio'],
                ':fecha_fin' => $data['fecha_fin'],
                ':fecha_cierre_inscripcion' => $data['fecha_cierre_inscripcion'],
                ':id' => $data['id'],
            ];
        } else {
            $sql = "INSERT INTO admision_procesos_modalidades (id_proceso_admision, id_tipo_modalidad, fecha_inicio, fecha_fin, fecha_cierre_inscripcion) VALUES (:id_proceso_admision, :id_tipo_modalidad, :fecha_inicio, :fecha_fin, :fecha_cierre_inscripcion)";
            $params = [
                ':id_proceso_admision' => $data['id_proceso_admision'],
                ':id_tipo_modalidad' => $data['id_tipo_modalidad'],
                ':fecha_inicio' => $data['fecha_inicio'],
                ':fecha_fin' => $data['fecha_fin'],
                ':fecha_cierre_inscripcion' => $data['fecha_cierre_inscripcion'],
            ];
        }
        $stmt = self::$db->prepare($sql);
        return $stmt->execute($params);
    }

    public function checkDuplicate($id_proceso, $id_modalidad, $exclude_id = null)
    {
        $sql = "SELECT COUNT(*) FROM admision_procesos_modalidades WHERE id_proceso_admision = ? AND id_tipo_modalidad = ?";
        $params = [$id_proceso, $id_modalidad];

        if ($exclude_id) {
            $sql .= " AND id != ?";
            $params[] = $exclude_id;
        }

        $stmt = self::$db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }
}
