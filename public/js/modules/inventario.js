document.addEventListener('DOMContentLoaded', () => {

    const buscarDebounce = debounce(() => cargarTabla(), 350);
    document.getElementById('buscador')?.addEventListener('input', buscarDebounce);
    document.getElementById('filtroCategoria')?.addEventListener('change', () => cargarTabla());
    document.getElementById('filtroEstado')?.addEventListener('change', () => cargarTabla());

    document.addEventListener('click', e => {
        const link = e.target.closest('.pagina-link');
        if (link) { e.preventDefault(); cargarTabla(link.dataset.url); }
    });

    document.getElementById('iCategoria')?.addEventListener('change', e => {
        cargarSpecs(e.target.options[e.target.selectedIndex].dataset.nombre);
    });
     // Guardar tab activo al hacer click
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const onclick = btn.getAttribute('onclick');
            const match   = onclick?.match(/cambiarTab\('([^']+)'/);
            if (match) window.location.hash = match[1];
        });
    });

    // Restaurar tab activo al recargar
    const hash = window.location.hash.replace('#', '');
    if (hash) {
        const btn = document.querySelector(`[onclick*="cambiarTab('${hash}'"]`);
        if (btn) cambiarTab(hash, btn);
    }

});

function cargarTabla(url = '/inventario') {
    const buscar = document.getElementById('buscador').value;
    const estado = document.getElementById('filtroEstado').value;

    axios.get(url, {
        params: { buscar, estado },
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).then(res => {
        document.getElementById('tablaContainer').innerHTML      = res.data.html;
        document.getElementById('paginacionContainer').innerHTML = res.data.pagination;
    }).catch(() => toast('Error al cargar los datos', 'error'));
}

function abrirModalNuevo() {
    limpiarModal();
    openModal('modalItem');
}

function editarItem(id) {
    axios.get(`/inventario/${id}`)
        .then(res => {
            const i = res.data.data;

            document.getElementById('modalTitulo').textContent = 'Editar Ítem';
            document.getElementById('itemId').value            = i.id;
            document.getElementById('iNombre').value           = i.nombre ?? '';
            document.getElementById('iMarca').value            = i.marca ?? '';
            document.getElementById('iModelo').value           = i.modelo ?? '';
            document.getElementById('iCodigo').value           = i.codigo_interno ?? '';
            document.getElementById('iSerie').value            = i.numero_serie ?? '';
            document.getElementById('iPrecio').value           = i.precio ?? '';
            document.getElementById('iFecha').value            = i.fecha_adquisicion ? i.fecha_adquisicion.substring(0, 10) : '';
            document.getElementById('iUbicacion').value        = i.ubicacion ?? '';
            document.getElementById('iObservacion').value      = i.observacion ?? '';
            document.getElementById('iEstado').value           = i.estado ?? 'Disponible';

            // Marcar categorías
            document.querySelectorAll('.cat-adicional').forEach(cb => cb.checked = false);
            const catPrincipal = document.querySelector(`.cat-adicional[value="${i.id_categoria}"]`);
            if (catPrincipal) catPrincipal.checked = true;
            if (i.categorias) {
                i.categorias.forEach(cat => {
                    const cb = document.querySelector(`.cat-adicional[value="${cat.id}"]`);
                    if (cb) cb.checked = true;
                });
            }

            // Abrir modal
            openModal('modalItem');

            // Cargar specs
            if (i.categoria?.nombre) {
                cargarSpecs(i.categoria.nombre, i);
            }
        })
        .catch(() => toast('Error al cargar el ítem', 'error'));
}

function cargarSpecs(categoria, datos = null) {
    const container = document.getElementById('specsContainer');

    const specsMap = {
        'Computacional': '/inventario/partials/specs/computacional',
        'Mobiliario': '/inventario/partials/specs/mobiliario',
        'Vehículo': '/inventario/partials/specs/vehiculo',
        'Herramienta': '/inventario/partials/specs/herramienta',
        'Insumo': '/inventario/partials/specs/insumo',
    };

    if (!specsMap[categoria]) {
        container.innerHTML = '';
        return;
    }

    axios.get(specsMap[categoria])
        .then(res => {
            container.innerHTML = res.data;
            if (datos) llenarSpecs(categoria, datos);
        })
        .catch(() => container.innerHTML = '');
}

function llenarSpecs(categoria, datos) {
    const specs = datos[`specs_${categoria.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '')}`];
    if (!specs) return;

    const campos = {
        'Computacional': {
            sNumeroInventario: 'numero_inventario',
            sProcesador: 'procesador', sRam: 'ram',
            sTipoDisco: 'tipo_disco', sCapacidadDisco: 'capacidad_disco',
            sMac: 'mac', sIp: 'ip', sHostname: 'hostname', sEntradas: 'entradas',
        },
        'Mobiliario': { sMaterial: 'material', sColor: 'color', sDimensiones: 'dimensiones' },
        'Vehículo': {
            sPatente: 'patente', sAnio: 'anio', sTipoCombustible: 'tipo_combustible',
            sKilometraje: 'kilometraje', sNumeroMotor: 'numero_motor', sNumeroChasis: 'numero_chasis',
        },
        'Herramienta': { sTipo: 'tipo', sMaterial: 'material', sMedida: 'medida' },
        'Insumo': {
            sStockInicial: 'stock_inicial', sStockActual: 'stock_actual',
            sUnidadMedida: 'unidad_medida', sStockMinimo: 'stock_minimo',
        },
    };

    const mapa = campos[categoria] ?? {};
    Object.entries(mapa).forEach(([elId, campo]) => {
        const el = document.getElementById(elId);
        if (el && specs[campo] !== null) el.value = specs[campo] ?? '';
    });
}

function guardarItem() {
    const id = document.getElementById('itemId').value;
    const url = id ? `/inventario/${id}` : '/inventario';

    const formData = new FormData();
    if (id) formData.append('_method', 'PUT');

    // Categorías — primera marcada es la principal
    const catsAdicionales = document.querySelectorAll('.cat-adicional:checked');

    if (catsAdicionales.length === 0) {
        toast('Debes seleccionar al menos una categoría', 'error');
        return;
    }

    // Primera = principal, resto = adicionales
    formData.append('id_categoria', catsAdicionales[0].value);
    catsAdicionales.forEach((cat, index) => {
        if (index > 0) formData.append('categorias_adicionales[]', cat.value);
    });

    const campos = {
        nombre: 'iNombre',
        marca: 'iMarca',
        modelo: 'iModelo',
        codigo_interno: 'iCodigo',
        numero_serie: 'iSerie',
        precio: 'iPrecio',
        fecha_adquisicion: 'iFecha',
        ubicacion: 'iUbicacion',
        observacion: 'iObservacion',
        estado: 'iEstado',
        procesador: 'sProcesador',
        ram: 'sRam',
        tipo_disco: 'sTipoDisco',
        capacidad_disco: 'sCapacidadDisco',
        mac: 'sMac',
        ip: 'sIp',
        hostname: 'sHostname',
        numero_inventario: 'sNumeroInventario',
        entradas: 'sEntradas',
        material: 'sMaterial',
        color: 'sColor',
        dimensiones: 'sDimensiones',
        patente: 'sPatente',
        anio: 'sAnio',
        tipo_combustible: 'sTipoCombustible',
        kilometraje: 'sKilometraje',
        numero_motor: 'sNumeroMotor',
        numero_chasis: 'sNumeroChasis',
        tipo: 'sTipo',
        medida: 'sMedida',
        stock_inicial: 'sStockInicial',
        stock_actual: 'sStockActual',
        unidad_medida: 'sUnidadMedida',
        stock_minimo: 'sStockMinimo',
    };

    Object.entries(campos).forEach(([campo, elId]) => {
        const el = document.getElementById(elId);
        if (el) formData.append(campo, el.value);
    });

    const imagen = document.getElementById('iImagen')?.files[0];
    if (imagen) formData.append('imagen', imagen);

    axios.post(url, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    })
        .then(res => {
            toast(res.data.message, 'success');
            closeModal('modalItem');
            limpiarModal();
            cargarTabla();
        })
        .catch(err => {
            const errores = err.response?.data?.errors;
            if (errores) {
                toast(Object.values(errores).flat().join(' '), 'error');
            } else {
                toast('Error al guardar', 'error');
            }
        });
}

