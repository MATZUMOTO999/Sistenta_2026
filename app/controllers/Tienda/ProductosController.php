<?php

namespace App\Controllers\Tienda;

use Core\Controller;
// Incluir manualmente el modelo
//require_once __DIR__ . '/../../../app/models/Admision/ProcesosAdmision.php';

/*use App\Models\Admision\TiposModalidades;
use App\Models\Sigi\DatosInstitucionales;*/

class ProductosController extends Controller
{
    protected $model;

    public function __construct()
    {
        parent::__construct();
        /*$this->model = new Inscripciones();
        $this->apiController = new ApiController();*/
    }

    public function index()
    {
        /*if (\Core\Auth::esAdminAdmision()):
        endif;*/
        $this->view('tienda/productos/index', [
            'isTienda'   => true,
            'module'    => 'tienda',
            'pageTitle' => 'productos'
        ]);
        exit;
    }
    public function nuevo()
    {
        $this->view('tienda/productos/nuevo', [
            'isTienda'   => true,
            'module'    => 'tienda',
            'pageTitle' => 'Nuevo Producto'
        ]);
        exit;
    }

    /*public function data()
    {
        if (\Core\Auth::esAdminAdmision()):
            header('Content-Type: application/json; charset=utf-8');
            $draw      = $_GET['draw']  ?? 1;
            $start     = $_GET['start'] ?? 0;
            $length    = $_GET['length'] ?? 10;
            $orderCol  = $_GET['order'][0]['column'] ?? 1;
            $orderDir  = $_GET['order'][0]['dir']    ?? 'asc';
            $filters = [
                'id_sede'       => $_SESSION['sigi_sede_actual'] ?? null,
                'id_periodo'    => $_SESSION['sigi_periodo_actual_id'] ?? null,
            ];
            $result = $this->model->getPaginated($filters, $length, $start, $orderCol, $orderDir);

            echo json_encode([
                'draw'            => (int)$draw,
                'recordsTotal'    => (int)$result['total'],
                'recordsFiltered' => (int)$result['total'],
                'data'            => $result['data']
            ], JSON_UNESCAPED_UNICODE);
            exit;
        endif;
        exit;
    }*/
/*
    public function nuevo()
    {
        if (\Core\Auth::esAdminAdmision()):
            $periodo = $_SESSION['sigi_periodo_actual_id'];
            $sede = $_SESSION['sigi_sede_actual'];
            $procesosAdmision = $this->procesosAdmision->getProcesosAdmisionSedePeriodo($sede, $periodo);
            $procesoAdmisionSeleccionado = null;
            $tipoModalidadSeleccionado = null;
            $tiposModalidades = $this->tiposModalidades->getTiposModalidades();
            $programaSeleccionado = null;
            $programas = $this->programa->getTodosProgramas();
            $modalidadSeleccionado = null;
            $requisitos = ['DNI', 'Certificado de Estudios'];
        endif;
        $this->view('admision/inscripciones/nuevo', [
            'IsEdit' => false,
            'procesosAdmision' => $procesosAdmision,
            'procesoAdmisionSeleccionado' => $procesoAdmisionSeleccionado,
            'tipoModalidadSeleccionado' => $tipoModalidadSeleccionado,
            'tiposModalidades' => $tiposModalidades,
            'programaSeleccionado' => $programaSeleccionado,
            'programas' => $programas,
            'modalidadSeleccionado' => $modalidadSeleccionado,
            'requisitos' => $requisitos,
            'isEdit'    => false,
            'module'    => 'admision',
            'pageTitle' => 'Nueva Inscripcion'
        ]);
        exit;
    }*/
    /*public function guardar()
    {
        if (\Core\Auth::esAdminAdmision()):


            $id_usuario = $_POST['id_usuario_sigi'];

            // Si no hay ID de usuario pero hay DNI y Nombre (caso nuevo desde API), crear usuario
            if (empty($id_usuario) && !empty($_POST['dni_postulante']) && !empty($_POST['apellido_paterno_postulante']) && !empty($_POST['apellido_materno_postulante']) && !empty($_POST['nombres_postulante'])) {
                $newUserData = [
                    'tipo_doc' => $_POST['tipo_doc_postulante'],
                    'dni' => $_POST['dni_postulante'],
                    'apellidos_nombres' => strtoupper($_POST['apellido_paterno_postulante'] . '_' . $_POST['apellido_materno_postulante'] . '_' . $_POST['nombres_postulante']),
                    'genero' => $_POST['genero'],
                    'fecha_nacimiento' => $_POST['fecha_nacimiento'],
                    'telefono' => $_POST['telefono'],
                    'correo' => $_POST['correo'],
                    'direccion' => $_POST['direccion'],
                    'id_periodo_registro' => $_SESSION['sigi_periodo_actual_id'],
                    'id_sede' => $_SESSION['sigi_sede_actual'],
                    'id_programa_estudios' => $_POST['id_programa_estudio'],
                    'discapacidad' => 'NO',
                    'id_rol' => 8,
                    'distrito_nacimiento' => $_POST['departamento'] . '-' . $_POST['provincia'] . '-' . $_POST['distrito'],
                    'password' => '',
                    'reset_password' => 0,
                    'token_password' => '',
                    'estado' => 0,
                ];
                $id_usuario = $this->docente->nuevo($newUserData);
            }

            if (empty($id_usuario)) {
                $_SESSION['flash_error'] = "Debe buscar y seleccionar un postulante válido.";
                header('Location: ' . BASE_URL . '/admision/inscripciones/nuevo');
                exit;
            }
            $codigoInscripcion = $this->model->generarCodigoPostulante($_POST['dni_postulante']);

            // Process requirements array from POST
            $requisitos = '';
            if (isset($_POST['requisitos_adjuntos']) && is_array($_POST['requisitos_adjuntos'])) {
                $requisitos = implode(',', $_POST['requisitos_adjuntos']);
            }

            //cargar foto
            $fotoPath = '';
            if (isset($_POST['foto_actual'])) {
                $fotoPath = $_POST['foto_actual'];
            }

            $uploadDir = __DIR__ . '/../../../public/uploads/admision/fotos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // check base64 first
            if (!empty($_POST['foto_capture_base64'])) {
                $data = $_POST['foto_capture_base64'];
                if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                    $data = substr($data, strpos($data, ',') + 1);
                    $type = strtolower($type[1]); // jpg, png, etc.
                    if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                        // invalid file type
                    }
                    $data = base64_decode($data);
                    if ($data !== false) {
                        $filename = uniqid('foto_cam_') . '.' . $type;
                        $targetFile = $uploadDir . $filename;
                        if (file_put_contents($targetFile, $data)) {
                            $fotoPath = 'public/uploads/admision/fotos/' . $filename;
                        }
                    }
                }
            }
            // Fallback to normal upload
            elseif (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $filename = uniqid('foto_') . '_' . basename($_FILES['foto']['name']);
                $targetFile = $uploadDir . $filename;
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $targetFile)) {
                    $fotoPath = 'public/uploads/admision/fotos/' . $filename;
                }
            }

            $data = [
                'id' => $_POST['id'] ?? null,
                'id_usuario_sigi' => $id_usuario,
                'id_proceso_admision' => $_POST['id_proceso_admision'],
                'id_modalidad_admision' => $_POST['id_modalidad_admision'],
                'id_programa_estudio' => $_POST['id_programa_estudio'],
                'colegio_procedencia' => $_POST['colegio_procedencia'],
                'anio_egreso_colegio' => $_POST['anio_egreso_colegio'],
                'foto' => $fotoPath,
                'calificacion' => '',
                'codigo' => $codigoInscripcion,
                'estado' => 'postulante',
                'requisitos' => $requisitos,
            ];

            // Validar unicidad (Usuario en el mismo proceso)
            if ($this->model->checkDuplicate($data['id_usuario_sigi'], $data['id_proceso_admision'], $data['id_modalidad_admision'], $data['id_programa_estudio'], $data['id'])) {
                $_SESSION['flash_error'] = "El usuario ya está inscrito en este proceso de admisión para el mismo programa de estudios.";
                if (!empty($data['id'])) {
                    header('Location: ' . BASE_URL . '/admision/inscripciones/editar/' . $data['id']);
                } else {
                    header('Location: ' . BASE_URL . '/admision/inscripciones/nuevo');
                }
                exit;
            }
            //actualizar datos del usuario
            $userData = [
                'dni' => $_POST['dni_postulante'],
                'apellidos_nombres' => strtoupper($_POST['apellido_paterno_postulante'] . '_' . $_POST['apellido_materno_postulante'] . '_' . $_POST['nombres_postulante']),
                'genero' => $_POST['genero'],
                'fecha_nacimiento' => $_POST['fecha_nacimiento'],
                'telefono' => $_POST['telefono'],
                'correo' => $_POST['correo'],
                'direccion' => $_POST['direccion'],
                'id_sede' => $_SESSION['sigi_sede_actual'] ?? 1,
                'discapacidad' => 'NO',
                'distrito_nacimiento' => $_POST['departamento'] . '-' . $_POST['provincia'] . '-' . $_POST['distrito'],
            ];
            $this->docente->updateInAdmision($id_usuario, $userData);
            $this->model->guardar($data);
            $_SESSION['flash_success'] = "Inscripcion guardada correctamente.";
        endif;
        header('Location: ' . BASE_URL . '/admision/inscripciones');
        exit;
    }*/
    /*public function editar($id)
    {
        if (\Core\Auth::esAdminAdmision()):
            $inscripcion = $this->model->find($id);

            // Explode requirements string to array for view
            if (!empty($inscripcion['requisitos'])) {
                $inscripcion['requisitos'] = explode(',', $inscripcion['requisitos']);
            } else {
                $inscripcion['requisitos'] = [];
            }

            $requisitos = ['DNI', 'Certificado de Estudios'];
            $procesosAdmision = $this->procesosAdmision->getProcesosAdmisionSedePeriodo($_SESSION['sigi_sede_actual'], $_SESSION['sigi_periodo_actual_id']);
            $procesoAdmisionSeleccionado = $inscripcion['id_proceso_admision'];
            $tipoModalidadSeleccionado = $inscripcion['id_tipo_modalidad'];
            $modalidadSeleccionado = $inscripcion['id_modalidad_admision'];
            $tiposModalidades = $this->tiposModalidades->getTiposModalidades();
            $programaSeleccionado = $inscripcion['id_programa_estudio'];
            $programas = $this->programa->getTodosProgramas();

            $departamento = '';
            $provincia = '';
            $distrito = '';

            if (!empty($inscripcion['distrito_nacimiento'])) {
                $parts = explode('-', $inscripcion['distrito_nacimiento']);
                if (count($parts) >= 3) {
                    $departamento = $parts[0];
                    $provincia = $parts[1];
                    $distrito = $parts[2];
                }
            }

            // Assign to inscripcion so form_fields can use them
            $inscripcion['departamento'] = $departamento;
            $inscripcion['provincia'] = $provincia;
            $inscripcion['distrito'] = $distrito;
        endif;
        $this->view('admision/inscripciones/editar', [
            'IsEdit' => true,
            'inscripcion' => $inscripcion,
            'procesosAdmision' => $procesosAdmision,
            'tiposModalidades' => $tiposModalidades,
            'programas' => $programas,
            'programaSeleccionado' => $programaSeleccionado,
            'procesoAdmisionSeleccionado' => $procesoAdmisionSeleccionado,
            'tipoModalidadSeleccionado' => $tipoModalidadSeleccionado,
            'modalidadSeleccionado' => $modalidadSeleccionado,
            'requisitos' => $requisitos,
            'isEdit'    => true,
            'module'    => 'admision',
            'pageTitle' => 'Editar Inscripcion'
        ]);
        exit;
    }*/
    /*public function pdfFichaInscripcion($id)
    {
        require_once __DIR__ . '/../../../vendor/autoload.php';
        $inscripcion = $this->model->find($id);
        $datosSistema = $this->datosSistema->buscar();
        $datosSede = $this->sedes->find($_SESSION['sigi_sede_actual']);
        $procesoAdmision = $this->procesosAdmision->find($inscripcion['id_proceso_admision']);
        $colegioResponse = json_decode($this->apiController->apiColegios($inscripcion['colegio_procedencia']));
        $colegioProcedencia = null;
        if ($colegioResponse && isset($colegioResponse->data) && count($colegioResponse->data) > 0) {
            $colegioProcedencia = $colegioResponse->data[0];
        } else {
            // Fallback object or empty to avoid errors
            $colegioProcedencia = (object)[
                'Nombre' => 'NO ENCONTRADO',
                'CodigoModular' => $inscripcion['colegio_procedencia'],
                'Departamento' => '',
                'Provincia' => '',
                'Distrito' => '',
                'Direccion' => '',
                'Gestion' => '',
                'Modalidad' => ''
            ];
        }

        $departamento = '';
        $provincia = '';
        $distrito = '';

        if (!empty($inscripcion['distrito_nacimiento'])) {
            $parts = explode('-', $inscripcion['distrito_nacimiento']);
            if (count($parts) >= 3) {
                $departamento = $parts[0];
                $provincia = $parts[1];
                $distrito = $parts[2];
            }
        }
        // Assign to inscripcion so form_fields can use them
        $inscripcion['departamento'] = $departamento;
        $inscripcion['provincia'] = $provincia;
        $inscripcion['distrito'] = $distrito;

        /* PDF */
        /*$pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetTitle("Ficha de Inscripcion");
        $pdf->setPrintHeader(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->AddPage();

        ob_start();
        include __DIR__ . '/../../views/admision/inscripciones/pdf_ficha_inscripcion.php';
        $html = ob_get_clean();

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('Ficha_Inscripcion.pdf', 'I');*/
    /*}
    public function pdfCarnet($id)
    {
        require_once __DIR__ . '/../../../vendor/autoload.php';
        $inscripcion = $this->model->find($id);
        $datosSistema = $this->datosSistema->buscar();
        $datosSede = $this->sedes->find($_SESSION['sigi_sede_actual']);
        $procesoAdmision = $this->procesosAdmision->find($inscripcion['id_proceso_admision']);
        $departamento = '';
        $provincia = '';
        $distrito = '';

        if (!empty($inscripcion['distrito_nacimiento'])) {
            $parts = explode('-', $inscripcion['distrito_nacimiento']);
            if (count($parts) >= 3) {
                $departamento = $parts[0];
                $provincia = $parts[1];
                $distrito = $parts[2];
            }
        }
        // Assign to inscripcion so form_fields can use them
        $inscripcion['departamento'] = $departamento;
        $inscripcion['provincia'] = $provincia;
        $inscripcion['distrito'] = $distrito;

        /* PDF */
        //$pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        /*$pdf = new \TCPDF('P', 'mm', array(297, 210), true, 'UTF-8', false);
        $pdf->SetTitle("Carnet de Inscripcion");
        $pdf->setPrintHeader(false);
        $pdf->SetMargins(20, 20, 20);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->AddPage();
        if ($datosSistema['fondo_carnet_postulante']) {
            $template = __DIR__ . '/../../../public/images/' . $datosSistema['fondo_carnet_postulante'];
        } else {
            $template = __DIR__ . '/../../../public/img/plantilla_carnet.png';
        }

