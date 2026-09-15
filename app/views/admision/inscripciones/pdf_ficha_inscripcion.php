<?php
$html = ob_get_clean();
$pdf->writeHTML($html, true, false, true, false, '');
$logoMineduPath = (__DIR__ . '/../../../../public/img/logo_minedu.jpeg');
if ($datosSistema['logo'] != '') {
    $logoPath = __DIR__ . '/../../../../public/images/' . $datosSistema['logo'];
} else {
    $logoPath = __DIR__ . '/../../../../public/img/logo_completo.png';
}
$pdf->Image($logoMineduPath, 15, 10, 30, 8); // (x, y, width en mm)
$pdf->Image($logoPath, 165, 10, 30, 10); // (x, y, width en mm)
ob_start();
?>
<style>
    body {
        font-family: helvetica, sans-serif;
        font-size: 9pt;
        color: #333;
    }

    /* Clases generales de tabla */
    table {
        width: 100%;
        border-spacing: 3px;
        border-collapse: collapse;
        font-size: 9px;
        text-align: center;
    }

    td {
        padding: 3px;
        vertical-align: middle;
    }

    /* Estilos de bordes para las cajas de input */
    .input-box {
        border: 1px solid #000;
        background-color: #fff;
        height: 16px;
        line-height: 16px;
        /* Center text vertically */
        vertical-align: middle;
        /* Altura fija para simular el input */
        padding-left: 5px;
    }

    /* Etiquetas de texto (Labels) */
    .label {
        text-align: right;
        font-weight: bold;
        color: #555;
        padding-right: 5px;
        vertical-align: middle;
        font-size: 7px;
        width: 1%;
        /* Truco para que la celda se ajuste al texto */
        white-space: nowrap;
    }

    /* Títulos de las secciones */
    .section-header {
        background-color: #e0e0e0;
        border: 1px solid #000;
        font-weight: bold;
        padding: 5px;
        margin-top: 10px;
        margin-bottom: 5px;
        font-size: 10pt;
    }

    /* Cabecera del documento */
    .header-title {
        text-align: center;
        font-weight: bold;
        font-size: 11pt;
    }

    .header-sub {
        text-align: center;
        font-size: 9pt;
    }

    .header-resol {
        text-align: right;
        font-size: 6pt;
        color: #666;
    }

    /* Foto */
    .photo-container {
        border: 0px solid #ffffffff;
        text-align: center;
        height: 120px;
        width: 100px;
    }

    /* Footer */
    .huella-box {
        border: 1px solid #000;
        height: 100px;
        width: 100px;
        background-color: #ffffff;
        margin: 0 auto;
        border-radius: 10px;
        /* TCPDF soporta radio simple */
    }

    .firma-line {
        border-top: 1px solid #000;
        width: 200px;
        margin: 0 auto;
        text-align: center;
        padding-top: 5px;
    }
</style>
<table cellpadding="0" cellspacing="0">
    <tr>
        <td width="100%" align="center">
            <div class="header-sub"><?= $datosSistema['nombre_completo'] ?></div>
            <div class="header-title">PROCESO DE ADMISIÓN : <?= $procesoAdmision['nombre'] ?></div>
            <div class="header-sub">FICHA DE INSCRIPCIÓN DEL POSTULANTE</div>
        </td>
        <!-- <td width="25%" class="header-resol">
            Creado por R.M. N° 265-86-ED<br>
            Revalidado por R.M. N° 161-2005-ED
        </td> -->
    </tr>
</table>
<br>
<div class="section-header">DATOS PERSONALES</div>
<br>
<table>
    <tr>
        <td width="100%">
            <table>
                <tr>
                    <td class="label" width="20%">CÓDIGO DE POSTULANTE:</td>
                    <td class="input-box" width="20%"><?= $inscripcion['codigo'] ?></td>
                    <td width="20%"></td>
                    <td width="20%"></td>
                    <td width="20%" rowspan="6">
                        <div class="photo-box">
                            <br>
                            <?php if ($inscripcion['foto'] != ''): ?>
                                <img src="<?= BASE_URL . '/' . $inscripcion['foto'] ?>" alt="Foto">
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="label" width="20%">POSTULANTE:</td>
                    <td class="input-box" colspan="3"><?= $inscripcion['usuario_nombre'] ?></td>
                </tr>
                <tr>
                    <td class="label" width="20%">TIPO DOC.:</td>
                    <td class="input-box"><?= $inscripcion['tipo_doc'] ?></td>
                    <td class="label" width="20%">DOC. IDENTIDAD:</td>
                    <td class="input-box"><?= $inscripcion['usuario_dni'] ?></td>
                </tr>
                <tr>
                    <td class="label" width="20%">GENERO:</td>
                    <td class="input-box"><?= $inscripcion['genero'] == 'M' ? 'MASCULINO' : 'FEMENINO' ?></td>
                    <td class="label" width="20%">EDAD:</td>
                    <td class="input-box"><?= $inscripcion['create_at'] - $inscripcion['fecha_nacimiento'] ?></td>
                </tr>
                <tr>
                    <td class="label" width="20%">FECHA DE NACIMIENTO:</td>
                    <td class="input-box"><?= date('d/m/Y', strtotime($inscripcion['fecha_nacimiento'])) ?></td>
                    <td class="label" width="20%">DPTO.NAC:</td>
                    <td class="input-box"><?= $inscripcion['departamento'] ?></td>
                </tr>
                <tr>
                    <td class="label" width="20%">PROV.NAC:</td>
                    <td class="input-box"><?= $inscripcion['provincia'] ?></td>
                    <td class="label" width="20%">DIST.NAC:</td>
                    <td class="input-box"><?= $inscripcion['distrito'] ?></td>
                </tr>
                <tr>
                    <td class="label" width="20%">DIRECCION:</td>
                    <td class="input-box" colspan="4"><?= $inscripcion['direccion'] ?></td>
                </tr>
                <tr>
                    <td class="label" width="20%">TELEFONOS:</td>
                    <td class="input-box"><?= $inscripcion['telefono'] ?></td>
                    <td class="label" width="20%">E-MAIL:</td>
                    <td class="input-box" colspan="2"><?= $inscripcion['correo'] ?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<br>
