let modoEdicion = false;
let puestosData = [];
let drawing     = false;
let startX      = 0;
let startY      = 0;
let ctx, canvas, img, svg;

document.addEventListener('DOMContentLoaded', () => {
    img    = document.getElementById('planoImg');
    canvas = document.getElementById('overlay');
    svg    = document.getElementById('planoSvg');

    if (canvas) ctx = canvas.getContext('2d');

    if (img) {
        if (img.complete) {
            resizeCanvas();
        } else {
            img.onload = resizeCanvas;
        }
        window.addEventListener('resize', resizeCanvas);
    }

    iniciarEventosCanvas();
    cargarPuestos();
});

// ─── CANVAS ──────────────────────────────────────────────────────
function resizeCanvas() {
    if (!canvas || !img) return;
    canvas.width  = img.clientWidth;
    canvas.height = img.clientHeight;
}

function iniciarEventosCanvas() {
    if (!canvas) return;

    canvas.addEventListener('mousedown', e => {
        const rect = canvas.getBoundingClientRect();
        startX  = e.clientX - rect.left;
        startY  = e.clientY - rect.top;
        drawing = true;
    });

    canvas.addEventListener('mousemove', e => {
        if (!drawing) return;
        const rect = canvas.getBoundingClientRect();
        const x    = e.clientX - rect.left;
        const y    = e.clientY - rect.top;

        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.strokeStyle = '#1a56db';
        ctx.lineWidth   = 2;
        ctx.setLineDash([5, 3]);
        ctx.fillStyle   = 'rgba(26,86,219,0.1)';
        ctx.fillRect(startX, startY, x - startX, y - startY);
        ctx.strokeRect(startX, startY, x - startX, y - startY);
    });

  canvas.addEventListener('mouseup', e => {
    if (!drawing) return;
    drawing = false;

    const rect = canvas.getBoundingClientRect();
    const endX = e.clientX - rect.left;
    const endY = e.clientY - rect.top;

    ctx.clearRect(0, 0, canvas.width, canvas.height);

    const w = Math.abs(endX - startX);
    const h = Math.abs(endY - startY);
    if (w < 10 || h < 10) return;

    const x1 = Math.min(startX, endX);
    const y1 = Math.min(startY, endY);
    const x2 = Math.max(startX, endX);
    const y2 = Math.max(startY, endY);

    // Verificar colisión con puestos existentes
    if (hayColision(x1, y1, x2, y2)) {
        toast('Ya existe un puesto en esa área', 'warning');
        return;
    }

    const scaleX = img.naturalWidth  / img.clientWidth;
    const scaleY = img.naturalHeight / img.clientHeight;

    document.getElementById('puestoX1').value = (x1 * scaleX).toFixed(2);
    document.getElementById('puestoY1').value = (y1 * scaleY).toFixed(2);
    document.getElementById('puestoX2').value = (x2 * scaleX).toFixed(2);
    document.getElementById('puestoY2').value = (y2 * scaleY).toFixed(2);

    openModal('modalPuesto');
});
}

// ─── CARGAR PUESTOS ──────────────────────────────────────────────
function cargarPuestos() {
    const pisoId = document.getElementById('puestoIdPiso').value;

    axios.get(`/ubicaciones/piso/${pisoId}/puestos`)
        .then(res => {
            puestosData = res.data;
            renderizarPuestosSvg();
            renderizarLista();
        })
        .catch(() => toast('Error al cargar puestos', 'error'));
}

