document.addEventListener('DOMContentLoaded', () => {
    const buscarDebounce = debounce(() => cargarTabla(), 350);
    document.getElementById('filtroUsuario')?.addEventListener('input', buscarDebounce);

    document.addEventListener('click', e => {
        const link = e.target.closest('.pagina-link');
        if (link) { e.preventDefault(); cargarTabla(link.dataset.url); }
    });
});

function cargarTabla(url = '/auditoria') {
    const modelo  = document.getElementById('filtroModelo').value;
    const evento  = document.getElementById('filtroEvento').value;
    const usuario = document.getElementById('filtroUsuario').value;
    const desde   = document.getElementById('filtroDesde').value;
    const hasta   = document.getElementById('filtroHasta').value;

    axios.get(url, {
        params: { modelo, evento, usuario, fecha_desde: desde, fecha_hasta: hasta },
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).then(res => {
        document.getElementById('tablaContainer').innerHTML      = res.data.html;
        document.getElementById('paginacionContainer').innerHTML = res.data.pagination;
    }).catch(() => toast('Error al cargar los datos', 'error'));
}

function limpiarFiltros() {
    document.getElementById('filtroModelo').value  = '';
    document.getElementById('filtroEvento').value  = '';
    document.getElementById('filtroUsuario').value = '';
    document.getElementById('filtroDesde').value   = '';
    document.getElementById('filtroHasta').value   = '';
    cargarTabla();
}

function verDetalle(id) {
    axios.get(`/auditoria/${id}`)
        .then(res => {
            const log      = res.data;
            const contenido = document.getElementById('modalDetalleContenido');
            const camposOcultos = ['updated_at','created_at','updated_by','created_by','deleted_by'];

            let cambios = '';
            if (log.event === 'updated' && log.properties?.attributes) {
                const attrs = log.properties.attributes;
                const old   = log.properties.old ?? {};

                const filas = Object.entries(attrs)
                    .filter(([campo]) => !camposOcultos.includes(campo))
                    .filter(([campo, valor]) => old[campo] != valor)
                    .map(([campo, valor]) => `
                        <tr>
                            <td style="padding:8px 12px;font-size:12px;font-weight:500;color:var(--text-secondary);width:35%;">${campo}</td>
                            <td style="padding:8px 12px;font-size:12px;color:#991b1b;">${old[campo] ?? '—'}</td>
                            <td style="padding:8px 12px;font-size:12px;color:#065f46;">${valor ?? '—'}</td>
                        </tr>
                    `).join('');

                if (filas) {
                    cambios = `
                        <div style="margin-top:16px;">
                            <div style="font-size:12px;font-weight:500;color:var(--text-secondary);margin-bottom:8px;">CAMBIOS REALIZADOS</div>
                            <table style="width:100%;border-collapse:collapse;border:0.5px solid var(--border);border-radius:8px;overflow:hidden;">
                                <thead>
                                    <tr style="background:var(--hover-bg);">
                                        <th style="padding:8px 12px;font-size:11px;text-align:left;color:var(--text-secondary);">Campo</th>
                                        <th style="padding:8px 12px;font-size:11px;text-align:left;color:#991b1b;">Valor anterior</th>
                                        <th style="padding:8px 12px;font-size:11px;text-align:left;color:#065f46;">Valor nuevo</th>
                                    </tr>
                                </thead>
                                <tbody>${filas}</tbody>
                            </table>
                        </div>
                    `;
                }
            }

            // Link al elemento
            const modeloLinks = {
                'Funcionario': `/funcionarios/${log.subject_id}/perfil`,
                'Proveedor':   `/proveedores/${log.subject_id}/perfil`,
                'ItemInventario': `/inventario/${log.subject_id}/perfil`,
            };

            const modelo    = log.subject_type?.split('\\').pop();
            const linkUrl   = modeloLinks[modelo];
            const linkHtml  = linkUrl ? `
                <a href="${linkUrl}" target="_blank"
                   style="display:inline-flex;align-items:center;gap:6px;margin-top:12px;font-size:12px;color:var(--primary);">
                    <i class="ti ti-external-link"></i> Ver ${modelo}
                </a>` : '';

            contenido.innerHTML = `
                <table class="table-detail" style="margin-bottom:0;">
                    <tr><td class="detail-label">Modelo</td><td>${modelo ?? '—'}</td></tr>
                    <tr><td class="detail-label">Acción</td><td>${log.description}</td></tr>
                    <tr><td class="detail-label">Usuario</td><td>${log.causer?.name ?? 'Sistema'}</td></tr>
                    <tr><td class="detail-label">Fecha</td><td>${new Date(log.created_at).toLocaleString('es-CL')}</td></tr>
                </table>
                ${linkHtml}
                ${cambios}
            `;

            openModal('modalDetalle');
        })
        .catch(() => toast('Error al cargar detalle', 'error'));
}
function exportarAuditoria() {
    const desde  = document.getElementById('exportDesde').value;
    const hasta  = document.getElementById('exportHasta').value;
    const modelo = document.getElementById('exportModelo').value;
    const evento = document.getElementById('exportEvento').value;

    if (!desde || !hasta) {
        toast('Debes seleccionar un rango de fechas', 'error');
        return;
    }

    const fechaDesde = new Date(desde + 'T00:00:00');
    const fechaHasta = new Date(hasta + 'T00:00:00');
    const diffDias   = (fechaHasta - fechaDesde) / (1000 * 60 * 60 * 24);

    if (diffDias > 93) {
        toast('El rango máximo de exportación es 3 meses', 'error');
        return;
    }

    if (fechaHasta < fechaDesde) {
        toast('La fecha hasta debe ser mayor a la fecha desde', 'error');
        return;
    }

    const params = new URLSearchParams({ desde, hasta, modelo, evento });
    window.open(`/auditoria/exportar?${params}`, '_blank');
    closeModal('modalExportar');
}