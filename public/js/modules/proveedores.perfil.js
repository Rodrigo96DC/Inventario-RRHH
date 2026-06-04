
const proveedorId = document.getElementById('facturaProveedorId')?.value;

document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', e => {
        const link = e.target.closest('.pagina-link-factura');
        if (link) { e.preventDefault(); cargarFacturas(link.dataset.url); }
    });
});

function cargarFacturas(url = null) {
    if (!url) url = `/proveedores/${proveedorId}/perfil`;

    axios.get(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).then(res => {
        document.getElementById('facturasContainer').innerHTML  = res.data.html;
        document.getElementById('facturasPaginacion').innerHTML = res.data.pagination;
    }).catch(() => toast('Error al cargar facturas', 'error'));
}

// ─── FACTURAS ────────────────────────────────────────────────────
function guardarFactura() {
    const id       = document.getElementById('facturaId').value;
    const url      = id ? `/facturas/${id}` : '/facturas';
    const formData = new FormData();

    if (!id) formData.append('id_proveedor', proveedorId);
    formData.append('numero_factura', document.getElementById('fNumero').value);
    formData.append('fecha_factura',  document.getElementById('fFecha').value);
    formData.append('monto_total',    document.getElementById('fMonto').value);
    formData.append('observacion',    document.getElementById('fObservacion').value);

    const archivo = document.getElementById('fArchivo').files[0];
    if (archivo) formData.append('archivo', archivo);

    axios.post(url, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    })
        .then(res => {
            toast(res.data.message, 'success');
            closeModal('modalFactura');
            limpiarModalFactura();
            cargarFacturas();
        })
        .catch(err => {
            const errores = err.response?.data?.errors;
            toast(errores ? Object.values(errores).flat().join(' ') : 'Error al guardar', 'error');
        });
}

function editarFactura(id, numero, fecha, monto, observacion) {
    document.getElementById('modalFacturaTitulo').textContent = 'Editar Factura';
    document.getElementById('facturaId').value    = id;
    document.getElementById('fNumero').value      = numero;
    document.getElementById('fFecha').value       = fecha;
    document.getElementById('fMonto').value       = monto ?? '';
    document.getElementById('fObservacion').value = observacion ?? '';
    openModal('modalFactura');
}

function eliminarFactura(id) {
    if (!confirm('¿Estás seguro de eliminar esta factura?')) return;

    axios.delete(`/facturas/${id}`)
        .then(res => { toast(res.data.message, 'success'); cargarFacturas(); })
        .catch(err => toast(err.response?.data?.message || 'Error al eliminar', 'error'));
}

function limpiarModalFactura() {
    document.getElementById('facturaId').value            = '';
    document.getElementById('modalFacturaTitulo').textContent = 'Nueva Factura';
    document.getElementById('formFactura').reset();
}

// ─── EVALUACIONES ────────────────────────────────────────────────
let estrellaSeleccionada = 0;

function abrirEvaluar(facturaId, calificacion = 0, comentario = '') {
    document.getElementById('evaluarFacturaId').value  = facturaId;
    document.getElementById('evaluarComentario').value = comentario;
    estrellaSeleccionada = calificacion;
    actualizarEstrellas(calificacion);
    openModal('modalEvaluar');
}

function seleccionarEstrella(valor) {
    estrellaSeleccionada = valor;
    document.getElementById('evaluarCalificacion').value = valor;
    actualizarEstrellas(valor);
}

function actualizarEstrellas(valor) {
    for (let i = 1; i <= 5; i++) {
        const btn = document.getElementById(`estrella${i}`);
        if (btn) {
            btn.style.color = i <= valor ? '#f59e0b' : '#d1d5db';
        }
    }
}

function guardarEvaluacion() {
    const facturaId    = document.getElementById('evaluarFacturaId').value;
    const calificacion = estrellaSeleccionada;
    const comentario   = document.getElementById('evaluarComentario').value;

    if (calificacion === 0) {
        toast('Debes seleccionar una calificación', 'error');
        return;
    }

    axios.post(`/facturas/${facturaId}/evaluar`, { calificacion, comentario })
        .then(res => {
            toast(res.data.message, 'success');
            closeModal('modalEvaluar');
            cargarFacturas();
            // Mantener tab facturas activo en vez de recargar
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            document.getElementById('tab-facturas').classList.add('active');
            document.querySelector('[onclick*="facturas"]').classList.add('active');
        })
        .catch(() => toast('Error al guardar evaluación', 'error'));
}

// Hover en estrellas
document.addEventListener('DOMContentLoaded', () => {
    for (let i = 1; i <= 5; i++) {
        const btn = document.getElementById(`estrella${i}`);
        if (!btn) continue;

        btn.addEventListener('mouseenter', () => actualizarEstrellas(i));
        btn.addEventListener('mouseleave', () => actualizarEstrellas(estrellaSeleccionada));
    }
});

function destacarFactura(facturaId) {
    // Esperar que cargue la tabla de facturas
    setTimeout(() => {
        const filas = document.querySelectorAll('#facturasContainer tbody tr');
        filas.forEach(fila => {
            if (fila.innerHTML.includes(`editarFactura(${facturaId}`)) {
                fila.style.transition = 'background 0.3s';
                fila.style.background = 'var(--primary-light)';
                fila.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => fila.style.background = '', 2000);
            }
        });
    }, 300);
}