// ─── SVG PUESTOS GUARDADOS ───────────────────────────────────────
function renderizarPuestosSvg() {
    if (!svg || !img) return;

    svg.innerHTML = '';

    // Usar coordenadas de pantalla igual que el canvas
    svg.setAttribute('viewBox', `0 0 ${img.clientWidth} ${img.clientHeight}`);
    svg.style.width  = `${img.clientWidth}px`;
    svg.style.height = `${img.clientHeight}px`;

    // Escalar coordenadas guardadas (naturales) a pantalla
    const scaleX = img.clientWidth  / img.naturalWidth;
    const scaleY = img.clientHeight / img.naturalHeight;

    puestosData.forEach(p => {
        const g    = document.createElementNS('http://www.w3.org/2000/svg', 'g');
        const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');

        const colores = {
            'Disponible':   { fill: 'rgba(209,250,229,0.75)', stroke: '#059669' },
            'Ocupado':      { fill: 'rgba(191,219,254,0.75)', stroke: '#2563eb' },
            'Inhabilitado': { fill: 'rgba(241,245,249,0.75)', stroke: '#94a3b8' },
        };

        const color = colores[p.estado] ?? colores['Disponible'];

        // Convertir coordenadas naturales a pantalla
        const x1 = p.x1 * scaleX;
        const y1 = p.y1 * scaleY;
        const x2 = p.x2 * scaleX;
        const y2 = p.y2 * scaleY;
        const w  = x2 - x1;
        const h  = y2 - y1;
        const fontSize = Math.max(8, Math.min(w, h) * 0.35);

        rect.setAttribute('x',            x1);
        rect.setAttribute('y',            y1);
        rect.setAttribute('width',        w);
        rect.setAttribute('height',       h);
        rect.setAttribute('fill',         color.fill);
        rect.setAttribute('stroke',       color.stroke);
        rect.setAttribute('stroke-width', '1.5');
        rect.setAttribute('rx',           '3');

        text.setAttribute('x',           x1 + w / 2);
        text.setAttribute('y',           y1 + h / 2 + fontSize * 0.35);
        text.setAttribute('text-anchor', 'middle');
        text.setAttribute('font-size',   fontSize);
        text.setAttribute('fill',        color.stroke);
        text.setAttribute('font-weight', '600');
        text.style.pointerEvents = 'none';
        text.textContent = p.numero;

        g.style.cursor = 'pointer';
        g.appendChild(rect);
        g.appendChild(text);

        g.addEventListener('click', e => {
            e.stopPropagation();
            mostrarPopup(p);
        });

        g.addEventListener('mouseenter', () => {
            rect.setAttribute('fill', color.fill.replace('0.75', '0.95'));
            rect.setAttribute('stroke-width', '2.5');
        });
        g.addEventListener('mouseleave', () => {
            rect.setAttribute('fill', color.fill);
            rect.setAttribute('stroke-width', '1.5');
        });

        svg.appendChild(g);
    });
}

// ─── MODO EDICIÓN ────────────────────────────────────────────────
function toggleModoEdicion() {
    modoEdicion = !modoEdicion;
    const btn       = document.getElementById('btnModoEdicion');
    const indicador = document.getElementById('indicadorEdicion');

    if (modoEdicion) {
        // Activar canvas para dibujar, ocultar SVG clicks
        canvas.style.display    = 'block';
        svg.style.pointerEvents = 'none';
        btn.classList.add('btn-primary');
        btn.classList.remove('btn-secondary');
        indicador.style.display = 'flex';
    } else {
        // Desactivar canvas, activar SVG clicks
        canvas.style.display    = 'none';
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        svg.style.pointerEvents = 'all';
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-secondary');
        indicador.style.display = 'none';
    }

    renderizarLista();
}

// ─── POPUP ───────────────────────────────────────────────────────
function mostrarPopup(puesto) {
    const popup     = document.getElementById('popupPuesto');
    const titulo    = document.getElementById('popupTitulo');
    const contenido = document.getElementById('popupContenido');

    titulo.textContent = `Puesto ${puesto.numero} — Sección ${puesto.seccion ?? '—'}`;

    if (puesto.funcionario) {
        contenido.innerHTML = `
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                <div style="width:36px;height:36px;border-radius:50%;background:var(--primary-light);color:var(--primary);font-size:13px;font-weight:500;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    ${puesto.funcionario.iniciales}
                </div>
                <div>
                    <div style="font-size:13px;font-weight:500;color:var(--text-primary);">${puesto.funcionario.nombre}</div>
                    <div style="font-size:12px;color:var(--text-secondary);">${puesto.funcionario.cargo}</div>
                    <div style="font-size:12px;color:var(--text-secondary);">${puesto.funcionario.area}</div>
                </div>
            </div>
            <div style="display:flex;gap:8px;">
                <a href="/funcionarios/${puesto.funcionario.id}/perfil"
                   style="flex:1;text-align:center;padding:6px;border-radius:6px;background:var(--primary-light);color:var(--primary);font-size:12px;text-decoration:none;">
                    <i class="ti ti-user"></i> Ver perfil
                </a>
                <button onclick="liberarPuesto(${puesto.id})"
                   style="flex:1;padding:6px;border-radius:6px;background:#fee2e2;color:#991b1b;font-size:12px;border:none;cursor:pointer;">
                    <i class="ti ti-user-minus"></i> Liberar
                </button>
            </div>
        `;
    } else {
        contenido.innerHTML = `
            <div style="font-size:12px;color:var(--text-secondary);margin-bottom:12px;">
                Puesto disponible
            </div>
            <button onclick="abrirAsignar(${puesto.id}, '${puesto.numero}')"
               style="width:100%;padding:8px;border-radius:6px;background:var(--primary);color:#fff;font-size:12px;border:none;cursor:pointer;">
                <i class="ti ti-user-plus"></i> Asignar funcionario
            </button>
        `;
    }

    popup.style.display   = 'block';
    popup.style.top       = '50%';
    popup.style.left      = '50%';
    popup.style.transform = 'translate(-50%, -50%)';
}

