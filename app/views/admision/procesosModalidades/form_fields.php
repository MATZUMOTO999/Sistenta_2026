<div class="row">
    <div class="col-md-4 mb-2">
        <label class="form-label">Proceso Admision *</label>
        <select name="id_proceso_admision" id="id_proceso_admision" class="form-control" required>
            <option value="">Seleccione...</option>
            <?php foreach ($procesosAdmision as $p): ?>
                <option value="<?= $p['id'] ?>"
                    <?= ($procesoAdmisionSeleccionado == $p['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4 mb-2">
        <label class="form-label">Tipo de Modalidad *</label>
        <select name="id_tipo_modalidad" id="id_tipo_modalidad" class="form-control" required>
            <option value="">Seleccione...</option>
        </select>
    </div>
    <div class="col-md-4 mb-2">
        <label class="form-label">Fecha Inicio *</label>
        <input type="date" name="fecha_inicio" class="form-control" required
            value="<?= htmlspecialchars($procesoModalidad['fecha_inicio'] ?? '') ?>">
    </div>
    <div class="col-md-4 mb-2">
        <label class="form-label">Fecha Fin *</label>
        <input type="date" name="fecha_fin" class="form-control" required
            value="<?= htmlspecialchars($procesoModalidad['fecha_fin'] ?? '') ?>">
    </div>
    <div class="col-md-4 mb-2">
        <label class="form-label">Fecha Cierre de Inscripción *</label>
        <input type="date" name="fecha_cierre_inscripcion" class="form-control" required
            value="<?= htmlspecialchars($procesoModalidad['fecha_cierre_inscripcion'] ?? '') ?>">
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const procesoSelect = document.getElementById('id_proceso_admision');
        const modalidadSelect = document.getElementById('id_tipo_modalidad');
        const modalidadPreseleccionada = "<?= $tipoModalidadSeleccionado ?? '' ?>";

        function cargarModalidades(idProceso, seleccionado = null) {
            modalidadSelect.innerHTML = '<option value="">Cargando...</option>';

            if (!idProceso) {
                modalidadSelect.innerHTML = '<option value="">Seleccione...</option>';
                return;
            }

            fetch('<?= BASE_URL ?>/admision/procesosAdmision/modalidadesPorProceso/' + idProceso)
                .then(response => response.json())
                .then(data => {
                    modalidadSelect.innerHTML = '<option value="">Seleccione...</option>';
                    data.forEach(m => {
                        const option = document.createElement('option');
                        option.value = m.id;
                        option.textContent = m.nombre;
                        if (seleccionado && m.id == seleccionado) {
                            option.selected = true;
                        }
                        modalidadSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalidadSelect.innerHTML = '<option value="">Error al cargar</option>';
                });
        }

        // Listener para cambios
        procesoSelect.addEventListener('change', function() {
            cargarModalidades(this.value);
        });

        // Si hay un proceso seleccionado (edición o recarga de error), cargar sus modalidades
        if (procesoSelect.value) {
            cargarModalidades(procesoSelect.value, modalidadPreseleccionada);
        }
    });
</script>