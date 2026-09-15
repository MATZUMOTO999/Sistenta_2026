<div class="row">
    <div class="col-8 row">
        <h5 class="col-12 mb-3">Información Personal</h5>
        <?php if ($IsEdit) {
        ?>
            <div class="col-md-4 mb-2">
                <label class="form-label">Código *</label>
                <div class="input-group">
                    <input type="text" class="form-control" required
                        value="<?= htmlspecialchars($inscripcion['codigo'] ?? '') ?>" readonly>
                </div>
            </div>
        <?php } ?>
        <div class="col-md-4 mb-2">
            <label class="form-label">Tipo Documento *</label>
            <div class="input-group">
                <select name="tipo_doc_postulante" id="tipo_doc_postulante"  class="form-control"  required  <?= isset($inscripcion['id']) ? 'disabled' : '' ?>>
                    <option value="DNI" <?= (isset($inscripcion['tipo_doc']) && $inscripcion['tipo_doc'] == 'DNI') ? 'selected' : '' ?>>DNI</option>
                    <option value="CE" <?= (isset($inscripcion['tipo_doc']) && $inscripcion['tipo_doc'] == 'CE') ? 'selected' : '' ?>>CARNET DE EXTRANJERIA</option>
                    <option value="PASAPORTE" <?= (isset($inscripcion['tipo_doc']) && $inscripcion['tipo_doc'] == 'PASAPORTE') ? 'selected' : '' ?>>PASAPORTE</option>
                </select>
            </div>
        </div>
        <div class="col-md-4 mb-2">
            <label class="form-label">Número de Documento*</label>
            <div class="input-group">
                <input type="text" name="dni_postulante" id="dni_postulante" class="form-control" placeholder="Ingrese número" required
                    value="<?= htmlspecialchars($inscripcion['usuario_dni'] ?? '') ?>" <?= isset($inscripcion['id']) ? 'readonly' : '' ?>>
                <?php if (!isset($inscripcion['id'])): ?>
                    <button type="button" class="btn btn-outline-primary" id="btn-buscar-dni" title="Buscar"><i class="fas fa-search"></i></button>
                <?php endif; ?>
            </div>
            <input type="hidden" name="id_usuario_sigi" id="id_usuario_sigi" value="<?= htmlspecialchars($inscripcion['id_usuario_sigi'] ?? '') ?>" required>
        </div>
        <div class="col-md-4 mb-2">
            <label class="form-label">Apellido Paterno *</label>
            <input type="text" name="apellido_paterno_postulante" id="apellido_paterno_postulante" class="form-control" required
                value="<?= htmlspecialchars($inscripcion['apellido_paterno'] ?? '') ?>" required>
        </div>
        <div class="col-md-4 mb-2">
            <label class="form-label">Apellido Materno *</label>
            <input type="text" name="apellido_materno_postulante" id="apellido_materno_postulante" class="form-control" required
                value="<?= htmlspecialchars($inscripcion['apellido_materno'] ?? '') ?>" required>
        </div>
        <div class="col-md-4 mb-2">
            <label class="form-label">Nombres *</label>
            <input type="text" name="nombres_postulante" id="nombres_postulante" class="form-control" required
                value="<?= htmlspecialchars($inscripcion['nombres'] ?? '') ?>" required>
        </div>

        <div class="col-md-4 mb-2">
            <label class="form-label">Género *</label>
            <select name="genero" id="genero" class="form-control" required>
                <option value="">Seleccione...</option>
                <option value="M" <?= (isset($inscripcion['genero']) && $inscripcion['genero'] == 'M') ? 'selected' : '' ?>>Masculino</option>
                <option value="F" <?= (isset($inscripcion['genero']) && $inscripcion['genero'] == 'F') ? 'selected' : '' ?>>Femenino</option>
            </select>
        </div>
        <div class="col-md-4 mb-2">
            <label class="form-label">Fecha Nacimiento *</label>
            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control" required
                value="<?= htmlspecialchars($inscripcion['fecha_nacimiento'] ?? '') ?>" required>
        </div>
        <div class="col-md-4 mb-2">
            <label class="form-label">Departamento Nacimiento *</label>
            <select name="departamento" id="departamento" class="form-control" required>
                <option value="">Seleccione...</option>
            </select>
        </div>
        <div class="col-md-4 mb-2">
            <label class="form-label">Provincia Nacimiento *</label>
            <select name="provincia" id="provincia" class="form-control" required>
                <option value="">Seleccione...</option>
            </select>
        </div>
        <div class="col-md-4 mb-2">
            <label class="form-label">Distrito Nacimiento*</label>
            <select name="distrito" id="distrito" class="form-control" required>
                <option value="">Seleccione...</option>
            </select>
        </div>
        <div class="col-md-4 mb-2">
            <label class="form-label">Telefono *</label>
            <input type="text" name="telefono" id="telefono" class="form-control" required
                value="<?= htmlspecialchars($inscripcion['telefono'] ?? '') ?>" required>
        </div>
        <div class="col-md-5 mb-2">
            <label class="form-label">Correo *</label>
            <input type="email" name="correo" id="correo" class="form-control" required
                value="<?= htmlspecialchars($inscripcion['correo'] ?? '') ?>" required>
        </div>
        <div class="col-md-7 mb-5">
            <label class="form-label">Dirección *</label>
            <input type="text" name="direccion" id="direccion" class="form-control" required
                value="<?= htmlspecialchars($inscripcion['direccion'] ?? '') ?>" required>
        </div>


    </div>
    <div class="col-4">
        <div class="col-md-12 mb-2">
            <label class="form-label">Foto</label>
            <div class="input-group mb-2">
                <input type="file" name="foto" id="input-foto" class="form-control" accept="image/*">
                <button type="button" class="btn btn-secondary" id="btn-activar-camara" title="Usar Cámara"><i class="fas fa-camera"></i></button>
            </div>
            <input type="hidden" name="foto_actual" value="<?= htmlspecialchars($inscripcion['foto'] ?? '') ?>">
            <input type="hidden" name="foto_capture_base64" id="foto_capture_base64">

            <?php if (!empty($inscripcion['foto'])): ?>
                <div class="mt-1" id="preview-container-actual">
                    <small>Foto Actual:</small><br>
                    <a href="<?= BASE_URL . '/' . $inscripcion['foto'] ?>" target="_blank" class="d-block mb-1">Ver Foto</a>
                    <img src="<?= BASE_URL . '/' . $inscripcion['foto'] ?>" alt="Foto actual" style="max-height: 200px;">
                </div>
            <?php endif; ?>

            <div id="camera-interface" style="display: none;" class="mt-2 border p-2 text-center bg-light rounded position-absolute start-50 top-50 translate-middle shadow" style="z-index: 1050; width: 340px;">
                <h6>Captura de Foto</h6>
                <video id="video-feed" style="width: 100%; max-width: 320px; border: 1px solid #ccc; background: #000;" autoplay playsinline></video>
                <canvas id="canvas-capture" style="display: none;"></canvas>
                <div class="mt-2">
                    <button type="button" class="btn btn-primary btn-sm" id="btn-capturar-foto">Capturar</button>
                    <button type="button" class="btn btn-danger btn-sm" id="btn-cancelar-camara">Cancelar</button>
                </div>
            </div>
            <div id="preview-capture" style="display: none;" class="mt-2 text-center border p-1 rounded">
                <small class="text-success fw-bold">Foto Capturada:</small><br>
                <img id="img-capture-preview" src="" style="max-height: 120px; border: 1px solid #28a745;" class="img-fluid rounded">
                <button type="button" class="btn btn-sm btn-outline-danger d-block mx-auto mt-1" id="btn-eliminar-captura">Eliminar Captura</button>
            </div>
        </div>
    </div>
    <div class="row">
        <h5 class="col-12 mb-3">Información Académica</h5>
        <div class="col-md-4 mb-2">
            <label class="form-label">Departamento *</label>
            <select name="departamento_colegio" id="departamento_colegio" class="form-control">
                <option value="">Seleccione...</option>
            </select>
        </div>
        <div class="col-md-4 mb-2">
            <label class="form-label">Provincia *</label>
            <select name="provincia_colegio" id="provincia_colegio" class="form-control">
                <option value="">Seleccione...</option>
            </select>
        </div>
        <div class="col-md-4 mb-2">
            <label class="form-label">Distrito *</label>
            <select name="distrito_colegio" id="distrito_colegio" class="form-control">
                <option value="">Seleccione...</option>
            </select>
        </div>
        <!-- TIPO DE EGRESO -->
        <div class="col-md-12 mb-2 text-center position-relative" id="school-search-container">
            <label class="form-label">Colegio de Procedencia *</label>

            <!-- ID real que se enviará al backend (OJO: sin espacio en el name) -->
            <input type="hidden" name="colegio_procedencia" id="codigo_modular_colegio_procedencia" value="<?= htmlspecialchars($inscripcion['colegio_procedencia'] ?? '') ?>" required>

            <!-- Input visible para buscar/mostrar texto -->
            <input type="text" class="form-control" id="txt-buscar-colegio" placeholder="Escriba codigo o nombre del colegio..." autocomplete="off" required>

            <!-- Dropdown con tabla -->
            <div class="card shadow-sm position-absolute w-100" id="school-dropdown" style="max-height: 400px; overflow: hidden; z-index: 1050; display: none;">
                <div style="max-height: 250px; overflow-y: auto;">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 10%">Código Modular</th>
                                <th style="width: 20%">Nombre</th>
                                <th style="width: 10%">Modalidad</th>
                                <th style="width: 10%">Gestión</th>
                                <th style="width: 10%">Departamento</th>
                                <th style="width: 10%">Provincia</th>
                                <th style="width: 10%">Distrito</th>
                                <th style="width: 20%">Dirección</th>
                            </tr>
                        </thead>
                        <tbody id="school-tbody">
                            <!-- Populated via AJAX -->
                        </tbody>
                    </table>
                </div>
                <div id="school-pagination" class="border-top bg-light px-2 py-1 text-end"></div>
            </div>
        </div>
        <div class="col-md-3 mb-5">
            <label class="form-label">Año Egreso *</label>
            <input type="number" name="anio_egreso_colegio" id="anio_egreso_colegio" class="form-control" required
                value="<?= htmlspecialchars($inscripcion['anio_egreso_colegio'] ?? '') ?>">
        </div>
        <h5 class="col-12 mb-3">Información de Admision</h5>
        <div class="col-md-6 mb-2">
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
        <div class="col-md-6 mb-2">
            <label class="form-label">Tipo de Modalidad *</label>
            <select name="id_tipo_modalidad" id="id_tipo_modalidad" class="form-control" required>
                <option value="">Seleccione...</option>
            </select>
        </div>
        <div class="col-md-6 mb-2">
            <label class="form-label">Modalidad *</label>
            <select name="id_modalidad_admision" id="id_modalidad_admision" class="form-control" required>
                <option value="">Seleccione...</option>
            </select>
        </div>
        <div class="col-md-6 mb-2">
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
        <div class="mb-3">
            <label class="form-label">Requisitos Adjuntos</label>
            <div id="requisitos-list">
                <?php if (!empty($requisitos)): ?>
                    <?php foreach ($requisitos as $r): ?>
                        <div class="form-check form-check-inline mb-1">
                            <input class="form-check-input" type="checkbox" name="requisitos_adjuntos[]" id="requisitos-<?= $r ?>"
                                value="<?= $r ?>"
                                <?= (isset($inscripcion['requisitos']) && in_array($r, $inscripcion['requisitos'])) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="requisitos-<?= $r ?>"><?= htmlspecialchars($r) ?></label>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>



