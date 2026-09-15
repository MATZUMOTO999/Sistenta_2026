<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php if (\Core\Auth::esAdminAdmision()): ?>
    <div class="card p-2">
        <h3 class="mb-2">Inscripciones</h3>
        <div class="col-md-3 mb-2">
            <a href="<?= BASE_URL ?>/admision/inscripciones/nuevo" class="btn btn-success mt-2">Nueva Inscripcion</a>
        </div>
        <div class="table-responsive col-12">
            <table id="tabla-inscripciones" class="table table-bordered table-hover table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Doc.</th>
                        <th>Número</th>
                        <th>Apellidos y Nombres</th>
                        <th>Proceso Admision</th>
                        <th>Tipo Modalidad</th>
                        <th>Modalidad</th>
                        <th>Programa Estudios</th>
                        <th>Fecha Inscripcion</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTables AJAX llenará esto -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabla = $('#tabla-inscripciones').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    url: '<?= BASE_URL ?>/admision/inscripciones/data',
                    type: 'GET',
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-SIGI-DT', '1');
                    }
                },
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1 + meta.settings._iDisplayStart;
                        }
                    },
                    {
                        data: 'tipo_doc'
                    },
                    {
                        data: 'dni'
                    },
                    {
                        data: 'apellidos_nombres'
                    },
                    {
                        data: 'proceso_admision_nombre'
                    },
                    {
                        data: 'tipo_modalidad_nombre'
                    },
                    {
                        data: 'modalidad_nombre'
                    },
                    {
                        data: 'programa_estudio_nombre'
                    },
                    {
                        data: 'create_at'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                        <a href="<?= BASE_URL ?>/admision/inscripciones/editar/${row.id}" class="btn btn-warning btn-sm m-1">Editar</a>
                        <a href="<?= BASE_URL ?>/admision/inscripciones/pdfFichaInscripcion/${row.id}" target="_blank" class="btn btn-info btn-sm m-1">Ficha</a>
                        <a href="<?= BASE_URL ?>/admision/inscripciones/pdfCarnet/${row.id}" target="_blank" class="btn btn-info btn-sm m-1">Carnet</a>
                        `;
                        }
                    }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json'
                }
            });
        });
    </script>
<?php else: ?>
    <!-- Para director o coordinador en SIGI -->
    <p>El Modulo SIGI solo es para rol de Administrador</p>
<?php endif; ?>
<?php require __DIR__ . '/../../layouts/footer.php'; ?>