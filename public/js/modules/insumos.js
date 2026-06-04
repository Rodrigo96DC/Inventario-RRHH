let lotesEntrada = [];
const buscarDebounce = debounce(() => cargarTabla(), 350);
const buscarFacturaDebounce = debounce(() => _buscarFacturaEntrada(), 350);

document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', e => {
        const link = e.target.closest('.pagina-link');
        if (link) { e.preventDefault(); cargarTabla(link.dataset.url); }
    });
});

// ─── TABLA ───────────────────────────────────────────────────────
function cargarTabla(url = '/insumos') {
    const buscar = document.getElementById('buscador').value;
    const estado = document.getElementById('filtroEstado').value;
    const alerta = document.getElementById('filtroAlerta').checked ? 1 : '';

    axios.get(url, {
        params: { buscar, estado, alerta },
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).then(res => {
        document.getElementById('tablaContainer').innerHTML      = res.data.html;
        document.getElementById('paginacionContainer').innerHTML = res.data.pagination;
    }).catch(() => toast('Error al cargar los datos', 'error'));
}

// ─── CRUD ────────────────────────────────────────────────────────
function abrirModalNuevo() {
    limpiarModalInsumo();
    openModal('modalInsumo');
}

function editarInsumo(id) {
    axios.get(`/insumos/${id}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(res => {
            const i = res.data.data;
            document.getElementById('modalInsumoTitulo').textContent = 'Editar Insumo';
            document.getElementById('insumoId').value       = i.id;
            document.getElementById('insNombre').value      = i.nombre;
            document.getElementById('insDescripcion').value = i.descripcion ?? '';
            document.getElementById('insUnidad').value      = i.unidad_medida;
            document.getElementById('insStockMinimo').value = i.stock_minimo;
            document.getElementById('insMetodo').value      = i.metodo_valuacion;
            document.getElementById('insCategoria').value   = i.id_categoria ?? '';
            document.getElementById('insEstado').value      = i.estado;
            openModal('modalInsumo');
        })
        .catch(err => {
            console.log('error:', err.response);
            toast('Error al cargar insumo', 'error');
        });
}

function guardarInsumo() {
    const id     = document.getElementById('insumoId').value;
    const url    = id ? `/insumos/${id}` : '/insumos';
    const method = id ? 'put' : 'post';

    const data = {
        nombre:           document.getElementById('insNombre').value,
        descripcion:      document.getElementById('insDescripcion').value,
        unidad_medida:    document.getElementById('insUnidad').value,
        stock_minimo:     document.getElementById('insStockMinimo').value,
        metodo_valuacion: document.getElementById('insMetodo').value,
        id_categoria:     document.getElementById('insCategoria').value || null,
        estado:           document.getElementById('insEstado').value,
    };

    axios[method](url, data)
        .then(res => {
            toast(res.data.message, 'success');
            closeModal('modalInsumo');
            limpiarModalInsumo();
            cargarTabla();
        })
        .catch(err => {
            const errores = err.response?.data?.errors;
            toast(errores ? Object.values(errores).flat().join(' ') : 'Error al guardar', 'error');
        });
}

function limpiarModalInsumo() {
    document.getElementById('insumoId').value = '';
    document.getElementById('modalInsumoTitulo').textContent = 'Nuevo Insumo';
    document.getElementById('formInsumo').reset();
}

function toggleEstadoInsumo(id, estadoActual) {
    const mensaje = estadoActual === 'Activo' ? '¿Deshabilitar este insumo?' : '¿Habilitar este insumo?';
    if (!confirm(mensaje)) return;

    axios.patch(`/insumos/${id}/estado`)
        .then(res => { toast(res.data.message, 'success'); cargarTabla(); })
        .catch(() => toast('Error al cambiar estado', 'error'));
}

function eliminarInsumo(id) {
    if (!confirm('¿Estás seguro de eliminar este insumo?')) return;

    axios.delete(`/insumos/${id}`)
        .then(res => { toast(res.data.message, 'success'); cargarTabla(); })
        .catch(err => toast(err.response?.data?.message || 'Error al eliminar', 'error'));
}

// ─── ENTRADA MASIVA ──────────────────────────────────────────────
function abrirEntrada(id, nombre) {
    limpiarEntrada();
    document.getElementById('entradaInsumoId').value        = id;
    document.getElementById('entradaInsumoNombre').textContent = nombre;
    agregarLoteEntrada();
    openModal('modalEntrada');
}

function agregarLoteEntrada() {
    const id  = Date.now();
    const hoy = new Date().toISOString().split('T')[0];
    lotesEntrada.push({ id, cantidad: 1, fecha: hoy, precio_unitario: '', fecha_vencimiento: '', observacion: '' });
    renderizarLotesEntrada();
}

function renderizarLotesEntrada() {
    const container = document.getElementById('entradaLotesContainer');
    const vacio     = document.getElementById('entradaVacio');

    if (lotesEntrada.length === 0) {
        vacio.style.display = 'block';
        container.innerHTML = '';
        container.appendChild(vacio);
        return;
    }

    container.innerHTML = lotesEntrada.map((lote, idx) => `
        <div style="padding:12px;border-bottom:0.5px solid var(--border);">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                <span style="font-size:12px;font-weight:500;color:var(--text-secondary);">Lote ${idx + 1}</span>
                <button onclick="quitarLote(${lote.id})"
                    style="background:none;border:none;cursor:pointer;color:#ef4444;font-size:13px;">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;">
                <div class="form-group" style="margin:0;">
                    <label class="form-label" style="font-size:11px;">Cantidad *</label>
                    <input type="number" min="1" value="${lote.cantidad}"
                        class="form-control" style="padding:6px;"
                        onchange="actualizarLote(${lote.id}, 'cantidad', this.value)">
                </div>
                <div class="form-group" style="margin:0;">
                    <label class="form-label" style="font-size:11px;">Fecha ingreso *</label>
                    <input type="date" value="${lote.fecha}"
                        class="form-control" style="padding:6px;"
                        max="${new Date().toISOString().split('T')[0]}"
                        onchange="actualizarLote(${lote.id}, 'fecha', this.value)">
                </div>
                <div class="form-group" style="margin:0;">
                    <label class="form-label" style="font-size:11px;">Precio unitario</label>
                    <input type="number" min="0" value="${lote.precio_unitario}"
                        class="form-control" style="padding:6px;" placeholder="0"
                        onchange="actualizarLote(${lote.id}, 'precio_unitario', this.value)">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:8px;">
                <div class="form-group" style="margin:0;">
                    <label class="form-label" style="font-size:11px;">Fecha vencimiento</label>
                    <input type="date" value="${lote.fecha_vencimiento}"
                        class="form-control" style="padding:6px;"
                        onchange="actualizarLote(${lote.id}, 'fecha_vencimiento', this.value)">
                </div>
                <div class="form-group" style="margin:0;">
                    <label class="form-label" style="font-size:11px;">Observación</label>
                    <input type="text" value="${lote.observacion}"
                        class="form-control" style="padding:6px;" placeholder="Opcional"
                        onchange="actualizarLote(${lote.id}, 'observacion', this.value)">
                </div>
            </div>
        </div>
    `).join('');
}

function actualizarLote(id, campo, valor) {
    const lote = lotesEntrada.find(l => l.id === id);
    if (lote) lote[campo] = valor;
}

function quitarLote(id) {
    lotesEntrada = lotesEntrada.filter(l => l.id !== id);
    renderizarLotesEntrada();
}

function buscarFacturaEntrada() { buscarFacturaDebounce(); }

function _buscarFacturaEntrada() {
    const buscar = document.getElementById('entradaBuscarFactura').value;
    if (buscar.length < 2) {
        document.getElementById('entradaResultadosFactura').style.display = 'none';
        return;
    }

    axios.get('/facturas/buscar', { params: { buscar } })
        .then(res => {
            const resultados = document.getElementById('entradaResultadosFactura');
            resultados.innerHTML = '';

            if (res.data.length === 0) {
                resultados.style.display = 'none';
                return;
            }

            res.data.forEach(f => {
                const item = document.createElement('div');
                item.style.cssText = 'padding:10px 14px;cursor:pointer;font-size:13px;border-bottom:0.5px solid var(--border);';
                item.innerHTML = `<strong>${f.numero_factura}</strong> — ${f.proveedor?.razon_social ?? '—'}`;
                item.onmouseover = () => item.style.background = 'var(--hover-bg)';
                item.onmouseout  = () => item.style.background = '';
                item.onclick     = () => {
                    document.getElementById('entradaFacturaId').value = f.id;
                    document.getElementById('entradaBuscarFactura').value = '';
                    document.getElementById('entradaResultadosFactura').style.display = 'none';
                    document.getElementById('entradaFacturaSeleccionada').style.display = 'block';
                    document.getElementById('entradaFacturaSeleccionada').textContent =
                        `✓ Factura ${f.numero_factura} — ${f.proveedor?.razon_social ?? '—'}`;
                };
                resultados.appendChild(item);
            });

            resultados.style.display = 'block';
        });
}

function guardarEntrada() {
    const insumoId = document.getElementById('entradaInsumoId').value;
    const facturaId = document.getElementById('entradaFacturaId').value;

    if (lotesEntrada.length === 0) {
        toast('Debes agregar al menos un lote', 'error');
        return;
    }

    axios.post(`/insumos/${insumoId}/entrada`, {
        items:      lotesEntrada,
        id_factura: facturaId || null,
    })
        .then(res => {
            toast(res.data.message, 'success');
            closeModal('modalEntrada');
            limpiarEntrada();
            cargarTabla();
        })
        .catch(err => toast(err.response?.data?.message || 'Error al registrar', 'error'));
}

function limpiarEntrada() {
    lotesEntrada = [];
    document.getElementById('entradaInsumoId').value = '';
    document.getElementById('entradaInsumoNombre').textContent = '';
    document.getElementById('entradaBuscarFactura').value = '';
    document.getElementById('entradaFacturaId').value = '';
    document.getElementById('entradaFacturaSeleccionada').style.display = 'none';
    document.getElementById('entradaResultadosFactura').style.display = 'none';
    document.getElementById('entradaLotesContainer').innerHTML =
        '<div style="text-align:center;padding:20px;color:var(--text-secondary);font-size:13px;" id="entradaVacio">Agrega al menos un lote</div>';
}

function importarInsumos() {
    const archivo = document.getElementById('archivoCargaMasivaInsumo').files[0];
    if (!archivo) { toast('Debes seleccionar un archivo', 'error'); return; }

    const formData = new FormData();
    formData.append('archivo', archivo);

    axios.post('/plantillas/insumos/importar', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    })
        .then(res => {
            closeModal('modalCargaMasiva');
            document.getElementById('archivoCargaMasivaInsumo').value = '';
            cargarTabla();

            if (res.data.reporte) {
                toast(res.data.message, res.data.errores > 0 ? 'warning' : 'success');
                setTimeout(() => {
                    if (confirm(`${res.data.message}\n\n¿Deseas descargar el reporte detallado?`)) {
                        window.open(res.data.reporte, '_blank');
                    }
                }, 500);
            } else {
                toast(res.data.message, 'success');
            }
        })
        .catch(err => {
            toast(err.response?.data?.message || 'Error al importar', 'error');
        });
}