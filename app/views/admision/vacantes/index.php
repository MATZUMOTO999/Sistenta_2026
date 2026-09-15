<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php if (\Core\Auth::esAdminAdmision()): ?>
    <div class="card p-2">
        <h3 class="mb-2">Vacantes</h3>
        <div class="col-md-3 mb-2">
            <a href="<?= BASE_URL ?>/admision/vacantes/nuevo" class="btn btn-success mt-2">Nueva Vacante</a>
        </div>
        <div class="table-responsive col-12">
            <table id="tabla-vacantes" class="table table-bordered table-hover table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Proceso Admision</th>
                        <th>Tipo Modalidad</th>
                        <th>Modalidad</th>
                        <th>Programa Estudios</th>
                        <th>Cantidad</th>
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
            const tabla = $('#tabla-vacantes').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    url: '<?= BASE_URL ?>/admision/vacantes/data',
                    type: 'GET',
                },
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1 + meta.settings._iDisplayStart;
                        }
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
                        data: 'cantidad'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                        <a href="<?= BASE_URL ?>/admision/vacantes/editar/${row.id}" class="btn btn-warning btn-sm m-1">Editar</a>
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