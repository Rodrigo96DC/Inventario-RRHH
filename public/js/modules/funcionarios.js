document.addEventListener('DOMContentLoaded', () => {

    const buscarDebounce = debounce(() => cargarTabla(), 350);
    document.getElementById('buscador')?.addEventListener('input', buscarDebounce);
    document.getElementById('filtroEstado')?.addEventListener('change', () => cargarTabla());
    document.getElementById('filtroArea')?.addEventListener('change', () => cargarTabla());

    document.addEventListener('click', e => {
        const link = e.target.closest('.pagina-link');
        if (link) { e.preventDefault(); cargarTabla(link.dataset.url); }
    });

    document.getElementById('fRegion')?.addEventListener('change', e => {
        document.getElementById('fProvincia').innerHTML = '<option value="">Seleccionar provincia</option>';
        document.getElementById('fComuna').innerHTML = '<option value="">Seleccionar comuna</option>';
        if (e.target.value) cargarProvincias(e.target.value);
    });

    document.getElementById('fProvincia')?.addEventListener('change', e => {
        document.getElementById('fComuna').innerHTML = '<option value="">Seleccionar comuna</option>';
        if (e.target.value) cargarComunas(e.target.value);
    });

});

function cargarTabla(url = '/funcionarios') {
    const buscar = document.getElementById('buscador').value;
    const estado = document.getElementById('filtroEstado').value;
    const id_area = document.getElementById('filtroArea').value;

    axios.get(url, {
        params: { buscar, estado, id_area },
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).then(res => {
        document.getElementById('tablaContainer').innerHTML = res.data.html;
        document.getElementById('paginacionContainer').innerHTML = res.data.pagination;
    }).catch(() => toast('Error al cargar los datos', 'error'));
}

function abrirModalNuevo() {
    limpiarModal();
    openModal('modalFuncionario');
}

function editarFuncionario(id) {
    axios.get(`/funcionarios/${id}`)
        .then(res => {
            const f = res.data.data;
            document.getElementById('modalTitulo').textContent = 'Editar Funcionario';
            document.getElementById('funcionarioId').value = f.id;
            document.getElementById('fRut').value = f.rut;
            document.getElementById('fNombre').value = f.nombre;
            document.getElementById('fApellido').value = f.apellido;
            document.getElementById('fEmail').value = f.email ?? '';
            document.getElementById('fTelefono').value = f.telefono ?? '';
            document.getElementById('fDireccion').value = f.direccion ?? '';
            document.getElementById('fContrato').value = f.tipo_contrato ?? '';
            document.getElementById('fIngreso').value = f.fecha_ingreso ?? '';
            document.getElementById('fEstado').value = f.estado;
            document.getElementById('fArea').value = f.id_area ?? '';
            document.getElementById('fCargo').value = f.id_cargo ?? '';
            document.getElementById('fRegion').value = f.id_region ?? '';
            document.getElementById('fIngreso').value = f.fecha_ingreso ? f.fecha_ingreso.substring(0, 10) : '';
            document.getElementById('fFinContrato').value = f.fecha_fin_contrato ? f.fecha_fin_contrato.substring(0, 10) : '';

            if (f.id_region) {
                cargarProvincias(f.id_region, f.id_provincia, f.id_comuna);
            }

            openModal('modalFuncionario');
        })
        .catch(() => toast('Error al cargar el funcionario', 'error'));
}

function guardarFuncionario() {
    const id = document.getElementById('funcionarioId').value;
    const url = id ? `/funcionarios/${id}` : '/funcionarios';
    const method = id ? 'put' : 'post';

    const data = {
        rut: document.getElementById('fRut').value,
        nombre: document.getElementById('fNombre').value,
        apellido: document.getElementById('fApellido').value,
        email: document.getElementById('fEmail').value,
        telefono: document.getElementById('fTelefono').value,
        direccion: document.getElementById('fDireccion').value,
        tipo_contrato: document.getElementById('fContrato').value,
        fecha_ingreso: document.getElementById('fIngreso').value,
        fecha_fin_contrato: document.getElementById('fFinContrato').value,
        dias_aviso_contrato: document.getElementById('fDiasAviso').value,
        estado: document.getElementById('fEstado').value,
        id_area: document.getElementById('fArea').value,
        id_cargo: document.getElementById('fCargo').value,
        id_region: document.getElementById('fRegion').value,
        id_provincia: document.getElementById('fProvincia').value,
        id_comuna: document.getElementById('fComuna').value,
    };

    axios[method](url, data)
        .then(res => {
            toast(res.data.message, 'success');
            closeModal('modalFuncionario');
            limpiarModal();

            // Si estamos en el perfil recargamos la página, si estamos en el index recargamos la tabla
            if (window.location.pathname.includes('/perfil')) {
                window.location.reload();
            } else {
                cargarTabla();
            }
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
    document.getElementById('funcionarioId').value = '';
    document.getElementById('modalTitulo').textContent = 'Nuevo Funcionario';
    document.getElementById('formFuncionario').reset();
    document.getElementById('fProvincia').innerHTML = '<option value="">Seleccionar provincia</option>';
    document.getElementById('fComuna').innerHTML = '<option value="">Seleccionar comuna</option>';
}

function cargarProvincias(idRegion, idProvincia = null, idComuna = null) {
    axios.get('/funcionarios/provincias', { params: { id_region: idRegion } })
        .then(res => {
            const sel = document.getElementById('fProvincia');
            sel.innerHTML = '<option value="">Seleccionar provincia</option>';
            res.data.forEach(p => {
                sel.innerHTML += `<option value="${p.id}" ${p.id == idProvincia ? 'selected' : ''}>${p.nombre}</option>`;
            });
            if (idProvincia) cargarComunas(idProvincia, idComuna);
        });
}

function cargarComunas(idProvincia, idComuna = null) {
    axios.get('/funcionarios/comunas', { params: { id_provincia: idProvincia } })
        .then(res => {
            const sel = document.getElementById('fComuna');
            sel.innerHTML = '<option value="">Seleccionar comuna</option>';
            res.data.forEach(c => {
                sel.innerHTML += `<option value="${c.id}" ${c.id == idComuna ? 'selected' : ''}>${c.nombre}</option>`;
            });
        });
}

function toggleEstado(id, estadoActual) {
    const nuevoEstado = estadoActual === 'Inactivo' ? 'Activo' : 'Inactivo';
    const mensaje = estadoActual === 'Inactivo'
        ? '¿Deseas habilitar este funcionario?'
        : '¿Deseas deshabilitar este funcionario?';

    if (!confirm(mensaje)) return;

    axios.patch(`/funcionarios/${id}/estado`, { estado: nuevoEstado })
        .then(res => {
            toast(res.data.message, 'success');
            cargarTabla();
        })
        .catch(() => toast('Error al cambiar el estado', 'error'));
}

function eliminarFuncionario(id) {
    if (!confirm('¿Estás seguro de eliminar este funcionario? Esta acción no se puede deshacer.')) return;

    axios.delete(`/funcionarios/${id}`)
        .then(res => {
            toast(res.data.message, 'success');
            cargarTabla();
        })
        .catch(() => toast('Error al eliminar', 'error'));
}


function importarFuncionarios() {
    const archivo = document.getElementById('archivoCargaMasiva').files[0];
    if (!archivo) { toast('Debes seleccionar un archivo', 'error'); return; }

    const formData = new FormData();
    formData.append('archivo', archivo);

    axios.post('/plantillas/funcionarios/importar', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    })
        .then(res => {
            closeModal('modalCargaMasiva');
            document.getElementById('archivoCargaMasiva').value = '';
            manejarResultadoImportacion(res, () => cargarTabla());
        })
        .catch(err => {
            toast(err.response?.data?.message || 'Error al importar', 'error');
        });
}