        $pdf->Image($template, 5, 5, 85.6, 54, '', '', '', false, 300, '', false, false, 0, false, false, false);
        // 2) Texto encima (usa coordenadas en mm)
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', 'B', 5);

        $pdf->SetXY(40, 24);
        $pdf->MultiCell(50, 5, "SEDE " . $datosSede['nombre'], 0, 'C', false, 1);
        $pdf->SetXY(40, 26);
        $pdf->MultiCell(50, 5, "PROCESO DE ADMISIÓN: " . $procesoAdmision['nombre'], 0, 'C', false, 1);
        $pdf->SetXY(40, 28);
        $pdf->MultiCell(50, 5, $inscripcion['tipo_modalidad_nombre'] . ' - ' . $inscripcion['modalidad_nombre'], 0, 'C', false, 1);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetXY(41, 31);
        $pdf->MultiCell(50, 5, $inscripcion['tipo_doc'].': '.$inscripcion['usuario_dni'], 0, 'L', false, 1);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetXY(41, 35.5);
        $pdf->MultiCell(50, 5, $inscripcion['apellido_paterno'], 0, 'L', false, 1);
        $pdf->SetXY(41, 40);
        $pdf->MultiCell(50, 5, $inscripcion['apellido_materno'], 0, 'L', false, 1);
        $pdf->SetXY(41, 44.5);
        $pdf->MultiCell(50, 5, $inscripcion['nombres'], 0, 'L', false, 1);


