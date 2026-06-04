const funcId = document.getElementById('funcPerfilId')?.value;
let funcItemsSeleccionados = [];
const buscarItemFuncDebounce = debounce(() => _buscarItemsFuncMasiva(), 350);

document.addEventListener('DOMContentLoaded', () => {
    cargarItemsFuncionario();
     cargarInsumosFuncionario(); 

    // Restaurar tab activo
    const hash = window.location.hash.replace('#', '');
    if (hash) {
        const btn = document.querySelector(`[onclick*="cambiarTab('${hash}'"]`);
        if (btn) cambiarTab(hash, btn);
    }

    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const onclick = btn.getAttribute('onclick');
            const match   = onclick?.match(/cambiarTab\('([^']+)'/);
            if (match) window.location.hash = match[1];
        });
    });
});

function cargarItemsFuncionario() {
    if (!funcId) return;

    axios.get(`/funcionarios/${funcId}/items`)
        .then(res => {
            const asigs     = res.data.asignaciones;
            const container = document.getElementById('funcItemsAsignados');
            const total     = document.getElementById('totalItemsAsignados');

            total.textContent = asigs.length;

            if (asigs.length === 0) {
                container.innerHTML = '<div style="text-align:center;padding:20px;color:var(--text-secondary);font-size:13px;">Sin ítems asignados</div>';
                return;
            }

            container.innerHTML = asigs.map(a => `
                <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:0.5px solid var(--border);">
                    <div style="flex:1;">
                        <div style="font-size:13px;font-weight:500;color:var(--text-primary);">${a.item.nombre}</div>
                        <div style="font-size:11px;color:var(--text-secondary);">
                            ${a.item.categoria?.nombre ?? '—'}
                            ${a.item.marca ? '— ' + a.item.marca : ''}
                            ${a.item.codigo_interno ? '— ' + a.item.codigo_interno : ''}
                        </div>
                    </div>
                    <span style="font-size:12px;padding:2px 8px;border-radius:20px;background:var(--primary-light);color:var(--primary);flex-shrink:0;">
                        Cant. ${a.cantidad}
                    </span>
                    <button onclick="devolverItemFuncionario(${a.id})"
                        style="background:none;border:none;cursor:pointer;color:#ef4444;font-size:14px;"
                        title="Devolver">
                        <i class="ti ti-trash"></i>
                    </button>
                </div>
            `).join('');
        });
}

function cargarInsumosFuncionario() {
    if (!funcId) return;

    axios.get(`/funcionarios/${funcId}/insumos`)
        .then(res => {
            const movimientos = res.data;
            const container   = document.getElementById('funcInsumosDespachados');
            const total       = document.getElementById('totalInsumosDespachados');

            total.textContent = movimientos.length;

            if (movimientos.length === 0) {
                container.innerHTML = '<div style="text-align:center;padding:20px;color:var(--text-secondary);font-size:13px;">Sin insumos despachados</div>';
                return;
            }

            container.innerHTML = movimientos.map(m => `
                <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:0.5px solid var(--border);">
                    <div style="flex:1;">
                        <div style="font-size:13px;font-weight:500;color:var(--text-primary);">${m.insumo.nombre}</div>
                        <div style="font-size:11px;color:var(--text-secondary);">
                            ${m.insumo.unidad_medida} — ${new Date(m.fecha + 'T12:00:00').toLocaleDateString('es-CL')}
                            ${m.observacion ? '— ' + m.observacion : ''}
                        </div>
                    </div>
                    <span style="font-size:12px;padding:2px 8px;border-radius:20px;background:var(--primary-light);color:var(--primary);flex-shrink:0;">
                        Cant. ${m.cantidad}
                    </span>
                </div>
            `).join('');
        });
}

function devolverItemFuncionario(asignacionId) {
    if (!confirm('¿Confirmas la devolución?')) return;
    axios.patch(`/asignaciones/${asignacionId}/devolver`)
        .then(res => {
            toast(res.data.message, 'success');
            cargarItemsFuncionario();
        })
        .catch(() => toast('Error al devolver', 'error'));
}

// ─── BUSCAR ÍTEMS ────────────────────────────────────────────────
function buscarItemsFuncMasiva() { buscarItemFuncDebounce(); }