</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // DNI Search Logic
        const btnBuscarDni = document.getElementById('btn-buscar-dni');
        const inputDni = document.getElementById('dni_postulante');
        const inputApellidosPaterno = document.getElementById('apellido_paterno_postulante');
        const inputApellidosMaterno = document.getElementById('apellido_materno_postulante');
        const inputNombres = document.getElementById('nombres_postulante');
        const inputIdUsuario = document.getElementById('id_usuario_sigi');
        const generoSelect = document.getElementById('genero');
        const fechaNacimientoInput = document.getElementById('fecha_nacimiento');
        const departamentoSelect = document.getElementById('departamento');
        const provinciaSelect = document.getElementById('provincia');
        const distritoSelect = document.getElementById('distrito');
        const telefonoInput = document.getElementById('telefono');
        const correoInput = document.getElementById('correo');
        const direccionInput = document.getElementById('direccion');

        if (btnBuscarDni) {
            btnBuscarDni.addEventListener('click', function() {
                const dni = inputDni.value.trim();
                if (dni.length !== 8) {
                    alert('El DNI debe tener 8 dígitos.');
                    return;
                }

                btnBuscarDni.disabled = true;
                btnBuscarDni.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                inputNombres.value = 'Buscando...';

                fetch('<?= BASE_URL ?>/sigi/api/buscarUsuarioApi/' + dni)
                    .then(response => response.json())
                    .then(data => {
                        btnBuscarDni.disabled = false;
                        btnBuscarDni.innerHTML = '<i class="fas fa-search"></i>';

                        if (data.success) {
                            inputApellidosPaterno.value = data.data.ApellidoPaterno;
                            inputApellidosMaterno.value = data.data.ApellidoMaterno;
                            inputNombres.value = data.data.Nombres;
                            if (data.local) {
                                inputIdUsuario.value = data.data.id;
                                generoSelect.value = data.data.genero;
                                fechaNacimientoInput.value = data.data.fecha_nacimiento;
                                telefonoInput.value = data.data.telefono;
                                correoInput.value = data.data.correo;
                                direccionInput.value = data.data.direccion;
                                const lugar_nacimiento = data.data.distrito_nacimiento;
                                const parts = lugar_nacimiento.split('-');
                                if (parts.length >= 3) {
                                    departamentoSelect.value = parts[0];
                                    if (window.ubigeoNacimiento) {
                                        window.ubigeoNacimiento.loadProvinces(parts[0], parts[1], parts[2]);
                                    }
                                }
                            } else {
                                inputIdUsuario.value = ''; // New user from API
                                generoSelect.value = '';
                                fechaNacimientoInput.value = '';
                                telefonoInput.value = '';
                                correoInput.value = '';
                                direccionInput.value = '';
                                departamentoSelect.value = '';
                                provinciaSelect.innerHTML = '<option value="">Seleccionar</option>';
                                distritoSelect.innerHTML = '<option value="">Seleccionar</option>';
                            }
                        } else {
                            inputNombres.value = '';
                            inputIdUsuario.value = '';
                            alert(data.message || 'Error al buscar DNI.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        btnBuscarDni.disabled = false;
                        btnBuscarDni.innerHTML = '<i class="fas fa-search"></i>';
                        inputNombres.value = '';
                        alert('Error de conexión.');
                    });
            });
            inputDni.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    btnBuscarDni.click();
                }
            });
        }

        // Camera Variables
        const btnActivarCamara = document.getElementById('btn-activar-camara');
        const cameraInterface = document.getElementById('camera-interface');
        const videoFeed = document.getElementById('video-feed');
        const canvasCapture = document.getElementById('canvas-capture');
        const btnCapturar = document.getElementById('btn-capturar-foto');
        const btnCancelarCamara = document.getElementById('btn-cancelar-camara');
        const previewCapture = document.getElementById('preview-capture');
        const imgCapturePreview = document.getElementById('img-capture-preview');
        const inputBase64 = document.getElementById('foto_capture_base64');
        const btnEliminarCaptura = document.getElementById('btn-eliminar-captura');
        const inputFotoFile = document.getElementById('input-foto');
        let stream = null;

        // Start Camera
        if (btnActivarCamara) {
            btnActivarCamara.addEventListener('click', async function() {
                try {
                    cameraInterface.style.display = 'block';
                    // Positioning helper (simple centered modal-like)
                    // Note: 'position-absolute start-50 top-50 translate-middle' logic requires a relative parent or body. 
                    // Using fixed positioning for modal-like behavior usually better, but keeping simple inline style for now or updating style.
                    cameraInterface.style.position = 'fixed';

                    stream = await navigator.mediaDevices.getUserMedia({
                        video: true
                    });
                    videoFeed.srcObject = stream;
                } catch (err) {
                    alert("No se pudo acceder a la cámara: " + err);
                    cameraInterface.style.display = 'none';
                }
            });
        }

        // Capture Photo
        if (btnCapturar) {
            btnCapturar.addEventListener('click', function() {
                if (stream) {
                    const context = canvasCapture.getContext('2d');
                    canvasCapture.width = videoFeed.videoWidth;
                    canvasCapture.height = videoFeed.videoHeight;
                    context.drawImage(videoFeed, 0, 0, videoFeed.videoWidth, videoFeed.videoHeight);

                    const dataURL = canvasCapture.toDataURL('image/jpeg');
                    inputBase64.value = dataURL;
                    imgCapturePreview.src = dataURL;

                    // Stop camera and show preview
                    stopCamera();
                    cameraInterface.style.display = 'none';
                    previewCapture.style.display = 'block';
                    inputFotoFile.value = ''; // Clear file input preference
                }
            });
        }

        // Cancel Camera
        if (btnCancelarCamara) {
            btnCancelarCamara.addEventListener('click', function() {
                stopCamera();
                cameraInterface.style.display = 'none';
            });
        }

        // Remove Captured Photo
        if (btnEliminarCaptura) {
            btnEliminarCaptura.addEventListener('click', function() {
                inputBase64.value = '';
                previewCapture.style.display = 'none';
                imgCapturePreview.src = '';
            });
        }

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
        }

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
            setTimeout(() => {
                cargarModalidades(this.value);
            }, 300);
        });
        if (tipoModalidadSelect.value) {
            setTimeout(() => {
                cargarModalidades(tipoModalidadSelect.value, modalidadPreseleccionada);
            }, 300);
        }

        // ================= Ubigeo Logic =================
        // ================= Ubigeo Logic Generic =================
        function setupUbigeoCascade(deptId, provId, distId, preSelDept, preSelProv, preSelDist) {
            const selectDept = document.getElementById(deptId);
            const selectProv = document.getElementById(provId);
            const selectDist = document.getElementById(distId);

            if (!selectDept || !selectProv || !selectDist) return null;

            function loadDepartments() {
                selectDept.innerHTML = '<option value="">Cargando...</option>';
                fetch('<?= BASE_URL ?>/sigi/api/apiDepartamentos')
                    .then(r => r.json())
                    .then(res => {
                        selectDept.innerHTML = '<option value="">Seleccione...</option>';
                        if (res.ok && res.data.items) {
                            res.data.items.forEach(item => {
                                const opt = document.createElement('option');
                                opt.value = item.D_DPTO;
                                opt.textContent = item.D_DPTO;
                                if (preSelDept && preSelDept === item.D_DPTO) opt.selected = true;
                                selectDept.appendChild(opt);
                            });
                            if (preSelDept && preSelDept !== '') loadProvinces(preSelDept, preSelProv, preSelDist);
                        }
                    })
                    .catch(e => {
                        console.error(e);
                        selectDept.innerHTML = '<option value="">Error</option>';
                    });
            }

            function loadProvinces(dept, prov_selected = null, dist_selected = null) {
                selectProv.innerHTML = '<option value="">Cargando...</option>';
                selectDist.innerHTML = '<option value="">Seleccione...</option>';
                fetch('<?= BASE_URL ?>/sigi/api/apiProvincias?departamento=' + encodeURIComponent(dept))
                    .then(r => r.json())
                    .then(res => {
                        selectProv.innerHTML = '<option value="">Seleccione...</option>';
                        if (res.ok && res.data.items) {
                            res.data.items.forEach(item => {
                                const opt = document.createElement('option');
                                opt.value = item.D_PROV;
                                opt.textContent = item.D_PROV;
                                if (prov_selected && prov_selected === item.D_PROV) opt.selected = true;
                                selectProv.appendChild(opt);
                            });
                            if (prov_selected && prov_selected !== '') loadDistricts(dept, prov_selected, dist_selected);
                        }
                    })
                    .catch(e => {
                        console.error(e);
                        selectProv.innerHTML = '<option value="">Error</option>';
                    });
            }

            function loadDistricts(dept, prov, dist_selected = null) {
                selectDist.innerHTML = '<option value="">Cargando...</option>';
                fetch('<?= BASE_URL ?>/sigi/api/apiDistritos?departamento=' + encodeURIComponent(dept) + '&provincia=' + encodeURIComponent(prov))
                    .then(r => r.json())
                    .then(res => {
                        selectDist.innerHTML = '<option value="">Seleccione...</option>';
                        if (res.ok && res.data.items) {
                            res.data.items.forEach(item => {
                                const opt = document.createElement('option');
                                opt.value = item.D_DIST;
                                opt.textContent = item.D_DIST;
                                if (dist_selected && dist_selected === item.D_DIST) opt.selected = true;
                                selectDist.appendChild(opt);
                            });
                        }
                    })
                    .catch(e => {
                        console.error(e);
                        selectDist.innerHTML = '<option value="">Error</option>';
                    });
            }

            selectDept.addEventListener('change', function() {
                const val = this.value;
                if (val) loadProvinces(val);
                else {
                    selectProv.innerHTML = '<option value="">Seleccione...</option>';
                    selectDist.innerHTML = '<option value="">Seleccione...</option>';
                }
            });

            selectProv.addEventListener('change', function() {
                const dep = selectDept.value;
                const prov = this.value;
                if (dep && prov) loadDistricts(dep, prov);
                else selectDist.innerHTML = '<option value="">Seleccione...</option>';
            });

            // Initial Load
            loadDepartments();

            return {
                loadDepartments,
                loadProvinces,
                loadDistricts
            };
        }

        // Initialize for Nacimiento
        window.ubigeoNacimiento = setupUbigeoCascade(
            'departamento',
            'provincia',
            'distrito',
            "<?= $inscripcion['departamento'] ?? '' ?>",
            "<?= $inscripcion['provincia'] ?? '' ?>",
            "<?= $inscripcion['distrito'] ?? '' ?>"
        );

        // Initialize for Colegio (Filters)
        window.ubigeoColegio = setupUbigeoCascade(
            'departamento_colegio',
            'provincia_colegio',
            'distrito_colegio',
            '', '', '' // No pre-selection for now
        );






        // ================= School Search Logic =================
        const departamento_colegio = document.getElementById('departamento_colegio');
        const provincia_colegio = document.getElementById('provincia_colegio');
        const distrito_colegio = document.getElementById('distrito_colegio');
        const inputSchool = document.getElementById('txt-buscar-colegio');
        const inputSchoolId = document.getElementById('codigo_modular_colegio_procedencia');
        const dropdownSchool = document.getElementById('school-dropdown');
        const tbodySchool = document.getElementById('school-tbody');
        const paginaDiv = document.getElementById('school-pagination');
        let searchTimeout;
        let currentQuery = '';

        if (inputSchool) {
            inputSchool.addEventListener('keyup', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();

                // Clear ID if user types (must select again)
                inputSchoolId.value = '';
                currentQuery = query;

                searchTimeout = setTimeout(() => {
                    searchSchools(1);
                }, 500);
            });

            // Trigger search when filters change
            if (departamento_colegio) departamento_colegio.addEventListener('change', () => searchSchools(1));
            if (provincia_colegio) provincia_colegio.addEventListener('change', () => searchSchools(1));
            if (distrito_colegio) distrito_colegio.addEventListener('change', () => searchSchools(1));

            function searchSchools(page) {
                dropdownSchool.style.display = 'block';
                tbodySchool.innerHTML = '<tr><td colspan="3" class="text-center">Buscando...</td></tr>';
                paginaDiv.innerHTML = '';

                fetch(`<?= BASE_URL ?>/sigi/api/apiColegios?data=${encodeURIComponent(currentQuery)}&page=${page}&departamento=${encodeURIComponent(departamento_colegio.value)}&provincia=${encodeURIComponent(provincia_colegio.value)}&distrito=${encodeURIComponent(distrito_colegio.value)}`)
                    .then(r => r.json())
                    .then(res => {
                        tbodySchool.innerHTML = '';
                        if (res.ok && res.data && res.data.length > 0) {
                            res.data.forEach(colegio => {
                                const tr = document.createElement('tr');
                                tr.style.cursor = 'pointer';
                                tr.innerHTML = `
                                    <td>${colegio.CodigoModular}</td>
                                    <td>${colegio.CEN_EDU}</td>
                                    <td>${colegio.D_NIV_MOD}</td>
                                    <td>${colegio.D_GESTION}</td>
                                    <td>${colegio.D_DPTO}</td>
                                    <td>${colegio.D_PROV}</td>
                                    <td>${colegio.D_DIST}</td>
                                    <td>${colegio.DIR_CEN}</td>
                                `;
                                tr.addEventListener('click', () => {
                                    inputSchool.value = `${colegio.CodigoModular} - ${colegio.CEN_EDU} (${colegio.D_DPTO} - ${colegio.D_PROV} - ${colegio.D_DIST}) - ${colegio.D_NIV_MOD} - ${colegio.D_GESTION}`;
                                    inputSchoolId.value = colegio.CodigoModular;
                                    dropdownSchool.style.display = 'none';
                                });
                                tbodySchool.appendChild(tr);
                            });
                            // Pagination Logic
                            if (res.pagination) {
                                renderPagination(res.pagination);
                            }
                        } else {
                            tbodySchool.innerHTML = '<tr><td colspan="3" class="text-center">No se encontraron resultados</td></tr>';
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        tbodySchool.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Error al buscar</td></tr>';
                    });
            }

            function renderPagination(meta) {
                const totalPages = parseInt(meta.total_paginas) || 1;
                const currentPage = parseInt(meta.pagina_actual) || 1;

                if (totalPages <= 1) return;

                const nav = document.createElement('nav');
                const ul = document.createElement('ul');
                ul.className = 'pagination pagination-sm mb-0 justify-content-end';

                const createBtn = (text, page, disabled = false, active = false) => {
                    const li = document.createElement('li');
                    li.className = 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '');
                    const a = document.createElement('a');
                    a.className = 'page-link';
                    a.href = '#';
                    a.textContent = text;
                    if (!disabled && !active) {
                        a.addEventListener('click', (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                            searchSchools(page);
                        });
                    }
                    li.appendChild(a);
                    return li;
                };

                // Prev
                ul.appendChild(createBtn('«', currentPage - 1, currentPage === 1));

                // Logic to show a window of pages
                let start = Math.max(1, currentPage - 2);
                let end = Math.min(totalPages, start + 4);
                if (end - start < 4) start = Math.max(1, end - 4);

                for (let i = start; i <= end; i++) {
                    ul.appendChild(createBtn(String(i), i, false, i === currentPage));
                }

                // Next
                ul.appendChild(createBtn('»', currentPage + 1, currentPage === totalPages));

                nav.appendChild(ul);
                paginaDiv.appendChild(nav);
            }

            // Close on click outside
            document.addEventListener('click', function(e) {
                // Check if click is inside search container OR inside one of the filter selects
                const inContainer = document.getElementById('school-search-container') && document.getElementById('school-search-container').contains(e.target);
                const isFilterDept = departamento_colegio && departamento_colegio.contains(e.target);
                const isFilterProv = provincia_colegio && provincia_colegio.contains(e.target);
                const isFilterDist = distrito_colegio && distrito_colegio.contains(e.target);

                if (!inContainer && !isFilterDept && !isFilterProv && !isFilterDist) {
                    dropdownSchool.style.display = 'none';
                }
            });

            // Pre-fill text if value exists using AJAX to get full details
            if (inputSchoolId.value) {
                inputSchool.value = "Cargando datos del colegio...";
                inputSchool.disabled = true;

                fetch(`<?= BASE_URL ?>/sigi/api/apiColegios?data=${encodeURIComponent(inputSchoolId.value)}`)
                    .then(r => r.json())
                    .then(res => {
                        inputSchool.disabled = false;
                        if (res.ok && res.data && res.data.length > 0) {
                            // Find exact match if possible, or take first
                            const colegio = res.data.find(c => c.CodigoModular === inputSchoolId.value) || res.data[0];

                            inputSchool.value = `${colegio.CodigoModular} - ${colegio.CEN_EDU} (${colegio.D_DPTO} - ${colegio.D_PROV} - ${colegio.D_DIST}) - ${colegio.D_NIV_MOD} - ${colegio.D_GESTION}`;
                        } else {
                            inputSchool.value = inputSchoolId.value; // Fallback to ID if not found
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        inputSchool.disabled = false;
                        inputSchool.value = inputSchoolId.value; // Fallback on error
                    });
            }
        }

        // Al editar, carga dependientes y selecciona los módulos correctos
        setTimeout(function() {
            <?php if (!empty($inscripcion['requisitos'])): ?>
                setTimeout(function() {
                    var requisitosSeleccionados = <?= json_encode($inscripcion['requisitos']) ?>;
                    $('#requisitos-list input[type=checkbox]').each(function() {
                        if (requisitosSeleccionados.includes($(this).val())) {
                            $(this).prop('checked', true);
                        }
                    });
                }, 400);
            <?php endif; ?>
        }, 200);
    });
</script>