function limpiarModal() {
    document.getElementById('itemId').value = '';
    document.getElementById('modalTitulo').textContent = 'Nuevo Ítem';
    document.getElementById('formItem').reset();
    document.getElementById('specsContainer').innerHTML = '';
    document.querySelectorAll('.cat-adicional').forEach(cb => cb.checked = false);
}

function eliminarItem(id) {
    if (!confirm('¿Estás seguro de eliminar este ítem?')) return;
    axios.delete(`/inventario/${id}`)
        .then(res => { toast(res.data.message, 'success'); cargarTabla(); })
        .catch(() => toast('Error al eliminar', 'error'));
}

// ─── ASIGNACIONES ────────────────────────────────────────────────
const buscarFuncionarioDebounce = debounce(() => _buscarFuncionario(), 350);

function buscarFuncionarioAsig() {
    buscarFuncionarioDebounce();
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
        const parser = new DOMParser();
        const doc = parser.parseFromString(res.data.html, 'text/html');
        const rows = doc.querySelectorAll('tbody tr');
        const resultados = document.getElementById('asigResultados');

        resultados.innerHTML = '';
        let encontrados = 0;

        rows.forEach(row => {
            const nombre = row.querySelector('td:first-child div div:first-child')?.textContent?.trim();
            const idBtn = row.querySelector('button[onclick*="editarFuncionario"]')?.getAttribute('onclick');
            const id = idBtn?.match(/\d+/)?.[0];

            if (nombre && id) {
                encontrados++;
                const item = document.createElement('div');
                item.style.cssText = 'padding:10px 14px;cursor:pointer;font-size:13px;border-bottom:0.5px solid var(--border);';
                item.textContent = nombre;
                item.onmouseover = () => item.style.background = 'var(--hover-bg)';
                item.onmouseout = () => item.style.background = '';
                item.onclick = () => seleccionarFuncionario(id, nombre);
                resultados.appendChild(item);
            }
        });

        resultados.style.display = encontrados > 0 ? 'block' : 'none';
    });
}