<div class="section-header">INSTITUCION EDUCATIVA DE PROCEDENCIA</div>
<br>
<table>
    <tr>
        <td class="label" width="20%">NOMBRE DE LA I.E.:</td>
        <td class="input-box" colspan="3" width="80%"><?= $colegioProcedencia->CEN_EDU ?? '' ?></td>
    </tr>
    <tr>
        <td class="label" width="20%">CODIGO MODULAR:</td>
        <td class="input-box" width="25%"><?= $colegioProcedencia->CodigoModular ?? '' ?></td>
        <td class="label" width="20%">DPTO.:</td>
        <td class="input-box" width="35%"><?= $colegioProcedencia->D_DPTO ?? '' ?></td>
    </tr>
    <tr>
        <td class="label" width="20%">PROVINCIA:</td>
        <td class="input-box" width="25%"><?= $colegioProcedencia->D_PROV ?? '' ?></td>
        <td class="label" width="20%">DISTRITO:</td>
        <td class="input-box" width="35%"><?= $colegioProcedencia->D_DIST ?? '' ?></td>
    </tr>
    <tr>
        <td class="label" width="20%">GESTION:</td>
        <td class="input-box" width="80%" colspan="3"><?= ($colegioProcedencia->D_GESTION . ' - ' . $colegioProcedencia->D_GES_DEP) ?? '' ?></td>
    </tr>
    <tr>
        <td class="label" width="20%">GENERO:</td>
        <td class="input-box" width="25%"><?= $colegioProcedencia->D_TIPSSEXO ?? '' ?></td>
        <td class="label" width="20%">NIVEL:</td>
        <td class="input-box" width="35%"><?= $colegioProcedencia->D_NIV_MOD ?? '' ?></td>
    </tr>
    <tr>
        <td class="label" width="20%">DIRECCION:</td>
        <td class="input-box" colspan="3" width="80%"><?= $colegioProcedencia->DIR_CEN ?? '' ?></td>
    </tr>
</table>
<br>
<div class="section-header">CARRERA A POSTULAR</div>
<br>
<table>
    <tr>
        <td class="label" width="20%">ESPECIALIDAD:</td>
        <td class="input-box" width="80%"><?= $inscripcion['programa_estudio_nombre'] ?></td>
    </tr>
    <tr>
        <td class="label" width="20%">MODALIDAD:</td>
        <td class="input-box" width="80%"><?= $inscripcion['tipo_modalidad_nombre'] . ' - ' . $inscripcion['modalidad_nombre'] ?></td>
    </tr>
    <tr>
        <td class="label" width="20%">OTRO DOCUMENTO:</td>
        <td class="input-box" width="80%"><?= $inscripcion['requisitos'] ?></td>
    </tr>
</table>

<br><br><br><br>
<table width="100%">
    <tr>
        <td width="25%" align="center">
            <table cellpadding="0" cellspacing="0" width="70%">
                <tr>
                    <td height="100px" class="huella-box">
                    </td>
                </tr>
                <tr>
                    <td align="center" style="font-size: 8pt; color:#000;">Huella Dactilar</td>
                </tr>
            </table>
        </td>
        <td width="35%" align="right" valign="bottom">
            <div style="height: 50px;">
            </div>
            <br>
            <br>
            <br>
            <div style="font-size: 8pt; color:#000; left: 5%;">
                _________________<br>Firma del Postulante
            </div>
        </td>
        <td width="40%" align="right" valign="bottom" style="font-size: 9pt;">
            <?php
            if ($inscripcion['update_at']) {
                $fecha = new DateTime($inscripcion['update_at']);
            } else {
                $fecha = new DateTime($inscripcion['create_at']);
            }
            $fmt = new IntlDateFormatter(
                'es-ES',
                IntlDateFormatter::FULL,
                IntlDateFormatter::FULL,
                'America/Lima',
                IntlDateFormatter::GREGORIAN,
                'dd \'de\' MMMM \'del\' yyyy'
            );
            echo $datosSede['distrito'] . ', ' . $fmt->format($fecha);
            ?>
        </td>
    </tr>
</table>