<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php if(\Core\Auth::esAdmin()): ?>
    <div class="card p-2">
        <h4>Categorías</h4>
        <div class="col-md-2 mb-2">
            <a href="<?= BASE_URL ?>/admin/categorias/nuevo" class="btn btn-success"> + Nueva Categoría</a>
        </div>
        <div class="table-responsive">
            <table id="tabla-categoria" class="table table-bordered table-hover align-middle tabla-sm">
                <thead class="table-light">
                    <tr>
                        <th>Nro</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Será datatable con AJAX JS -->
                </tbody>
            </table>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const tablaCategoria = $('#tabla-categoria').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    url: '<?= BASE_URL ?>/admin/categorias/datatable',
                    type: 'GET',
                    data: function(d) {
                        // Puedes agregar parámetros adicionales aquí si es necesario
                    }
                },
                columns: [
                    { data: null, render: function(data, type, row, meta){
                        return meta.row + 1; // Números de fila
                    } },
                    { data: 'nombre'},
                    { data: 'descripcion'},
                    { data: 'estado_text'},
                    { 
                        data: null, 
                        orderable: false, 
                        searchable: false,
                        render: function(data, type, row){
                            return `
                                <a href="<?= BASE_URL ?>/admin/categorias/editar/${row.id}" class="btn btn-sm btn-primary">Editar</a>
                                <button class="btn btn-sm btn-danger" onclick="eliminarCategoria(${row.id})">Eliminar</button>
                            `;
                        } 
                    }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                }
            });
        });
    </script>
<?php else: ?>
    <div class="alert alert-danger" role="alert">
        No tienes permisos para acceder a esta página.
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../../layouts/footer.php'; ?>