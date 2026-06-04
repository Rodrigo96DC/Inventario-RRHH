// ─── ASIGNACIONES ────────────────────────────────────────────────
const buscarFuncionarioAsigDebounce = debounce(() => _buscarFuncionario(), 350);

function buscarFuncionarioAsig() {
    buscarFuncionarioAsigDebounce();
}

function _buscarFuncionario() {
    const buscar = document.getElementById('asigBuscar').value;
    if (buscar.length < 2) {
        document.getElementById('asigResultados').style.display = 'none';
        return;
    }

    axios.get('/funcionarios', {
        params: { buscar },
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).then(res => {
        const parser     = new DOMParser();
        const doc        = parser.parseFromString(res.data.html, 'text/html');
        const rows       = doc.querySelectorAll('tbody tr');
        const resultados = document.getElementById('asigResultados');

        resultados.innerHTML = '';
        let encontrados = 0;

        rows.forEach(row => {
            const nombre = row.querySelector('td:first-child div div:first-child')?.textContent?.trim();
            const idBtn  = row.querySelector('button[onclick*="editarFuncionario"]')?.getAttribute('onclick');
            const id     = idBtn?.match(/\d+/)?.[0];

            if (nombre && id) {
                encontrados++;
                const item = document.createElement('div');
                item.style.cssText = 'padding:10px 14px;cursor:pointer;font-size:13px;border-bottom:0.5px solid var(--border);';
                item.textContent   = nombre;
                item.onmouseover   = () => item.style.background = 'var(--hover-bg)';
                item.onmouseout    = () => item.style.background = '';
                item.onclick       = () => seleccionarFuncionario(id, nombre);
                resultados.appendChild(item);
            }
        });

        resultados.style.display = encontrados > 0 ? 'block' : 'none';
    });
}

function seleccionarFuncionario(id, nombre) {
    document.getElementById('asigFuncionarioId').value                  = id;
    document.getElementById('asigBuscar').value                         = '';
    document.getElementById('asigResultados').style.display             = 'none';
    document.getElementById('asigFuncionarioSeleccionado').style.display = 'block';
    document.getElementById('asigFuncionarioSeleccionado').textContent   = '✓ ' + nombre;
}

function guardarAsignacion(itemId) {
    const idFuncionario = document.getElementById('asigFuncionarioId').value;
    if (!idFuncionario) { toast('Debes seleccionar un funcionario', 'error'); return; }

    const data = {
        id_item:          itemId,
        id_funcionario:   idFuncionario,
        cantidad:         document.getElementById('asigCantidad').value,
        fecha_asignacion: document.getElementById('asigFecha').value,
        observacion:      document.getElementById('asigObservacion').value,
        estado:           'Activo',
    };

    axios.post('/asignaciones', data)
        .then(res => {
            toast(res.data.message, 'success');
            closeModal('modalAsignacion');
            window.location.reload();
        })
        .catch(err => {
            toast(err.response?.data?.message || 'Error al guardar', 'error');
        });
}

function devolverAsignacion(id) {
    if (!confirm('¿Confirmas la devolución de este ítem?')) return;

    axios.patch(`/asignaciones/${id}/devolver`)
        .then(res => {
            toast(res.data.message, 'success');
            window.location.reload();
        })
        .catch(() => toast('Error al registrar devolución', 'error'));
}

// ─── MERMAS ──────────────────────────────────────────────────────
function guardarMerma(itemId) {
    const data = {
        id_item:        itemId,
        cantidad:       document.getElementById('mermaCantidad').value,
        fecha_merma:    document.getElementById('mermaFecha').value,
        descripcion:    document.getElementById('mermaDescripcion').value,
        lugar_guardado: document.getElementById('mermaLugar').value,
    };

    axios.post('/mermas', data)
        .then(res => {
            toast(res.data.message, 'success');
            closeModal('modalMerma');
            window.location.reload();
        })
        .catch(err => {
            const errores = err.response?.data?.errors;
            toast(errores ? Object.values(errores).flat().join(' ') : 'Error al guardar', 'error');
        });
}

// ─── PROGRAMAS ───────────────────────────────────────────────────
function agregarPrograma(itemId) {
    const idPrograma = document.getElementById('progId').value;
    if (!idPrograma) { toast('Debes seleccionar un programa', 'error'); return; }

    axios.post(`/inventario/${itemId}/programas`, {
        id_programa:       idPrograma,
        fecha_instalacion: document.getElementById('progFecha').value,
    })
        .then(res => {
            toast(res.data.message, 'success');
            closeModal('modalPrograma');
            window.location.reload();
        })
        .catch(err => toast(err.response?.data?.message || 'Error al agregar', 'error'));
}

function quitarPrograma(itemId, programaId) {
    if (!confirm('¿Deseas quitar este programa del equipo?')) return;

    axios.delete(`/inventario/${itemId}/programas/${programaId}`)
        .then(res => {
            toast(res.data.message, 'success');
            window.location.reload();
        })
        .catch(() => toast('Error al quitar programa', 'error'));
}

function registrarFormateo(itemId) {
    const data = {
        fecha_formato: document.getElementById('formateoFecha').value,
        observacion:   document.getElementById('formateoObservacion').value,
    };

    axios.post(`/inventario/${itemId}/formateo`, data)
        .then(res => {
            toast(res.data.message, 'success');
            closeModal('modalFormateo');
            window.location.reload();
        })
        .catch(() => toast('Error al registrar formateo', 'error'));
}