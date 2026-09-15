<?php

namespace App\Helpers;

class HorarioHelper
{
    // Mapea nombres de días comunes a 1..7 (Lun=1, Dom=7)
    private static $dayMap = [
        'lun' => 1,
        'lunes' => 1,
        'mar' => 2,
        'martes' => 2,
        'mie' => 3,
        'miércoles' => 3,
        'miercoles' => 3,
        'mié' => 3,
        'mier' => 3,
        'jue' => 4,
        'jueves' => 4,
        'vie' => 5,
        'viernes' => 5,
        'sab' => 6,
        'sábado' => 6,
        'sabado' => 6,
        'dom' => 7,
        'domingo' => 7,
    ];

    public static function isJson($s): bool
    {
        if (!is_string($s) || $s === '') return false;
        json_decode($s);
        return (json_last_error() === JSON_ERROR_NONE);
    }

    // Recibe texto libre (multi-línea) y devuelve array normalizado de franjas
    public static function parseText(string $raw): array
    {
        $out = [];
        $lineas = preg_split('/\r\n|\r|\n/', trim($raw));
        foreach ($lineas as $i => $line) {
            $line = trim($line);
            if ($line === '') continue;

            // Ejemplo aceptado:
            // "Lun 08:00-10:00 | Aula: B-203 | Sec: A"
            $regex = '/^\s*([A-Za-zÁÉÍÓÚáéíóú\.]+)\s+(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})(?:\s*\|\s*Aula\s*:\s*([^|]+))?(?:\s*\|\s*Sec(?:ci[oó]n)?\s*:\s*([^|]+))?\s*$/u';

            if (!preg_match($regex, $line, $m)) {
                return ['error' => "Línea " . ($i + 1) . " con formato inválido: \"$line\""];
            }
            $diaStr = mb_strtolower(trim($m[1]));
            $diaStr = rtrim($diaStr, '.'); // “Mié.” -> “Mié”
            $diaNum = self::diaToNum($diaStr);
            if (!$diaNum) {
                return ['error' => "Línea " . ($i + 1) . ": día inválido \"$m[1]\""];
            }

            $hi = self::fixTime($m[2]);
            $hf = self::fixTime($m[3]);
            if (!self::validTime($hi) || !self::validTime($hf) || $hi >= $hf) {
                return ['error' => "Línea " . ($i + 1) . ": rango horario inválido ($hi-$hf)"];
            }

            $aula    = isset($m[4]) ? trim($m[4]) : null;
            $seccion = isset($m[5]) ? trim($m[5]) : null;

            $out[] = [
                'dia'     => $diaNum,
                'inicio'  => $hi,
                'fin'     => $hf,
                'aula'    => $aula ?: null,
                'seccion' => $seccion ?: null,
            ];
        }

        // Validación simple de solapamientos internos (mismo día)
        usort($out, function ($a, $b) {
            return [$a['dia'], $a['inicio']] <=> [$b['dia'], $b['inicio']];
        });
        for ($i = 1; $i < count($out); $i++) {
            $p = $out[$i - 1];
            $c = $out[$i];
            if ($p['dia'] === $c['dia'] && $p['fin'] > $c['inicio']) {
                return ['error' => "Solapamiento interno en día {$p['dia']} entre {$p['inicio']}-{$p['fin']} y {$c['inicio']}-{$c['fin']}"];
            }
        }

        return $out;
    }

    public static function toJson(array $slots): string
    {
        return json_encode($slots, JSON_UNESCAPED_UNICODE);
    }

    // Convierte JSON (o array) a texto estándar bonitito para mostrar
    public static function pretty($horario): string
    {
        if (is_string($horario) && self::isJson($horario)) {
            $slots = json_decode($horario, true) ?: [];
        } elseif (is_array($horario)) {
            $slots = $horario;
        } else {
            return trim((string)$horario);
        }

        $lines = [];
        foreach ($slots as $s) {
            $dia = self::numToDia($s['dia'] ?? null);
            if (!$dia) continue;
            $line = sprintf(
                '%s %s-%s',
                $dia,
                $s['inicio'] ?? '',
                $s['fin'] ?? ''
            );
            if (!empty($s['aula']))    $line .= ' | Aula: ' . $s['aula'];
            if (!empty($s['seccion'])) $line .= ' | Sección: '  . $s['seccion'];
            $lines[] = $line . '';
        }
        return implode("\n", $lines);
    }

    private static function diaToNum($s)
    {
        $s = mb_strtolower($s);
        return self::$dayMap[$s] ?? null;
    }
    private static function numToDia($n)
    {
        $map = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
        return $map[$n] ?? null;
    }
    private static function validTime($t)
    {
        return (bool)preg_match('/^\d{2}:\d{2}$/', $t);
    }
    private static function fixTime($t)
    {
        // Asegura HH:MM
        if (preg_match('/^(\d{1,2}):(\d{2})$/', $t, $m)) {
            return sprintf('%02d:%s', (int)$m[1], $m[2]);
        }
        return $t;
    }
}