function seleccionarFuncionario(id, nombre) {
    document.getElementById('asigFuncionarioId').value = id;
    document.getElementById('asigBuscar').value = '';
    document.getElementById('asigResultados').style.display = 'none';
    document.getElementById('asigFuncionarioSeleccionado').style.display = 'block';
    document.getElementById('asigFuncionarioSeleccionado').textContent = '✓ ' + nombre;
}

function guardarAsignacion(itemId) {
    const idFuncionario = document.getElementById('asigFuncionarioId').value;
    if (!idFuncionario) { toast('Debes seleccionar un funcionario', 'error'); return; }

    const data = {
        id_item: itemId,
        id_funcionario: idFuncionario,
        cantidad: document.getElementById('asigCantidad').value,
        fecha_asignacion: document.getElementById('asigFecha').value,
        observacion: document.getElementById('asigObservacion').value,
        estado: 'Activo',
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
        id_item: itemId,
        cantidad: document.getElementById('mermaCantidad').value,
        fecha_merma: document.getElementById('mermaFecha').value,
        descripcion: document.getElementById('mermaDescripcion').value,
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
        id_programa: idPrograma,
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
        observacion: document.getElementById('formateoObservacion').value,
    };

    axios.post(`/inventario/${itemId}/formateo`, data)
        .then(res => {
            toast(res.data.message, 'success');
            closeModal('modalFormateo');
            window.location.reload();
        })
        .catch(() => toast('Error al registrar formateo', 'error'));
}


// ─── ASIGNACIÓN MASIVA ───────────────────────────────────────────

