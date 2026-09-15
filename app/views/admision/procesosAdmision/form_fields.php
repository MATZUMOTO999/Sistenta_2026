<div class="row">
    <div class="col-md-4 mb-2">
        <label class="form-label">Nombre *</label>
        <input type="text" name="nombre" class="form-control" required
            value="<?= htmlspecialchars($proceso['nombre'] ?? '') ?>" maxlength="20">
    </div>
    <div class="col-md-4 mb-2">
        <label class="form-label">Periodo Académico *</label>
        <select name="id_periodo" id="id_periodo" class="form-control" required>
            <option value="">Seleccione...</option>
            <?php foreach ($periodos as $p): ?>
                <option value="<?= $p['id'] ?>"
                    <?= ($periodoSeleccionado == $p['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4 mb-2">
        <label class="form-label">Sede *</label>
        <select name="id_sede" id="id_sede" class="form-control" required>
            <option value="">Seleccione...</option>

            <?php foreach ($sedes as $s): ?>
                <option value="<?= $s['id'] ?>"
                    <?= ($sedeSeleccionada == $s['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($s['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3 mb-2">
        <label class="form-label">Fecha Inicio *</label>
        <input type="date" name="fecha_inicio" class="form-control" required
            value="<?= htmlspecialchars($proceso['fecha_inicio'] ?? '') ?>">
    </div>
    <div class="col-md-3 mb-2">
        <label class="form-label">Fecha Fin *</label>
        <input type="date" name="fecha_fin" class="form-control" required
            value="<?= htmlspecialchars($proceso['fecha_fin'] ?? '') ?>">
    </div>
</div>
<div class="mb-3">
    <label class="form-label">Modalidades *</label>
    <div id="modalidades-list">
        <?php if (!empty($tiposModalidades)): ?>
            <?php foreach ($tiposModalidades as $m): ?>
                <div class="form-check form-check-inline mb-1">
                    <input class="form-check-input" type="checkbox" name="tipos_modalidades_ingreso[]" id="tiposModalidades-<?= $m['id'] ?>"
                        value="<?= $m['id'] ?>"
                        <?= (isset($proceso['tipos_modalidades_ingreso']) && in_array($m['id'], $proceso['tipos_modalidades_ingreso'])) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="tiposModalidades-<?= $m['id'] ?>"><?= htmlspecialchars($m['nombre']) ?></label>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Al editar, carga dependientes y selecciona los módulos correctos
        setTimeout(function() {
            <?php if (!empty($proceso['tipos_modalidades_ingreso'])): ?>
                setTimeout(function() {
                    var tiposModalidadesSeleccionados = <?= json_encode($proceso['tipos_modalidades_ingreso']) ?>;
                    $('#modalidades-list input[type=checkbox]').each(function() {
                        if (tiposModalidadesSeleccionados.includes(parseInt($(this).val()))) {
                            $(this).prop('checked', true);
                        }
                    });
                }, 400);
            <?php endif; ?>
        }, 200);

        // Validación del formulario antes de enviar
        const form = document.querySelector('form');
        form.addEventListener('submit', function(event) {
            const checkboxes = document.querySelectorAll('input[name="tipos_modalidades_ingreso[]"]:checked');
            if (checkboxes.length === 0) {
                event.preventDefault();
                alert('Debe seleccionar al menos una modalidad de ingreso.');
            }
        });
    });
</script>