function cerrarPopup() {
    document.getElementById('popupPuesto').style.display = 'none';
}

document.addEventListener('click', e => {
    const popup = document.getElementById('popupPuesto');
    if (popup && !popup.contains(e.target) && !e.target.closest('#planoSvg')) {
        cerrarPopup();
    }
});

// ─── LISTA LATERAL ───────────────────────────────────────────────
function renderizarLista(filtro = '') {
    const lista    = document.getElementById('listaPuestos');
    const total    = document.getElementById('totalPuestos');
    const filtrado = puestosData.filter(p =>
        p.numero.toString().includes(filtro) ||
        (p.seccion ?? '').toLowerCase().includes(filtro.toLowerCase())
    );

    total.textContent = puestosData.length;
    lista.innerHTML   = '';

    if (filtrado.length === 0) {
        lista.innerHTML = '<div style="text-align:center;padding:20px;color:var(--text-secondary);font-size:13px;">Sin resultados</div>';
        return;
    }

    const coloresDot = {
        'Disponible':   '#059669',
        'Ocupado':      '#2563eb',
        'Inhabilitado': '#94a3b8',
    };

 filtrado.forEach(p => {
    const item = document.createElement('div');
    item.style.cssText = 'display:flex;align-items:center;padding:8px 14px;gap:10px;cursor:pointer;border-bottom:0.5px solid var(--border);';
    item.onmouseover   = () => item.style.background = 'var(--hover-bg)';
    item.onmouseout    = () => item.style.background = '';
    item.onclick       = () => mostrarPopup(p);

    const coloresDot = {
        'Disponible':   '#059669',
        'Ocupado':      '#2563eb',
        'Inhabilitado': '#94a3b8',
    };

    const botonesEdicion = modoEdicion ? `
        <div style="display:flex;gap:4px;">
            <button onclick="event.stopPropagation();toggleEstadoPuesto(${p.id}, '${p.estado}')"
                style="background:none;border:none;cursor:pointer;font-size:14px;color:${p.estado === 'Inhabilitado' ? '#059669' : '#92400e'};"
                title="${p.estado === 'Inhabilitado' ? 'Habilitar' : 'Deshabilitar'}">
                <i class="ti ti-${p.estado === 'Inhabilitado' ? 'toggle-left' : 'toggle-right'}"></i>
            </button>
            <button onclick="event.stopPropagation();eliminarPuesto(${p.id})"
                style="background:none;border:none;cursor:pointer;color:#ef4444;font-size:14px;"
                title="Eliminar">
                <i class="ti ti-trash"></i>
            </button>
        </div>
    ` : '';

    item.innerHTML = `
        <div style="width:8px;height:8px;border-radius:50%;background:${coloresDot[p.estado] ?? '#94a3b8'};flex-shrink:0;"></div>
        <div style="flex:1;">
            <div style="font-size:13px;font-weight:500;color:var(--text-primary);">Puesto ${p.numero}</div>
            <div style="font-size:11px;color:var(--text-secondary);">Sección ${p.seccion ?? '—'} — ${p.estado}</div>
        </div>
        ${p.funcionario ? `<div style="font-size:11px;color:var(--primary);">${p.funcionario.nombre.split(' ')[0]}</div>` : ''}
        ${botonesEdicion}
    `;

    lista.appendChild(item);
});
}

function filtrarPuestos() {
    renderizarLista(document.getElementById('buscadorPuesto').value);
}

