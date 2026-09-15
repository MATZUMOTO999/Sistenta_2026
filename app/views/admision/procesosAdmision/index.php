<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php if (\Core\Auth::esAdminAdmision()): ?>
    <div class="card p-2">
        <h3 class="mb-2">Procesos de Admision</h3>
        <div class="col-md-3 mb-2">
            <a href="<?= BASE_URL ?>/admision/procesosAdmision/nuevo" class="btn btn-success mt-2">Nuevo Proceso de Admision</a>
        </div>
        <div class="table-responsive col-12">
            <table id="tabla-procesosAdmision" class="table table-bordered table-hover table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Periodo</th>
                        <th>Sede</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
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
            const tabla = $('#tabla-procesosAdmision').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    url: '<?= BASE_URL ?>/admision/procesosAdmision/data',
                    type: 'GET',
                },
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1 + meta.settings._iDisplayStart;
                        }
                    },
                    {
                        data: 'nombre'
                    },
                    {
                        data: 'periodo_nombre'
                    },
                    {
                        data: 'sede_nombre'
                    },
                    {
                        data: 'fecha_inicio'
                    },
                    {
                        data: 'fecha_fin'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                        <a href="<?= BASE_URL ?>/admision/procesosAdmision/editar/${row.id}" class="btn btn-warning btn-sm m-1">Editar</a>
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