<?php

namespace App\Models\Admision;

use Core\Model;
use PDO;

class ProcesosAdmision extends Model
{
    protected $table = 'admision_proceso_admision';

    public function getPaginated($filters, $length, $start, $orderCol, $orderDir)
    {
        $columnas = [
            0 => 'id',
            1 => 'nombre',
            2 => 'id_periodo',
            3 => 'id_sede',
            4 => 'fecha_inicio',
            5 => 'fecha_fin'
        ];
        $ordenarPor = $columnas[$orderCol] ?? 'nombre';

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

        $sql = "SELECT c.*, pa.nombre AS periodo_nombre, s.nombre AS sede_nombre
                FROM admision_proceso_admision c
                JOIN sigi_periodo_academico pa ON pa.id = c.id_periodo
                JOIN sigi_sedes s ON s.id = c.id_sede
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

        $sqlTotal = "SELECT COUNT(*) FROM admision_proceso_admision c
                     JOIN sigi_periodo_academico pa ON pa.id = c.id_periodo
                     JOIN sigi_sedes s ON s.id = c.id_sede
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
        $stmt = self::$db->prepare("SELECT * FROM admision_proceso_admision WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function guardar($data)
    {
        if (!empty($data['id'])) {
            $sql = "UPDATE admision_proceso_admision SET nombre=:nombre, id_periodo=:id_periodo, id_sede=:id_sede, fecha_inicio=:fecha_inicio, fecha_fin=:fecha_fin, tipos_modalidades_ingreso=:tipos_modalidades_ingreso WHERE id=:id";
            $params = [
                ':nombre' => $data['nombre'],
                ':id_periodo' => $data['id_periodo'],
                ':id_sede' => $data['id_sede'],
                ':fecha_inicio' => $data['fecha_inicio'],
                ':fecha_fin' => $data['fecha_fin'],
                ':tipos_modalidades_ingreso' => $data['tipos_modalidades_ingreso'],
                ':id' => $data['id'],
            ];
        } else {
            $sql = "INSERT INTO admision_proceso_admision (nombre, id_periodo, id_sede, fecha_inicio, fecha_fin, tipos_modalidades_ingreso) VALUES (:nombre, :id_periodo, :id_sede, :fecha_inicio, :fecha_fin, :tipos_modalidades_ingreso)";
            $params = [
                ':nombre' => $data['nombre'],
                ':id_periodo' => $data['id_periodo'],
                ':id_sede' => $data['id_sede'],
                ':fecha_inicio' => $data['fecha_inicio'],
                ':fecha_fin' => $data['fecha_fin'],
                ':tipos_modalidades_ingreso' => $data['tipos_modalidades_ingreso'],
            ];
        }
        $stmt = self::$db->prepare($sql);
        return $stmt->execute($params);
    }
    public function getProcesosAdmisionSedePeriodo($sede, $periodo)
    {
        $stmt = self::$db->prepare("SELECT * FROM admision_proceso_admision WHERE id_sede = :id_sede AND id_periodo = :id_periodo");
        $params = [
            ':id_sede' => $sede,
            ':id_periodo' => $periodo,
        ];
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