// ─── GUARDAR PUESTO ──────────────────────────────────────────────
function guardarPuesto() {
    const data = {
        id_piso: document.getElementById('puestoIdPiso').value,
        numero:  document.getElementById('puestoNumero').value,
        seccion: document.getElementById('puestoSeccion').value,
        x1:      document.getElementById('puestoX1').value,
        y1:      document.getElementById('puestoY1').value,
        x2:      document.getElementById('puestoX2').value,
        y2:      document.getElementById('puestoY2').value,
    };

    axios.post('/puestos', data)
        .then(res => {
            toast(res.data.message, 'success');
            closeModal('modalPuesto');
            document.getElementById('formPuesto').reset();
            cargarPuestos();
        })
        .catch(err => {
            const errores = err.response?.data?.errors;
            toast(errores ? Object.values(errores).flat().join(' ') : 'Error al guardar', 'error');
        });
}

function cancelarDibujo() {
    drawing = false;
    if (ctx) ctx.clearRect(0, 0, canvas.width, canvas.height);
}

function eliminarPuesto(id) {
    if (!confirm('¿Eliminar este puesto?')) return;
    axios.delete(`/puestos/${id}`)
        .then(res => { toast(res.data.message, 'success'); cargarPuestos(); })
        .catch(() => toast('Error al eliminar', 'error'));
}

// ─── ASIGNAR FUNCIONARIO ─────────────────────────────────────────
function abrirAsignar(puestoId, numero) {
    cerrarPopup();
    document.getElementById('asignarPuestoId').value               = puestoId;
    document.getElementById('modalAsignarTitulo').textContent       = `Asignar funcionario — Puesto ${numero}`;
    document.getElementById('asignarBuscar').value                  = '';
    document.getElementById('asignarFuncionarioId').value           = '';
    document.getElementById('asignarResultados').style.display      = 'none';
    document.getElementById('asignarFuncionarioSeleccionado').style.display = 'none';
    openModal('modalAsignarPuesto');
}

const buscarFuncionarioPuestoDebounce = debounce(() => _buscarFuncionarioPuesto(), 350);

function buscarFuncionarioPuesto() {
    buscarFuncionarioPuestoDebounce();
}

function _buscarFuncionarioPuesto() {
    const buscar = document.getElementById('asignarBuscar').value;
    if (buscar.length < 2) {
        document.getElementById('asignarResultados').style.display = 'none';
        return;
    }

    axios.get('/funcionarios', {
        params: { buscar },
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).then(res => {
        const parser     = new DOMParser();
        const doc        = parser.parseFromString(res.data.html, 'text/html');
        const rows       = doc.querySelectorAll('tbody tr');
        const resultados = document.getElementById('asignarResultados');

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
                item.onclick       = () => {
                    document.getElementById('asignarFuncionarioId').value               = id;
                    document.getElementById('asignarBuscar').value                      = '';
                    document.getElementById('asignarResultados').style.display          = 'none';
                    document.getElementById('asignarFuncionarioSeleccionado').style.display = 'block';
                    document.getElementById('asignarFuncionarioSeleccionado').textContent   = '✓ ' + nombre;
                };
                resultados.appendChild(item);
            }
        });

        resultados.style.display = encontrados > 0 ? 'block' : 'none';
    });
}

function guardarAsignacionPuesto() {
    const puestoId      = document.getElementById('asignarPuestoId').value;
    const idFuncionario = document.getElementById('asignarFuncionarioId').value;

    if (!idFuncionario) { toast('Debes seleccionar un funcionario', 'error'); return; }

    axios.post(`/puestos/${puestoId}/asignar`, {
        id_funcionario: idFuncionario,
        fecha_inicio:   document.getElementById('asignarFecha').value,
        observacion:    document.getElementById('asignarObservacion').value,
    })
        .then(res => {
            toast(res.data.message, 'success');
            closeModal('modalAsignarPuesto');
            cargarPuestos();
        })
        .catch(err => toast(err.response?.data?.message || 'Error al asignar', 'error'));
}

function liberarPuesto(puestoId) {
    if (!confirm('¿Confirmas liberar este puesto?')) return;
    cerrarPopup();

    axios.post(`/puestos/${puestoId}/liberar`)
        .then(res => { toast(res.data.message, 'success'); cargarPuestos(); })
        .catch(() => toast('Error al liberar', 'error'));
}