function _buscarItemsFuncMasiva() {
    const buscar = document.getElementById('funcBuscarItem').value;
    if (buscar.length < 2) return;

    axios.get('/asignaciones/masiva/items', { params: { buscar } })
        .then(res => {
            const lista = document.getElementById('funcListaItems');
            lista.innerHTML = '';

            if (res.data.length === 0) {
                lista.innerHTML = '<div style="text-align:center;padding:20px;color:var(--text-secondary);font-size:13px;">Sin resultados</div>';
                return;
            }

            res.data.forEach(item => {
                const seleccionado = funcItemsSeleccionados.find(i => i.id === item.id);
                const div = document.createElement('div');
                div.style.cssText = 'display:flex;align-items:center;gap:10px;padding:10px 14px;border-bottom:0.5px solid var(--border);';
                div.innerHTML = `
                    <input type="checkbox" ${seleccionado ? 'checked' : ''}
                        style="width:15px;height:15px;flex-shrink:0;"
                       onchange="toggleItemFuncMasiva(${item.id}, '${item.nombre}', '${item.marca ?? ''}', '${item.modelo ?? ''}', '${item.codigo_interno ?? ''}', this.checked)">
                    <div style="flex:1;">
                        <div style="font-size:13px;font-weight:500;color:var(--text-primary);">${item.nombre}</div>
                        <div style="font-size:11px;color:var(--text-secondary);">
                            ${item.categoria?.nombre ?? '—'} — ${item.codigo_interno ?? 'Sin código'} — ${item.numero_serie ?? 'Sin serie'}
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:4px;">
                        <label style="font-size:11px;color:var(--text-secondary);">Cant.</label>
                        <input type="number" min="1" value="${seleccionado?.cantidad ?? 1}"
                            id="funcCantidad_${item.id}"
                            style="width:50px;padding:4px;border:0.5px solid var(--border);border-radius:6px;font-size:12px;background:var(--input-bg);color:var(--text-primary);"
                            onchange="actualizarCantidadFuncMasiva(${item.id}, this.value)">
                    </div>
                `;
                lista.appendChild(div);
            });
        });
}

function toggleItemFuncMasiva(id, nombre, marca, modelo, codigo, checked) {
    if (checked) {
        if (!funcItemsSeleccionados.find(i => i.id === id)) {
            funcItemsSeleccionados.push({ id, nombre, marca, modelo, codigo, cantidad: 1 });
        }
    } else {
        funcItemsSeleccionados = funcItemsSeleccionados.filter(i => i.id !== id);
    }
    actualizarContadorFunc();
    renderizarSeleccionadosFunc();
}

function actualizarCantidadFuncMasiva(id, cantidad) {
    const item = funcItemsSeleccionados.find(i => i.id === id);
    if (item) item.cantidad = parseInt(cantidad) || 1;
    renderizarSeleccionadosFunc();
}

function actualizarContadorFunc() {
    document.getElementById('funcContador').textContent =
        `${funcItemsSeleccionados.length} ítem(s) seleccionados`;
}

function renderizarSeleccionadosFunc() {
    const container = document.getElementById('funcSeleccionadosContainer');
    const lista     = document.getElementById('funcListaSeleccionados');

    if (funcItemsSeleccionados.length === 0) {
        container.style.display = 'none';
        return;
    }

    container.style.display = 'block';
    lista.innerHTML = funcItemsSeleccionados.map(item => `
        <div style="display:flex;align-items:center;gap:10px;padding:8px 12px;border-bottom:0.5px solid var(--border);">
            <div style="flex:1;">
                <div style="font-size:13px;font-weight:500;color:var(--text-primary);">${item.nombre}</div>
                <div style="font-size:11px;color:var(--text-secondary);">
                    ${item.marca ? item.marca : ''}
                    ${item.modelo ? '— ' + item.modelo : ''}
                    ${item.codigo ? '— ' + item.codigo : ''}
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:6px;">
                <label style="font-size:11px;color:var(--text-secondary);">Cant.</label>
                <input type="number" min="1" value="${item.cantidad}"
                    style="width:50px;padding:4px;border:0.5px solid var(--border);border-radius:6px;font-size:12px;background:var(--input-bg);color:var(--text-primary);"
                    onchange="actualizarCantidadFuncMasiva(${item.id}, this.value)">
            </div>
            <button onclick="quitarItemFuncSeleccionado(${item.id})"
                style="background:none;border:none;cursor:pointer;color:#ef4444;font-size:14px;">
                <i class="ti ti-x"></i>
            </button>
        </div>
    `).join('');
}

function quitarItemFuncSeleccionado(id) {
    funcItemsSeleccionados = funcItemsSeleccionados.filter(i => i.id !== id);
    actualizarContadorFunc();
    renderizarSeleccionadosFunc();
}

