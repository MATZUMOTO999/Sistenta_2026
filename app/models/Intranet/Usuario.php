<?php

namespace App\Models\Intranet;

use Core\Model;
use PDO;

class Usuario extends Model
{
    //protected $table = 'sigi_usuarios';
    protected $table = 'sigi_usuarios';

    // Obtener todos los docentes (roles distintos de ESTUDIANTE y EXTERNO)
    public function buscar()
    {
        $sql = "SELECT * FROM sigi_datos_sistema WHERE id = 1 ";
        $stmt = self::$db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function find($id)
    {
        $sql = "SELECT 
                u.*, 
                r.nombre AS nombre_rol, 
                s.nombre AS nombre_sede,
                p.nombre AS nombre_programa
            FROM sigi_usuarios u
            LEFT JOIN sigi_roles r ON u.id_rol = r.id
            LEFT JOIN sigi_sedes s ON u.id_sede = s.id
            LEFT JOIN sigi_programa_estudios p ON u.id_programa_estudios = p.id
            WHERE u.id = :id";
        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $apellidos_nombres = explode('_', trim($data['apellidos_nombres']));
        $data['ApellidoPaterno'] = $apellidos_nombres[0];
        $data['ApellidoMaterno'] = $apellidos_nombres[1];
        $data['Nombres'] = $apellidos_nombres[2];
        $data['apellidos_nombres'] = $apellidos_nombres[0] . ' ' . $apellidos_nombres[1] . ' ' . $apellidos_nombres[2];
        return $data;
    }
    public function getCantidadSemanas()
    {
        $stmt = self::$db->query("SELECT cant_semanas FROM sigi_datos_sistema LIMIT 1");
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ? (int)$res['cant_semanas'] : 16; // Valor por defecto si no existe
    }
    public function getNro_PlantillaSilabos()
    {
        $stmt = self::$db->query("SELECT plantilla_silabo FROM sigi_datos_sistema LIMIT 1");
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ? (int)$res['plantilla_silabo'] : 1; // Valor por defecto si no existe
    }
    public function getNro_PlantillaSesion()
    {
        $stmt = self::$db->query("SELECT plantilla_sesion FROM sigi_datos_sistema LIMIT 1");
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ? (int)$res['plantilla_sesion'] : 1; // Valor por defecto si no existe
    }
    public function getNotaSiInasistencia()
    {
        $stmt = self::$db->query("SELECT nota_inasistencia FROM sigi_datos_sistema LIMIT 1");
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res; // Valor por defecto si no existe
    }
    public function getDuracionSession()
    {
        $stmt = self::$db->query("SELECT duracion_sesion FROM sigi_datos_sistema LIMIT 1");
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res; // Valor por defecto si no existe
    }

    // Helpers
    public static function encodePermisos(array $ids): string
    {
        // JSON compacto (p.ej. "[2,3,7]") — cabe en VARCHAR(50) para pocos sistemas
        return json_encode(array_values(array_map('intval', $ids)), JSON_UNESCAPED_UNICODE);
    }

    public static function decodePermisos(?string $raw): array
    {
        if (!$raw) return [];
        $raw = trim($raw);
        // Soporte retro: "2,3,7"
        if ($raw && $raw[0] !== '[') {
            return array_values(array_filter(array_map('intval', explode(',', $raw))));
        }
        $arr = json_decode($raw, true);
        return is_array($arr) ? array_values(array_map('intval', $arr)) : [];
    }

    public static function sigi_email_template(array $data = []): string
    {
        $brand     = $data['brand']     ?? 'SIGI Notificaciones';
        $logo      = $data['logo'];
        $primary   = $data['primary']; // azul
        $title     = $data['title'];
        $intro     = $data['intro']     ?? 'Hola,';
        $bodyHtml  = $data['body_html'];
        $ctaText   = $data['cta_text']  ?? null;      // e.g. 'Restablecer contraseña'
        $ctaUrl    = $data['cta_url']   ?? null;      // e.g. 'https://...'
        $note      = $data['note']      ?? '';        // texto secundario opcional
        $address   = $data['address'];
        $year      = $data['year']      ?? date('Y');
        $preheader = $data['preheader'] ?? $title;

        // Seguridad básica para evitar HTML inesperado en variables de texto
        $esc = fn($t) => htmlspecialchars($t ?? '', ENT_QUOTES, 'UTF-8');

        $btnHtml = '';
        if ($ctaText && $ctaUrl) {
            $btnHtml = '
        <!-- Botón (bulletproof) -->
        <table role="presentation" cellspacing="0" cellpadding="0">
          <tr>
            <td align="center" bgcolor="' . $primary . '" style="border-radius:8px;">
              <a href="' . $esc($ctaUrl) . '" target="_blank"
                 style="font-family:Arial,Helvetica,sans-serif;font-size:16px;line-height:20px;
                        color:#ffffff;text-decoration:none;padding:12px 22px;display:inline-block;">
                ' . $esc($ctaText) . '
              </a>
            </td>
          </tr>
        </table>';
        }

        return <<<HTML
        <!doctype html>
        <html lang="es" xmlns="http://www.w3.org/1999/xhtml">
        <head>
        <meta charset="utf-8" />
        <meta name="x-apple-disable-message-reformatting" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>{$esc($title)}</title>
        <style>
            /* Estilos básicos y responsive */
            body { margin:0; padding:0; background:#f4f5f7; }
            img { border:0; line-height:100%; vertical-align:middle; }
            @media (max-width: 600px) {
            .container { width:100% !important; }
            .p-24 { padding:16px !important; }
            h1 { font-size:22px !important; line-height:28px !important; }
            }
            /* Oculta el preheader en la vista */
            .preheader { display:none!important; visibility:hidden; opacity:0; color:transparent; height:0; width:0; overflow:hidden; mso-hide:all; }
        </style>
        </head>
        <body>
        <div class="preheader">{$esc($preheader)}</div>

        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background:#f4f5f7;">
            <tr>
            <td align="center" style="padding:24px;">
                <table role="presentation" class="container" cellspacing="0" cellpadding="0" border="0" width="600" style="width:600px;max-width:600px;background:#ffffff;border-radius:14px;overflow:hidden;">
                <!-- Header -->
                <tr>
                    <td align="center" style="padding:20px 24px;border-bottom:1px solid #e6e8eb;">
                    <img src="{$esc($logo)}" alt="{$esc($brand)}" width="120" style="display:block;max-width:120px;height:auto;" />
                    <div style="font: 600 14px/1 Arial,Helvetica,sans-serif;color:#6b7280;margin-top:8px;">{$esc($brand)}</div>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td class="p-24" style="padding:24px;">
                    <h1 style="margin:0 0 12px 0;font:700 24px/30px Arial,Helvetica,sans-serif;color:#111827;">{$esc($title)}</h1>
                    <p style="margin:0 0 12px 0;font:400 14px/22px Arial,Helvetica,sans-serif;color:#111827;">{$esc($intro)}</p>
                    <div style="margin:0 0 20px 0;font:400 14px/22px Arial,Helvetica,sans-serif;color:#374151;">
                        {$bodyHtml}
                    </div>

                    {$btnHtml}

                    <!-- Nota secundaria -->
                    <div style="margin-top:20px;font:400 12px/20px Arial,Helvetica,sans-serif;color:#6b7280;">
                        {$note}
                    </div>

                    <!-- Divider -->
                    <div style="height:1px;background:#e6e8eb;margin:24px 0;"></div>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td align="center" style="padding:16px 24px;background:#fafafa;border-top:1px solid #e6e8eb;">
                    <div style="font:400 11px/18px Arial,Helvetica,sans-serif;color:#9ca3af;">
                        © {$esc($year)} {$esc($brand)} • {$esc($address)}<br/>
                        Este es un mensaje automático. Por favor, no respondas a esta dirección.
                    </div>
                    </td>
                </tr>
                </table>

                <!-- Fallback texto legal -->
                <div style="padding:12px 0;font:400 11px/18px Arial,Helvetica,sans-serif;color:#9ca3af;">
                Si no ves correctamente este correo, intenta abrirlo en tu navegador.
                </div>
            </td>
            </tr>
        </table>
        </body>
        </html>
        HTML;
    }

    public function actualizarTokenMicrosoft($token, $expire_in)
    {
        $sql = "UPDATE sigi_datos_sistema SET token_dinamico_microsoft = :token, token_microsoft_expire = :expire_in";
        $stmt = self::$db->prepare($sql);
        $data['token'] = $token;
        $data['expire_in'] = $expire_in;
        return $stmt->execute($data);
    }
}