function hayColision(x1, y1, x2, y2) {
    return puestosData.some(p => {
        return !(x2 <= p.x1 * (img.clientWidth / img.naturalWidth) ||
                 x1 >= p.x2 * (img.clientWidth / img.naturalWidth) ||
                 y2 <= p.y1 * (img.clientHeight / img.naturalHeight) ||
                 y1 >= p.y2 * (img.clientHeight / img.naturalHeight));
    });
}

function toggleEstadoPuesto(id, estadoActual) {
    const nuevoEstado = estadoActual === 'Inhabilitado' ? 'Disponible' : 'Inhabilitado';
    const mensaje     = estadoActual === 'Inhabilitado'
        ? '¿Habilitar este puesto?'
        : '¿Deshabilitar este puesto?';

    if (!confirm(mensaje)) return;

    axios.patch(`/puestos/${id}/estado`, { estado: nuevoEstado })
        .then(res => {
            toast(res.data.message, 'success');
            cargarPuestos();
        })
        .catch(() => toast('Error al cambiar estado', 'error'));
}

// ─── LIMPIAR PISO ────────────────────────────────────────────────
function verificarFrase() {
    const frase = document.getElementById('limpiarFrase').value;
    const btn   = document.getElementById('btnConfirmarLimpiar');
    btn.disabled = frase !== 'LIMPIAR PISO';
}

function confirmarLimpiarPiso(pisoId) {
    const frase  = document.getElementById('limpiarFrase').value;
    const motivo = document.getElementById('limpiarMotivo').value;

    axios.post(`/pisos/${pisoId}/limpiar`, { frase, motivo })
        .then(res => {
            toast(res.data.message, 'success');
            closeModal('modalLimpiarPiso');
            document.getElementById('limpiarFrase').value  = '';
            document.getElementById('limpiarMotivo').value = '';
            document.getElementById('btnConfirmarLimpiar').disabled = true;
            cargarPuestos();
        })
        .catch(err => {
            toast(err.response?.data?.message || 'Error al limpiar', 'error');
        });
}

// ─── HISTORIAL Y ROLLBACK ────────────────────────────────────────
function abrirHistorial(pisoId) {
    openModal('modalHistorial');

    axios.get(`/pisos/${pisoId}/historial`)
        .then(res => {
            const contenido = document.getElementById('historialContenido');

            if (res.data.length === 0) {
                contenido.innerHTML = `
                    <div style="text-align:center;padding:32px;color:var(--text-secondary);">
                        <i class="ti ti-history" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                        Sin historial de limpiezas
                    </div>`;
                return;
            }

            contenido.innerHTML = res.data.map(h => `
                <div style="padding:14px;border-bottom:0.5px solid var(--border);">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
                        <div>
                            <div style="font-size:13px;font-weight:500;color:var(--text-primary);">
                                ${new Date(h.created_at).toLocaleDateString('es-CL')} —
                                ${h.snapshot_puestos.length} puestos
                            </div>
                            <div style="font-size:12px;color:var(--text-secondary);margin-top:2px;">
                                Por ${h.creado_por?.name ?? '—'}
                                ${h.motivo ? '— ' + h.motivo : ''}
                            </div>
                        </div>
                        ${h.revertido
                            ? `<span style="font-size:11px;padding:3px 10px;border-radius:20px;background:#d1fae5;color:#065f46;">
                                Revertido el ${new Date(h.revertido_at).toLocaleDateString('es-CL')}
                               </span>`
                            : `<button onclick="rollbackPiso(${h.id})"
                                style="padding:6px 14px;border-radius:6px;background:#fef3c7;color:#92400e;border:none;cursor:pointer;font-size:12px;">
                                <i class="ti ti-arrow-back-up"></i> Rollback
                               </button>`
                        }
                    </div>
                </div>
            `).join('');
        })
        .catch(() => toast('Error al cargar historial', 'error'));
}

function rollbackPiso(historialId) {
    if (!confirm('¿Restaurar los puestos de esta limpieza? Los puestos actuales serán reemplazados.')) return;

    axios.post(`/historial-pisos/${historialId}/rollback`)
        .then(res => {
            toast(res.data.message, 'success');
            closeModal('modalHistorial');
            cargarPuestos();
        })
        .catch(err => toast(err.response?.data?.message || 'Error al revertir', 'error'));
}