function ejecutarAsignacionFuncMasiva() {
    if (!funcId) return;
    if (funcItemsSeleccionados.length === 0) { toast('Debes seleccionar al menos un ítem', 'error'); return; }

    const fecha = document.getElementById('funcMasivaFecha').value;
    const hoy   = new Date().toISOString().split('T')[0];
    if (fecha > hoy) { toast('La fecha no puede ser mayor al día de hoy', 'error'); return; }

    axios.post('/asignaciones/masiva', {
        id_funcionario: funcId,
        items:          funcItemsSeleccionados,
        fecha:          fecha,
        observacion:    document.getElementById('funcMasivaObservacion').value,
    })
        .then(res => {
            toast(res.data.message, 'success');
            funcItemsSeleccionados = [];
            actualizarContadorFunc();
            renderizarSeleccionadosFunc();
            document.getElementById('funcListaItems').innerHTML =
                '<div style="text-align:center;padding:20px;color:var(--text-secondary);font-size:13px;">Busca un ítem para comenzar</div>';
            document.getElementById('funcBuscarItem').value = '';
            cargarItemsFuncionario();
        })
        .catch(err => toast(err.response?.data?.message || 'Error al asignar', 'error'));
}

// ─── INSUMOS ─────────────────────────────────────────────────────
let insumosSeleccionados = [];
const buscarInsumoFuncDebounce = debounce(() => _buscarInsumoFunc(), 350);

function buscarInsumoFunc() { buscarInsumoFuncDebounce(); }

function cargarInsumosFuncionario() {
    if (!funcId) return;

    axios.get(`/funcionarios/${funcId}/insumos`)
        .then(res => {
            const movimientos = res.data;
            const container   = document.getElementById('funcInsumosDespachados');
            const total       = document.getElementById('totalInsumosDespachados');

            total.textContent = movimientos.length;

            if (movimientos.length === 0) {
                container.innerHTML = '<div style="text-align:center;padding:20px;color:var(--text-secondary);font-size:13px;">Sin insumos despachados</div>';
                return;
            }

            container.innerHTML = movimientos.map(m => `
                <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:0.5px solid var(--border);">
                    <div style="flex:1;">
                        <div style="font-size:13px;font-weight:500;color:var(--text-primary);">${m.insumo.nombre}</div>
                        <div style="font-size:11px;color:var(--text-secondary);">
                            ${m.insumo.unidad_medida} — ${m.insumo.unidad_medida} — ${m.fecha.substring(0, 10).split('-').reverse().join('/')}
                            ${m.observacion ? '— ' + m.observacion : ''}
                        </div>
                    </div>
                    <span style="font-size:12px;padding:2px 8px;border-radius:20px;background:var(--primary-light);color:var(--primary);flex-shrink:0;">
                        Cant. ${m.cantidad}
                    </span>
                </div>
            `).join('');
        });
}

function _buscarInsumoFunc() {
    const buscar = document.getElementById('funcBuscarInsumo').value;
    if (buscar.length < 2) return;

    axios.get('/insumos/buscar', { params: { buscar } })
        .then(res => {
            const lista = document.getElementById('funcListaInsumos');
            lista.innerHTML = '';

            if (res.data.length === 0) {
                lista.innerHTML = '<div style="text-align:center;padding:20px;color:var(--text-secondary);font-size:13px;">Sin resultados</div>';
                return;
            }

            res.data.forEach(insumo => {
                const seleccionado = insumosSeleccionados.find(i => i.id === insumo.id);
                const div = document.createElement('div');
                div.style.cssText = 'display:flex;align-items:center;gap:10px;padding:10px 14px;border-bottom:0.5px solid var(--border);';
                div.innerHTML = `
                    <input type="checkbox" ${seleccionado ? 'checked' : ''}
                        style="width:15px;height:15px;flex-shrink:0;"
                        onchange="toggleInsumoFunc(${insumo.id}, '${insumo.nombre}', '${insumo.unidad_medida}', this.checked)">
                    <div style="flex:1;">
                        <div style="font-size:13px;font-weight:500;color:var(--text-primary);">${insumo.nombre}</div>
                        <div style="font-size:11px;color:var(--text-secondary);">
                            ${insumo.categoria?.nombre ?? '—'} —
                            <span style="color:#059669;font-weight:500;">Stock: ${insumo.stock_actual} ${insumo.unidad_medida}</span>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:4px;">
                        <label style="font-size:11px;color:var(--text-secondary);">Cant.</label>
                        <input type="number" min="1" max="${insumo.stock_actual}" value="${seleccionado?.cantidad ?? 1}"
                            id="insumoFuncCantidad_${insumo.id}"
                            style="width:50px;padding:4px;border:0.5px solid var(--border);border-radius:6px;font-size:12px;background:var(--input-bg);color:var(--text-primary);"
                            onchange="actualizarCantidadInsumoFunc(${insumo.id}, this.value)">
                    </div>
                `;
                lista.appendChild(div);
            });
        });
}

