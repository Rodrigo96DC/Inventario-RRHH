document.addEventListener('DOMContentLoaded', () => {

    const buscarDebounce = debounce(() => cargarTabla(), 350);
    document.getElementById('buscador')?.addEventListener('input', buscarDebounce);

    document.addEventListener('click', e => {
        const link = e.target.closest('.pagina-link');
        if (link) { e.preventDefault(); cargarTabla(link.dataset.url); }
    });

});

function cargarTabla(url = '/cargos') {
    const buscar = document.getElementById('buscador').value;

    axios.get(url, {
        params: { buscar },
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).then(res => {
        document.getElementById('tablaContainer').innerHTML      = res.data.html;
        document.getElementById('paginacionContainer').innerHTML = res.data.pagination;
    }).catch(() => toast('Error al cargar los datos', 'error'));
}

function abrirModalNuevo() {
    limpiarModal();
    openModal('modalCargo');
}

function editarCargo(id) {
    axios.get(`/cargos/${id}`)
        .then(res => {
            const c = res.data.data;
            document.getElementById('modalTitulo').textContent = 'Editar Cargo';
            document.getElementById('cargoId').value           = c.id;
            document.getElementById('cNombre').value           = c.nombre;
            document.getElementById('cGrado').value            = c.grado ?? '';
            document.getElementById('cEstado').value           = c.estado;
            openModal('modalCargo');
        })
        .catch(() => toast('Error al cargar el cargo', 'error'));
}

function guardarCargo() {
    const id     = document.getElementById('cargoId').value;
    const url    = id ? `/cargos/${id}` : '/cargos';
    const method = id ? 'put' : 'post';

    const data = {
        nombre: document.getElementById('cNombre').value,
        grado:  document.getElementById('cGrado').value,
        estado: document.getElementById('cEstado').value,
    };

    axios[method](url, data)
        .then(res => {
            toast(res.data.message, 'success');
            closeModal('modalCargo');
            limpiarModal();
            cargarTabla();
        })
        .catch(err => {
            const errores = err.response?.data?.errors;
            if (errores) {
                toast(Object.values(errores).flat().join(' '), 'error');
            } else {
                toast(err.response?.data?.message || 'Error al guardar', 'error');
            }
        });
}

function limpiarModal() {
    document.getElementById('cargoId').value           = '';
    document.getElementById('modalTitulo').textContent = 'Nuevo Cargo';
    document.getElementById('formCargo').reset();
}

function toggleEstadoCargo(id, estadoActual) {
    const mensaje = estadoActual === 'Activo'
        ? '¿Deseas deshabilitar este cargo?'
        : '¿Deseas habilitar este cargo?';

    if (!confirm(mensaje)) return;

    axios.patch(`/cargos/${id}/estado`)
        .then(res => { toast(res.data.message, 'success'); cargarTabla(); })
        .catch(() => toast('Error al cambiar el estado', 'error'));
}

function importarCargos() {
    const archivo = document.getElementById('archivoCargaMasiva').files[0];
    if (!archivo) { toast('Debes seleccionar un archivo', 'error'); return; }

    const formData = new FormData();
    formData.append('archivo', archivo);

    axios.post('/plantillas/cargos/importar', formData, {
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
function eliminarCargo(id) {
    if (!confirm('¿Estás seguro de eliminar este cargo?')) return;

    axios.delete(`/cargos/${id}`)
        .then(res => {
            toast(res.data.message, 'success');
            cargarTabla();
        })
        .catch(err => {
            toast(err.response?.data?.message || 'Error al eliminar', 'error');
        });
}