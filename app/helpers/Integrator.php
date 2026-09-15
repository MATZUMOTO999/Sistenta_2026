<?php

namespace App\Helpers;

require_once __DIR__ . '/../models/Sigi/DatosSistema.php';
require_once __DIR__ . '/../models/Sigi/Docente.php';
require_once __DIR__ . '/../models/Academico/ProgramacionUnidadDidactica.php';

use App\Models\Sigi\DatosSistema;
use App\Models\Sigi\Docente;
use App\Models\Academico\ProgramacionUnidadDidactica;

class Integrator
{
    private $token;
    private $objDatosSistema;
    private $objDocente;
    private $objProgramacionUnidadDidactica;

    public function __construct()
    {
        // Asumiendo que definiste las constantes en config.php
        $this->objDatosSistema = new DatosSistema();
        $this->objDocente = new Docente();
        $this->objProgramacionUnidadDidactica = new ProgramacionUnidadDidactica();
    }
    public function sincronizarUsuarios(array $data)
    {
        if (INTEGRACIONES_SYNC_ACTIVE) {
            $ds = $this->objDatosSistema->buscar();
            $apiKey = $ds['token_sistema'];
            $url = API_BASE_URL . "/api/integracion/syncUserIntegraciones";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, INTEGRACIONES_SSL_VERIFY);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, INTEGRACIONES_SSL_VERIFY ? 2 : 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'X-Api-Key: ' . $apiKey
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            $response = curl_exec($ch);
            curl_close($ch);
            return json_decode($response, true);
        } else {
            return ['success' => false, 'details' => 'Integraciones no activadas'];
        }
    }
    public function loginMoodle($idUsuarioSigi, $a, $id)
    {
        if (INTEGRACIONES_SYNC_ACTIVE) {
            $ds = $this->objDatosSistema->buscar();
            $apiKey = $ds['token_sistema'];
            $url = API_BASE_URL . "/api/integracion/loginMoodle";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, INTEGRACIONES_SSL_VERIFY);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, INTEGRACIONES_SSL_VERIFY ? 2 : 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'X-Api-Key: ' . $apiKey
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'idUsuarioSigi' => $idUsuarioSigi,
                'a' => $a,
                'id' => $id
            ]));
            $response = curl_exec($ch);
            curl_close($ch);
            return json_decode($response, true);
        } else {
            return ['success' => false, 'details' => 'Integraciones no activadas'];
        }
    }
    public function sincronizarUsuariosMasivos($usuarios)
    {
        // Verificar constante de activación
        if (!defined('INTEGRACIONES_SYNC_ACTIVE') || !INTEGRACIONES_SYNC_ACTIVE) {
            return ['success' => false, 'errores' => ['Integraciones no activadas en config']];
        }

        // Obtener Token de la BD (Tu lógica original)
        // Asumo que $this->objDatosSistema ya está instanciado en el __construct del Helper
        // Si no, instáncialo aquí: $objDatosSistema = new \App\Models\Sigi\DatosSistema();
        $ds = $this->objDatosSistema->buscar();
        $apiKey = $ds['token_sistema'];

        $url = API_BASE_URL . "/api/integracion/sincronizarUsuariosMasivos";

        // 1. Dividir en lotes de 20 para no saturar tu API Master
        $loteSize = 20;
        $chunks = array_chunk($usuarios, $loteSize);

        $totalEnviados = 0;
        $errores = [];
        $respuestasAPI = [];

        foreach ($chunks as $lote) {

            // --- TU LÓGICA CURL ORIGINAL ---
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');

            // OJO: En producción idealmente deberías verificar SSL, pero lo dejamos false como pediste
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, INTEGRACIONES_SSL_VERIFY);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, INTEGRACIONES_SSL_VERIFY ? 2 : 0);

            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'X-Api-Key: ' . $apiKey
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($lote));

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                $errores[] = "Error cURL: " . curl_error($ch);
            }
            curl_close($ch);
            // -------------------------------

            // Procesar respuesta parcial
            $jsonResp = json_decode($response, true);

            if ($httpCode === 200 && isset($jsonResp['success']) && $jsonResp['success']) {
                $totalEnviados += count($lote);
            } else {
                $msg = $jsonResp['details'] ?? 'Error desconocido en API Master';
                $errores[] = "Lote fallido ($httpCode): " . $msg;
            }

            $respuestasAPI[] = $jsonResp;
        }

        // Retornar resumen unificado
        return [
            'success' => count($errores) === 0,
            'enviados' => count($usuarios),
            'procesados_api' => $totalEnviados,
            'errores' => $errores,
            'detalles_api' => $respuestasAPI // Para debug si lo necesitas
        ];
    }


    //============= datos para vinculacion con moodle por programacion masiva por periodo academico
    public function sincronizarProgramacionUDMoodle(array $programaciones)
    {
        // Verificar constante de activación
        if (!defined('INTEGRACIONES_SYNC_ACTIVE') || !INTEGRACIONES_SYNC_ACTIVE) {
            return ['success' => false, 'errores' => ['Integraciones no activadas en config']];
        }

        // Obtener Token de la BD (Tu lógica original)
        $ds = $this->objDatosSistema->buscar();
        $apiKey = $ds['token_sistema'];

        $url = API_BASE_URL . "/api/integracion/sincronizarCursosMoodle";

        // 1. Dividir en lotes de 20 para no saturar tu API Master
        $loteSize = 20;
        $chunks = array_chunk($programaciones, $loteSize);

        $totalEnviados = 0;
        $errores = [];
        $respuestasAPI = [];

        foreach ($chunks as $lote) {

            // --- TU LÓGICA CURL ORIGINAL ---
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');

            // OJO: En producción idealmente deberías verificar SSL, pero lo dejamos false como pediste
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, INTEGRACIONES_SSL_VERIFY);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, INTEGRACIONES_SSL_VERIFY ? 2 : 0);

            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'X-Api-Key: ' . $apiKey
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($lote));

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                $errores[] = "Error cURL: " . curl_error($ch);
            }
            curl_close($ch);
            // -------------------------------

            // Procesar respuesta parcial
            $jsonResp = json_decode($response, true);

            if ($httpCode === 200 && isset($jsonResp['success']) && $jsonResp['success']) {
                $totalEnviados += count($lote);
            } else {
                $msg = $jsonResp['message'] ?? 'Error desconocido en API Master';
                $errores[] = "Lote fallido ($httpCode): " . $msg;
            }

            $respuestasAPI[] = $jsonResp;
        }

        // Retornar resumen unificado
        return [
            'success' => count($errores) === 0,
            'enviados' => count($programaciones),
            'procesados_api' => $totalEnviados,
            'errores' => $errores,
            'detalles_api' => $respuestasAPI // Para debug si lo necesitas
        ];
    }


    //============= eliminar programacion en moodle
    public function deleteProgramacionUd($id_programacion)
    {
        // Verificar constante de activación
        if (!defined('INTEGRACIONES_SYNC_ACTIVE') || !INTEGRACIONES_SYNC_ACTIVE) {
            return ['success' => false, 'errores' => ['Integraciones no activadas en config']];
        }

        // Obtener Token de la BD 
        $ds = $this->objDatosSistema->buscar();
        $apiKey = $ds['token_sistema'];
        if (empty($apiKey)) {
            return [
                'success' => false,
                'errores' => ['No se encontró token_sistema en datos del sistema'],
            ];
        }

        $url = API_BASE_URL . "/api/integracion/deleteProgramacionUd";
        $payload = [
            'id_programacion' => $id_programacion
        ];
        $errores = [];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');

        // OJO: En producción idealmente deberías verificar SSL, pero lo dejamos false como pediste
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, INTEGRACIONES_SSL_VERIFY);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, INTEGRACIONES_SSL_VERIFY ? 2 : 0);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Api-Key: ' . $apiKey
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $errores[] = "Error cURL: " . curl_error($ch);
        }
        curl_close($ch);

        $jsonResp = json_decode($response, true);

        if ($httpCode === 200 && isset($jsonResp['success']) && $jsonResp['success']) {
        } else {
            $msg = $jsonResp['message'] ?? 'Error desconocido en API Master';
            $errores[] = " fallido ($httpCode): " . $msg;
        }

        return [
            'success' => $jsonResp['success'],
            'errores' => $errores,
            'jsonResp' => $jsonResp
        ];
    }


    //=============== Sincronizar matricula de estudiante =======================
    public function SyncCreateMatricula($datos_moodle)
    {
        // Verificar constante de activación
        if (!defined('INTEGRACIONES_SYNC_ACTIVE') || !INTEGRACIONES_SYNC_ACTIVE) {
            return ['success' => false, 'errores' => ['Integraciones no activadas en config']];
        }

        // Obtener Token de la BD 
        $ds = $this->objDatosSistema->buscar();
        $apiKey = $ds['token_sistema'];
        if (empty($apiKey)) {
            return [
                'success' => false,
                'errores' => ['No se encontró token_sistema en datos del sistema'],
            ];
        }

        $url = API_BASE_URL . "/api/integracion/sincronizarMatriculaEstudiante";
        $payload = [
            'data' => $datos_moodle
        ];
        $errores = [];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');

        // OJO: En producción idealmente deberías verificar SSL, pero lo dejamos false como pediste
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, INTEGRACIONES_SSL_VERIFY);
        // En realidad, CURLOPT_SSL_VERIFYHOST espera un valor numérico (0, 1 o 2), no un booleano. Si INTEGRACIONES_SSL_VERIFY es false, deberías pasar 0. Si es true, deberías pasar 2.   
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, INTEGRACIONES_SSL_VERIFY ? 2 : 0);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Api-Key: ' . $apiKey
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $errores[] = "Error cURL: " . curl_error($ch);
        }
        curl_close($ch);

        $jsonResp = json_decode($response, true);

        if ($httpCode === 200 && isset($jsonResp['success']) && $jsonResp['success']) {
        } else {
            $msg = $jsonResp['message'] ?? 'Error desconocido en API Master';
            $errores[] = " fallido ($httpCode): " . $msg;
        }

        return [
            'success' => $jsonResp['success'],
            'errores' => $errores,
            'message' => $jsonResp['message'],
            'cantMatriculas' => $jsonResp['cantMatriculas']
        ];
    }

    //=============== Eliminar matricula de estudiante =======================
    public function eliminarMatriculaMoodle($datos_moodle)
    {
        // Verificar constante de activación
        if (!defined('INTEGRACIONES_SYNC_ACTIVE') || !INTEGRACIONES_SYNC_ACTIVE) {
            return ['success' => false, 'errores' => ['Integraciones no activadas en config']];
        }

        // Obtener Token de la BD 
        $ds = $this->objDatosSistema->buscar();
        $apiKey = $ds['token_sistema'];
        if (empty($apiKey)) {
            return [
                'success' => false,
                'errores' => ['No se encontró token_sistema en datos del sistema'],
            ];
        }

        $url = API_BASE_URL . "/api/integracion/deleteUserInCourse";
        $payload = [
            'data' => $datos_moodle
        ];
        $errores = [];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');

        // OJO: En producción idealmente deberías verificar SSL, pero lo dejamos false como pediste
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, INTEGRACIONES_SSL_VERIFY);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, INTEGRACIONES_SSL_VERIFY ? 2 : 0);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Api-Key: ' . $apiKey
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $errores[] = "Error cURL: " . curl_error($ch);
        }
        curl_close($ch);

        $jsonResp = json_decode($response, true);

        if ($httpCode === 200 && isset($jsonResp['success']) && $jsonResp['success']) {
        } else {
            $msg = $jsonResp['message'] ?? 'Error desconocido en API Master';
            $errores[] = " fallido ($httpCode): " . $msg;
        }

        return [
            'success' => $jsonResp['success'],
            'errores' => $errores,
            'message' => $jsonResp['message'],
            'cantDesmatriculas' => $jsonResp['cantDesmatriculas']
        ];
    }



    //=========================traer datos de seccion de curso 
    public function getSectionData($id_section, $courseid)
    {
        if (INTEGRACIONES_SYNC_ACTIVE) {
            $ds = $this->objDatosSistema->buscar();
            $apiKey = $ds['token_sistema'];
            $url = API_BASE_URL . "/api/integracion/getSectionData";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, INTEGRACIONES_SSL_VERIFY);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, INTEGRACIONES_SSL_VERIFY ? 2 : 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'X-Api-Key: ' . $apiKey
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'id_section' => $id_section,
                'courseid' => $courseid
            ]));
            $response = curl_exec($ch);
            curl_close($ch);
            return json_decode($response, true);
        } else {
            return ['success' => false, 'details' => 'Integraciones no activadas'];
        }
    }

    public function getEnabledModules()
    {
        if (INTEGRACIONES_SYNC_ACTIVE) {
            $ds = $this->objDatosSistema->buscar();
            $apiKey = $ds['token_sistema'];
            $url = API_BASE_URL . "/api/integracion/getEnabledModules";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, INTEGRACIONES_SSL_VERIFY);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, INTEGRACIONES_SSL_VERIFY ? 2 : 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'X-Api-Key: ' . $apiKey
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([]));
            $response = curl_exec($ch);
            curl_close($ch);
            return json_decode($response, true);
        } else {
            return ['success' => false, 'details' => 'Integraciones no activadas'];
        }
    }

    public function createModuleMoodle($courseid, $id_seccion_moodle, $modname, $moodle_params)
    {
        // Verificar constante de activación
        if (!defined('INTEGRACIONES_SYNC_ACTIVE') || !INTEGRACIONES_SYNC_ACTIVE) {
            return ['success' => false, 'details' => 'Integraciones no activadas'];
        }

        // Obtener Token de la BD
        $ds = $this->objDatosSistema->buscar();
        $apiKey = $ds['token_sistema'];
        if (empty($apiKey)) {
            return [
                'success' => false,
                'details' => 'No se encontró token_sistema en datos del sistema'
            ];
        }

        // Asumimos que el endpoint en tu API SIGI se llama "createModuleMoodle"
        $url = API_BASE_URL . "/api/integracion/createModuleMoodle";

        // Empaquetamos la data para la API
        $payload = [
            'courseid' => $courseid,
            'id_seccion_moodle' => $id_seccion_moodle,
            'modname' => $modname,
            'moodle_params' => $moodle_params
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, INTEGRACIONES_SSL_VERIFY);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, INTEGRACIONES_SSL_VERIFY ? 2 : 0);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Api-Key: ' . $apiKey
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return ['success' => false, 'details' => "Error cURL: " . $error];
        }
        curl_close($ch);

        $jsonResp = json_decode($response, true);

        if ($httpCode === 200 && isset($jsonResp['success']) && $jsonResp['success']) {
            return [
                'success' => true,
                // Asumimos que la API devuelve los IDs generados (cmid, url, etc) en 'data' o similar
                'data' => $jsonResp['data'] ?? [],
                'details' => $jsonResp['details'] ?? $jsonResp['message'] ?? 'Módulo creado exitosamente'
            ];
        } else {
            $msg = $jsonResp['details'] ?? $jsonResp['message'] ?? 'Error desconocido en API Master';
            return [
                'success' => false,
                'details' => "Fallo ($httpCode): " . $msg,
                'raw_response' => $jsonResp
            ];
        }
        /*return [
            $payload
        ];*/
    }


    public function uploadFile($filePath, $fileName)
    {
        if (!defined('INTEGRACIONES_SYNC_ACTIVE') || !INTEGRACIONES_SYNC_ACTIVE) return null;

        $ds = $this->objDatosSistema->buscar();
        $apiKey = $ds['token_sistema'];
        $url = API_BASE_URL . "/api/integracion/uploadFileMoodle";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, INTEGRACIONES_SSL_VERIFY);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Api-Key: ' . $apiKey]);
        curl_setopt($ch, CURLOPT_POST, true);

        // Enviamos el archivo físico usando CURLFile
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'file' => new \CURLFile($filePath, null, $fileName),
            'filename' => $fileName
        ]);

        $response = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($response, true);

        return (isset($res['success']) && $res['success']) ? $res['itemid'] : null;
    }
}