function toggleInsumoFunc(id, nombre, unidad, checked) {
    if (checked) {
        if (!insumosSeleccionados.find(i => i.id === id)) {
            insumosSeleccionados.push({ id, nombre, unidad, cantidad: 1 });
        }
    } else {
        insumosSeleccionados = insumosSeleccionados.filter(i => i.id !== id);
    }
    actualizarContadorInsumoFunc();
    renderizarInsumosSeleccionados();
}

function actualizarCantidadInsumoFunc(id, cantidad) {
    const item = insumosSeleccionados.find(i => i.id === id);
    if (item) item.cantidad = parseInt(cantidad) || 1;
    renderizarInsumosSeleccionados();
}

function actualizarContadorInsumoFunc() {
    document.getElementById('funcInsumoContador').textContent =
        `${insumosSeleccionados.length} insumo(s) seleccionados`;
}

function renderizarInsumosSeleccionados() {
    const container = document.getElementById('funcInsumosSeleccionadosContainer');
    const lista     = document.getElementById('funcInsumosSeleccionados');

    if (insumosSeleccionados.length === 0) {
        container.style.display = 'none';
        return;
    }

    container.style.display = 'block';
    lista.innerHTML = insumosSeleccionados.map(item => `
        <div style="display:flex;align-items:center;gap:10px;padding:8px 12px;border-bottom:0.5px solid var(--border);">
            <div style="flex:1;">
                <div style="font-size:13px;font-weight:500;color:var(--text-primary);">${item.nombre}</div>
                <div style="font-size:11px;color:var(--text-secondary);">${item.unidad}</div>
            </div>
            <div style="display:flex;align-items:center;gap:6px;">
                <label style="font-size:11px;color:var(--text-secondary);">Cant.</label>
                <input type="number" min="1" value="${item.cantidad}"
                    style="width:50px;padding:4px;border:0.5px solid var(--border);border-radius:6px;font-size:12px;background:var(--input-bg);color:var(--text-primary);"
                    onchange="actualizarCantidadInsumoFunc(${item.id}, this.value)">
            </div>
            <button onclick="quitarInsumoFunc(${item.id})"
                style="background:none;border:none;cursor:pointer;color:#ef4444;font-size:14px;">
                <i class="ti ti-x"></i>
            </button>
        </div>
    `).join('');
}

function quitarInsumoFunc(id) {
    insumosSeleccionados = insumosSeleccionados.filter(i => i.id !== id);
    actualizarContadorInsumoFunc();
    renderizarInsumosSeleccionados();
}

function ejecutarDespachoInsumos() {
    if (!funcId) return;
    if (insumosSeleccionados.length === 0) {
        toast('Debes seleccionar al menos un insumo', 'error');
        return;
    }

    const fecha = document.getElementById('funcInsumoFecha').value;
    const hoy   = new Date().toISOString().split('T')[0];
    if (fecha > hoy) { toast('La fecha no puede ser mayor al día de hoy', 'error'); return; }

    const promesas = insumosSeleccionados.map(item =>
        axios.post(`/insumos/${item.id}/salida`, {
            id_funcionario: funcId,
            cantidad:       item.cantidad,
            fecha:          fecha,
            observacion:    document.getElementById('funcInsumoObservacion').value,
        })
    );

    Promise.all(promesas)
        .then(() => {
            toast(`${insumosSeleccionados.length} insumo(s) despachados correctamente`, 'success');
            insumosSeleccionados = [];
            actualizarContadorInsumoFunc();
            renderizarInsumosSeleccionados();
            document.getElementById('funcListaInsumos').innerHTML =
                '<div style="text-align:center;padding:20px;color:var(--text-secondary);font-size:13px;">Busca un insumo para comenzar</div>';
            document.getElementById('funcBuscarInsumo').value = '';
            cargarInsumosFuncionario();
        })
        .catch(err => toast(err.response?.data?.message || 'Error al despachar', 'error'));
}