const buscarFuncMasivaDebounce = debounce(() => _buscarFuncionarioMasiva(), 350);
const buscarItemMasivaDebounce = debounce(() => _buscarItemsMasiva(), 350);

function buscarFuncionarioMasiva() { buscarFuncMasivaDebounce(); }
function buscarItemsMasiva()       { buscarItemMasivaDebounce(); }

function _buscarFuncionarioMasiva() {
    const buscar = document.getElementById('masivaBuscarFuncionario').value;
    if (buscar.length < 2) {
        document.getElementById('masivaResultadosFuncionario').style.display = 'none';
        return;
    }

    axios.get('/funcionarios', {
        params: { buscar },
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).then(res => {
        const parser     = new DOMParser();
        const doc        = parser.parseFromString(res.data.html, 'text/html');
        const rows       = doc.querySelectorAll('tbody tr');
        const resultados = document.getElementById('masivaResultadosFuncionario');

        resultados.innerHTML = '';
        let encontrados = 0;

        rows.forEach(row => {
            const divs   = row.querySelectorAll('td:first-child div div');
            const nombre = divs[0]?.textContent?.trim();
            const rut    = divs[1]?.textContent?.trim();
            const idBtn  = row.querySelector('button[onclick*="editarFuncionario"]')?.getAttribute('onclick');
            const id     = idBtn?.match(/\d+/)?.[0];

            if (nombre && id) {
                encontrados++;
                const item = document.createElement('div');
                item.style.cssText = 'padding:10px 14px;cursor:pointer;border-bottom:0.5px solid var(--border);';
                item.innerHTML = `
                    <div style="font-size:13px;font-weight:500;color:var(--text-primary);">${nombre}</div>
                    <div style="font-size:11px;color:var(--text-secondary);">${rut ?? ''}</div>
                `;
                item.onmouseover = () => item.style.background = 'var(--hover-bg)';
                item.onmouseout  = () => item.style.background = '';
                item.onclick     = () => seleccionarFuncionarioMasiva(id);
                resultados.appendChild(item);
            }
        });

        resultados.style.display = encontrados > 0 ? 'block' : 'none';
    });
}

function seleccionarFuncionarioMasiva(id) {
    document.getElementById('masivaResultadosFuncionario').style.display = 'none';
    document.getElementById('masivaBuscarFuncionario').value = '';

    axios.get(`/asignaciones/masiva/funcionario/${id}`)
        .then(res => {
            const f = res.data.funcionario;
            document.getElementById('masivaFuncionarioId').value    = f.id;
            document.getElementById('masivaFuncNombre').textContent = f.nombre;
            document.getElementById('masivaFuncCargo').textContent  = f.cargo;
            document.getElementById('masivaFuncIniciales').textContent = f.iniciales;
            document.getElementById('masivaFuncionarioSeleccionado').style.display = 'block';

            // Mostrar ítems asignados
            const container = document.getElementById('masivaItemsAsignados');
            const asigs     = res.data.asignaciones;

            if (asigs.length === 0) {
                container.innerHTML = '<div style="text-align:center;padding:20px;color:var(--text-secondary);font-size:13px;">Sin ítems asignados</div>';
                return;
            }

      container.innerHTML = asigs.map(a => `
    <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:0.5px solid var(--border);">
        <div style="flex:1;">
            <div style="font-size:13px;font-weight:500;color:var(--text-primary);">${a.item.nombre}</div>
            <div style="font-size:11px;color:var(--text-secondary);">
                ${a.item.categoria?.nombre ?? '—'} — ${a.item.codigo_interno ?? 'Sin código'}
            </div>
        </div>
        <span style="font-size:12px;padding:2px 8px;border-radius:20px;background:var(--primary-light);color:var(--primary);flex-shrink:0;">
            Cant. ${a.cantidad}
        </span>
        <button onclick="devolverAsignacionMasiva(${a.id})"
            style="background:none;border:none;cursor:pointer;color:#ef4444;font-size:14px;"
            title="Devolver">
            <i class="ti ti-trash"></i>
        </button>
    </div>
`).join('');
        })
        .catch(() => toast('Error al cargar funcionario', 'error'));
}

function limpiarFuncionarioMasiva() {
    document.getElementById('masivaFuncionarioId').value = '';
    document.getElementById('masivaFuncionarioSeleccionado').style.display = 'none';
    document.getElementById('masivaBuscarFuncionario').value = '';
}

function _buscarItemsMasiva() {
    const buscar = document.getElementById('masivaBuscarItem').value;
    if (buscar.length < 2) return;

    axios.get('/asignaciones/masiva/items', { params: { buscar } })
        .then(res => {
            const lista = document.getElementById('masivaListaItems');
            lista.innerHTML = '';

            if (res.data.length === 0) {
                lista.innerHTML = '<div style="text-align:center;padding:20px;color:var(--text-secondary);font-size:13px;">Sin resultados</div>';
                return;
            }

         res.data.forEach(item => {
    const seleccionado = itemsSeleccionados.find(i => i.id === item.id);
    const div = document.createElement('div');
    div.style.cssText = `display:flex;align-items:center;gap:10px;padding:10px 14px;border-bottom:0.5px solid var(--border);`;
 div.innerHTML = `
    <input type="checkbox" ${seleccionado ? 'checked' : ''}
        style="width:15px;height:15px;flex-shrink:0;"
        onchange="toggleItemMasiva(${item.id}, '${item.nombre}', '${item.marca ?? ''}', '${item.modelo ?? ''}', '${item.codigo_interno ?? ''}', this.checked)">
    <div style="flex:1;">
        <div style="font-size:13px;font-weight:500;color:var(--text-primary);">${item.nombre}</div>
        <div style="font-size:11px;color:var(--text-secondary);">
            ${item.categoria?.nombre ?? '—'}
            ${item.marca ? '— ' + item.marca : ''}
            ${item.modelo ? '— ' + item.modelo : ''}
            ${item.codigo_interno ? '— ' + item.codigo_interno : '— Sin código'}
            ${item.numero_serie ? '— ' + item.numero_serie : '— Sin serie'}
        </div>
    </div>
    <div style="display:flex;align-items:center;gap:4px;">
        <label style="font-size:11px;color:var(--text-secondary);">Cant.</label>
        <input type="number" min="1" value="${seleccionado?.cantidad ?? 1}"
            id="cantidad_${item.id}"
            style="width:50px;padding:4px;border:0.5px solid var(--border);border-radius:6px;font-size:12px;background:var(--input-bg);color:var(--text-primary);"
            onchange="actualizarCantidadMasiva(${item.id}, this.value)">
    </div>
`;
    lista.appendChild(div);
});
        });
}

let itemsSeleccionados = []; // ahora guarda objetos {id, cantidad}

function toggleItemMasiva(id, nombre, marca, modelo, codigo, checked) {
    if (checked) {
        if (!itemsSeleccionados.find(i => i.id === id)) {
            itemsSeleccionados.push({ id, nombre, marca, modelo, codigo, cantidad: 1 });
        }
    } else {
        itemsSeleccionados = itemsSeleccionados.filter(i => i.id !== id);
    }
    actualizarContador();
    renderizarSeleccionados();
}

function renderizarSeleccionados() {
    const container = document.getElementById('masivaSeleccionadosContainer');
    const lista     = document.getElementById('masivaListaSeleccionados');

    if (itemsSeleccionados.length === 0) {
        container.style.display = 'none';
        return;
    }

    container.style.display = 'block';
    lista.innerHTML = itemsSeleccionados.map(item => `
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
                    onchange="actualizarCantidadMasiva(${item.id}, this.value)">
            </div>
            <button onclick="quitarItemSeleccionado(${item.id})"
                style="background:none;border:none;cursor:pointer;color:#ef4444;font-size:14px;">
                <i class="ti ti-x"></i>
            </button>
        </div>
    `).join('');
}

function quitarItemSeleccionado(id) {
    itemsSeleccionados = itemsSeleccionados.filter(i => i.id !== id);
    actualizarContador();
    renderizarSeleccionados();
    // Desmarcar checkbox si está visible
    const checkbox = document.querySelector(`input[onchange*="toggleItemMasiva(${id}"]`);
    if (checkbox) checkbox.checked = false;
}

function actualizarCantidadMasiva(id, cantidad) {
    const item = itemsSeleccionados.find(i => i.id === id);
    if (item) item.cantidad = parseInt(cantidad) || 1;
}

function actualizarContador() {
    document.getElementById('masivaContador').textContent =
        `${itemsSeleccionados.length} ítem(s) seleccionados`;
}

function devolverAsignacionMasiva(asignacionId) {
    if (!confirm('¿Confirmas la devolución?')) return;
    axios.patch(`/asignaciones/${asignacionId}/devolver`)
        .then(res => {
            toast(res.data.message, 'success');
            const funcId = document.getElementById('masivaFuncionarioId').value;
            seleccionarFuncionarioMasiva(funcId);
        })
        .catch(() => toast('Error al devolver', 'error'));
}

function ejecutarAsignacionMasiva() {
    const idFuncionario = document.getElementById('masivaFuncionarioId').value;
    if (!idFuncionario) { toast('Debes seleccionar un funcionario', 'error'); return; }
    if (itemsSeleccionados.length === 0) { toast('Debes seleccionar al menos un ítem', 'error'); return; }

    const fecha      = document.getElementById('masivaFecha').value;
const hoy        = new Date().toISOString().split('T')[0];

if (fecha > hoy) {
    toast('La fecha de asignación no puede ser mayor al día de hoy', 'error');
    return;
}
    axios.post('/asignaciones/masiva', {
        id_funcionario: idFuncionario,
        items:          itemsSeleccionados,
        fecha:          document.getElementById('masivaFecha').value,
        observacion:    document.getElementById('masivaObservacion').value,
    })
      .then(res => {
    toast(res.data.message, 'success');
    itemsSeleccionados = [];
    actualizarContador();
    renderizarSeleccionados();
    document.getElementById('masivaListaItems').innerHTML =
        '<div style="text-align:center;padding:20px;color:var(--text-secondary);font-size:13px;">Busca un ítem para comenzar</div>';
    document.getElementById('masivaBuscarItem').value = '';
    seleccionarFuncionarioMasiva(idFuncionario);
})
        .catch(err => toast(err.response?.data?.message || 'Error al asignar', 'error'));
}

function actualizarLinkPlantilla() {
    const tipo = document.getElementById('cargaTipo').value;
    const link = document.getElementById('cargaPlantillaLink');
    const href = document.getElementById('cargaPlantillaHref');

    if (tipo) {
        link.style.display = 'block';
        href.href = `/plantillas/${tipo}/descargar`;
    } else {
        link.style.display = 'none';
    }
}

function importarInventario() {
    const tipo    = document.getElementById('cargaTipo').value;
    const archivo = document.getElementById('archivoCargaMasiva').files[0];

    if (!tipo)    { toast('Debes seleccionar un tipo de ítem', 'error'); return; }
    if (!archivo) { toast('Debes seleccionar un archivo', 'error'); return; }

    const formData = new FormData();
    formData.append('archivo', archivo);

    axios.post(`/plantillas/${tipo}/importar`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    })
        .then(res => {
            closeModal('modalCargaMasiva');
            document.getElementById('cargaTipo').value = '';
            document.getElementById('archivoCargaMasiva').value = '';
            document.getElementById('cargaPlantillaLink').style.display = 'none';
            cargarTabla();

            // Mostrar resultado con opción de descarga
            if (res.data.reporte) {
                const msg = `${res.data.message}`;
                toast(msg, res.data.errores > 0 ? 'warning' : 'success');

                // Ofrecer descarga del reporte
                setTimeout(() => {
                    if (confirm(`${msg}\n\n¿Deseas descargar el reporte detallado?`)) {
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