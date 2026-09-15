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
        <label class="form-label">Modalidad *</label>
        <select name="id_modalidad_admision" id="id_modalidad_admision" class="form-control" required>
            <option value="">Seleccione...</option>
        </select>
    </div>
    <div class="col-md-4 mb-2">
        <label class="form-label">Programa Estudios *</label>
        <select name="id_programa_estudio" id="id_programa_estudio" class="form-control" required>
            <option value="">Seleccione...</option>
            <?php foreach ($programas as $p): ?>
                <option value="<?= $p['id'] ?>"
                    <?= ($programaSeleccionado == $p['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4 mb-2">
        <label class="form-label">Cantidad *</label>
        <input type="number" name="cantidad" class="form-control" required
            value="<?= htmlspecialchars($vacante['cantidad'] ?? '') ?>">
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const procesoSelect = document.getElementById('id_proceso_admision');
        const tipoModalidadSelect = document.getElementById('id_tipo_modalidad');
        const modalidadSelect = document.getElementById('id_modalidad_admision');
        const tipoModalidadPreseleccionada = "<?= $tipoModalidadSeleccionado ?? '' ?>";
        const modalidadPreseleccionada = "<?= $modalidadSeleccionado ?? '' ?>";

        function cargarTipoModalidades(idProceso, seleccionado = null) {
            tipoModalidadSelect.innerHTML = '<option value="">Cargando...</option>';

            if (!idProceso) {
                tipoModalidadSelect.innerHTML = '<option value="">Seleccione...</option>';
                return;
            }

            fetch('<?= BASE_URL ?>/admision/procesosAdmision/modalidadesPorProceso/' + idProceso)
                .then(response => response.json())
                .then(data => {
                    tipoModalidadSelect.innerHTML = '<option value="">Seleccione...</option>';

                    data.forEach(m => {
                        const option = document.createElement('option');
                        option.value = m.id;
                        option.textContent = m.nombre;
                        if (seleccionado && m.id == seleccionado) {
                            option.selected = true;
                        }
                        tipoModalidadSelect.appendChild(option);
                    });
                    modalidadSelect.innerHTML = '<option value="">Seleccione...</option>';
                })
                .catch(error => {
                    console.error('Error:', error);
                    tipoModalidadSelect.innerHTML = '<option value="">Error al cargar</option>';
                });
        }

        function cargarModalidades(idTipoModalidad, seleccionado = null) {
            modalidadSelect.innerHTML = '<option value="">Cargando...</option>';

            if (!idTipoModalidad) {
                modalidadSelect.innerHTML = '<option value="">Seleccione...</option>';
                return;
            }

            fetch('<?= BASE_URL ?>/admision/modalidades/porTipoModalidad/' + idTipoModalidad)
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
            cargarTipoModalidades(this.value);
        });

        // Si hay un proceso seleccionado (edición o recarga de error), cargar sus modalidades
        if (procesoSelect.value) {
            cargarTipoModalidades(procesoSelect.value, tipoModalidadPreseleccionada);
            setTimeout(() => {
                cargarModalidades(tipoModalidadSelect.value, modalidadPreseleccionada);
            }, 300);
        }
        tipoModalidadSelect.addEventListener('change', function() {
            cargarModalidades(this.value);
        });
        if (tipoModalidadSelect.value) {
            cargarModalidades(tipoModalidadSelect.value, modalidadPreseleccionada);
        }
    });
</script>