        $pdf->SetXY(37, 51);
        // Valor del código
        $codigo = $inscripcion['usuario_dni'];

        // Estilo del barcode
        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => 0,
            'vpadding' => 0,
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false,
            'text' => false,      // muestra texto debajo del barcode
            'font' => 'helvetica',
            'fontsize' => 7,
            'stretchtext' => 4
        );
        // Guardar estado + rotar
        $pdf->StartTransform();

        // Rota 90 grados alrededor del punto (x,y)
        $x = 7.5;  // mm
        $y = 48;  // mm
        $pdf->Rotate(90, $x, $y);
        $pdf->write1DBarcode(
            $codigo,
            'C128',
            $x,
            $y,
            25.5,
            4.5,
            0.4,
            $style,
            'N'
        );
        $pdf->StopTransform();

        $pdf->SetFont('helvetica', 'B', 6.5);
        $pdf->SetXY(7, 50.8);
        $pdf->MultiCell(81.5, 6, $inscripcion['programa_estudio_nombre'], 0, 'C', false, 1);

        $foto = BASE_URL . '/' . $inscripcion['foto'];
        $pdf->Image($foto, 15.1, 26, 21, 20.5, '', '', '', false, 300);

        $pdf->Output('Carnet Inscripcion '.$inscripcion['usuario_dni'].'.pdf', 'I');
    /*}*/
}
