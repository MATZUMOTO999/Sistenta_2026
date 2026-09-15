<?php

namespace App\Models\Admision;

use Core\Model;
use PDO;

class Inscripciones extends Model
{
    protected $table = 'admision_inscripcion';

    public function getPaginated($filters, $length, $start, $orderCol, $orderDir)
    {
        $columnas = [
            0 => 'id',
            1 => 'u.tipo_doc',
            2 => 'u.dni',
            3 => 'u.apellidos_nombres',
            4 => 'pad.nombre',
            5 => 't.nombre',
            6 => 'm.nombre',
            7 => 'pe.nombre',
            8 => 'i.fecha_inscripcion',
        ];
        $ordenarPor = $columnas[$orderCol] ?? 'id';

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

        $sql = "SELECT i.*, u.tipo_doc, u.dni, u.apellidos_nombres, pa.nombre AS periodo_nombre, s.nombre AS sede_nombre, pad.nombre AS proceso_admision_nombre, t.nombre AS tipo_modalidad_nombre, pe.nombre AS programa_estudio_nombre, m.nombre AS modalidad_nombre
                FROM admision_inscripcion i
                JOIN sigi_usuarios u ON u.id = i.id_usuario_sigi
                JOIN admision_proceso_admision pad ON pad.id = i.id_proceso_admision
                JOIN admision_modalidad m ON m.id = i.id_modalidad_admision
                JOIN admision_tipos_modalidad t ON t.id = m.id_tipo_modalidad
                JOIN sigi_programa_estudios pe ON pe.id = i.id_programa_estudio
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
        foreach ($data as $key => $value) {
            $apellidos_nombres = explode('_', trim($value['apellidos_nombres']));
            $data[$key]['apellidos_nombres'] = $apellidos_nombres[0] . ' ' . $apellidos_nombres[1] . ' ' . $apellidos_nombres[2];
        }

        $sqlTotal = "SELECT COUNT(*) FROM admision_inscripcion i
                     JOIN sigi_usuarios u ON u.id = i.id_usuario_sigi
                     JOIN admision_proceso_admision pad ON pad.id = i.id_proceso_admision
                     JOIN admision_modalidad m ON m.id = i.id_modalidad_admision
                     JOIN admision_tipos_modalidad t ON t.id = m.id_tipo_modalidad
                     JOIN sigi_programa_estudios pe ON pe.id = i.id_programa_estudio
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
        $stmt = self::$db->prepare("SELECT i.*, u.tipo_doc, u.dni AS usuario_dni, u.apellidos_nombres AS usuario_nombre, u.genero AS genero, u.fecha_nacimiento AS fecha_nacimiento, u.direccion AS direccion, u.telefono AS telefono, u.correo AS correo, u.distrito_nacimiento AS distrito_nacimiento, pa.nombre AS periodo_nombre, s.nombre AS sede_nombre, pad.nombre AS proceso_admision_nombre, t.nombre AS tipo_modalidad_nombre, pe.nombre AS programa_estudio_nombre, m.nombre AS modalidad_nombre,m.id_tipo_modalidad AS id_tipo_modalidad, t.nombre AS tipo_modalidad_nombre
                FROM admision_inscripcion i
                LEFT JOIN sigi_usuarios u ON u.id = i.id_usuario_sigi
                JOIN admision_proceso_admision pad ON pad.id = i.id_proceso_admision
                JOIN admision_modalidad m ON m.id = i.id_modalidad_admision
                JOIN admision_tipos_modalidad t ON t.id = m.id_tipo_modalidad
                JOIN sigi_programa_estudios pe ON pe.id = i.id_programa_estudio
                JOIN sigi_periodo_academico pa ON pa.id = pad.id_periodo
                JOIN sigi_sedes s ON s.id = pad.id_sede WHERE i.id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $apellidos_nombres = explode('_', trim($data['usuario_nombre']));
        $data['apellido_paterno'] = $apellidos_nombres[0];
        $data['apellido_materno'] = $apellidos_nombres[1];
        $data['nombres'] = $apellidos_nombres[2];
        $data['usuario_nombre'] = $apellidos_nombres[0] . ' ' . $apellidos_nombres[1] . ' ' . $apellidos_nombres[2];
        return $data;
    }


    public function guardar($data)
    {
        if (!empty($data['id'])) {
            $sql = "UPDATE admision_inscripcion SET id_usuario_sigi=:id_usuario_sigi, id_proceso_admision=:id_proceso_admision, id_modalidad_admision=:id_modalidad_admision, id_programa_estudio=:id_programa_estudio, colegio_procedencia=:colegio_procedencia, anio_egreso_colegio=:anio_egreso_colegio, foto=:foto, calificacion=:calificacion, estado=:estado, requisitos=:requisitos, update_at=NOW() WHERE id=:id";
            $params = [
                ':id_usuario_sigi' => $data['id_usuario_sigi'],
                ':id_proceso_admision' => $data['id_proceso_admision'],
                ':id_modalidad_admision' => $data['id_modalidad_admision'],
                ':id_programa_estudio' => $data['id_programa_estudio'],
                ':colegio_procedencia' => $data['colegio_procedencia'],
                ':anio_egreso_colegio' => $data['anio_egreso_colegio'],
                ':foto' => $data['foto'],
                ':calificacion' => $data['calificacion'],
                ':estado' => $data['estado'],
                ':requisitos' => $data['requisitos'] ?? '',
                ':id' => $data['id'],
            ];
        } else {
            $sql = "INSERT INTO admision_inscripcion (id_usuario_sigi, id_proceso_admision, id_modalidad_admision, id_programa_estudio, colegio_procedencia, anio_egreso_colegio, foto, calificacion, estado, requisitos, codigo) VALUES (:id_usuario_sigi, :id_proceso_admision, :id_modalidad_admision, :id_programa_estudio, :colegio_procedencia, :anio_egreso_colegio, :foto, :calificacion, :estado, :requisitos, :codigo)";
            $params = [
                ':id_usuario_sigi' => $data['id_usuario_sigi'],
                ':id_proceso_admision' => $data['id_proceso_admision'],
                ':id_modalidad_admision' => $data['id_modalidad_admision'],
                ':id_programa_estudio' => $data['id_programa_estudio'],
                ':colegio_procedencia' => $data['colegio_procedencia'],
                ':anio_egreso_colegio' => $data['anio_egreso_colegio'],
                ':foto' => $data['foto'],
                ':calificacion' => $data['calificacion'],
                ':estado' => $data['estado'],
                ':requisitos' => $data['requisitos'] ?? '',
                ':codigo' => $data['codigo'] ?? null,
            ];
        }
        $stmt = self::$db->prepare($sql);
        return $stmt->execute($params);
    }

    public function checkDuplicate($id_usuario, $id_proceso, $id_modalidad_admision, $id_programa_estudio, $exclude_id = null)
    {
        $sql = "SELECT COUNT(*) FROM admision_inscripcion WHERE id_usuario_sigi = ? AND id_proceso_admision = ? AND id_modalidad_admision = ? AND id_programa_estudio = ?";
        $params = [$id_usuario, $id_proceso, $id_modalidad_admision, $id_programa_estudio];

        if ($exclude_id) {
            $sql .= " AND id != ?";
            $params[] = $exclude_id;
        }

        $stmt = self::$db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }
    function generarCodigoPostulante($dni)
    {
        // 1. Obtenemos el timestamp actual (segundos desde 1970)
        $timestamp = time();

        // 2. Convertimos el timestamp a Base36 (0-9, a-z) para acortarlo
        $tiempoCodificado = strtoupper(base_convert($timestamp, 10, 36));

        // 3. Tomamos los últimos 4 dígitos del DNI (o el DNI entero si prefieres)
        $dniCorto = substr($dni, -3);

        // 4. (Opcional) Agregamos una letra aleatoria para evitar duplicados en el mismo segundo exacto
        $random = strtoupper(chr(rand(65, 90))); // Letra A-Z

        // Resultado: "ABCD" (dni) + "-" + "XYZ12" (tiempo) + "R" (random)
        $resultado = $dniCorto . $tiempoCodificado . $random;

        //validar que es unico
        $stmt = self::$db->prepare("SELECT COUNT(*) FROM admision_inscripcion WHERE codigo = ?");
        $stmt->execute([$resultado]);
        if ($stmt->fetchColumn() > 0) {
            return $this->generarCodigoPostulante($dni);
        }
        return $resultado;
    }
}
