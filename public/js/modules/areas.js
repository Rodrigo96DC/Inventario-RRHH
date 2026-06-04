document.addEventListener('DOMContentLoaded', () => {

    const buscarDebounce = debounce(() => cargarTabla(), 350);
    document.getElementById('buscador')?.addEventListener('input', buscarDebounce);

    document.addEventListener('click', e => {
        const link = e.target.closest('.pagina-link');
        if (link) { e.preventDefault(); cargarTabla(link.dataset.url); }
    });

});

function cargarTabla(url = '/areas') {
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
    openModal('modalArea');
}

function editarArea(id) {
    axios.get(`/areas/${id}`)
        .then(res => {
            const a = res.data.data;
            document.getElementById('modalTitulo').textContent = 'Editar Área';
            document.getElementById('areaId').value            = a.id;
            document.getElementById('aNombre').value           = a.nombre;
            document.getElementById('aEstado').value           = a.estado;
            openModal('modalArea');
        })
        .catch(() => toast('Error al cargar el área', 'error'));
}

function guardarArea() {
    const id     = document.getElementById('areaId').value;
    const url    = id ? `/areas/${id}` : '/areas';
    const method = id ? 'put' : 'post';

    const data = {
        nombre: document.getElementById('aNombre').value,
        estado: document.getElementById('aEstado').value,
    };

    axios[method](url, data)
        .then(res => {
            toast(res.data.message, 'success');
            closeModal('modalArea');
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
    document.getElementById('areaId').value            = '';
    document.getElementById('modalTitulo').textContent = 'Nueva Área';
    document.getElementById('formArea').reset();
}
function toggleEstadoArea(id, estadoActual) {
    const mensaje = estadoActual === 'Activo'
        ? '¿Deseas deshabilitar esta área?'
        : '¿Deseas habilitar esta área?';

    if (!confirm(mensaje)) return;

    axios.patch(`/areas/${id}/estado`)
        .then(res => { toast(res.data.message, 'success'); cargarTabla(); })
        .catch(() => toast('Error al cambiar el estado', 'error'));
}



function importarAreas() {
    const archivo = document.getElementById('archivoCargaMasiva').files[0];
    if (!archivo) { toast('Debes seleccionar un archivo', 'error'); return; }

    const formData = new FormData();
    formData.append('archivo', archivo);

    axios.post('/plantillas/areas/importar', formData, {
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

function eliminarArea(id) {
    if (!confirm('¿Estás seguro de eliminar esta área?')) return;

    axios.delete(`/areas/${id}`)
        .then(res => {
            toast(res.data.message, 'success');
            cargarTabla();
        })
        .catch(err => {
            toast(err.response?.data?.message || 'Error al eliminar', 'error');
        });
}