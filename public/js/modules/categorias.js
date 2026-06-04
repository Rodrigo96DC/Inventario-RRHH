document.addEventListener('DOMContentLoaded', () => {
    const buscarDebounce = debounce(() => cargarTabla(), 350);
    document.getElementById('buscador')?.addEventListener('input', buscarDebounce);

    document.addEventListener('click', e => {
        const link = e.target.closest('.pagina-link');
        if (link) { e.preventDefault(); cargarTabla(link.dataset.url); }
    });
});

function cargarTabla(url = '/categorias') {
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
    openModal('modalCategoria');
}

function editarCategoria(id) {
    axios.get(`/categorias/${id}`)
        .then(res => {
            const c = res.data.data;
            document.getElementById('modalTitulo').textContent = 'Editar Categoría';
            document.getElementById('categoriaId').value       = c.id;
            document.getElementById('catNombre').value         = c.nombre;
            document.getElementById('catIcono').value          = c.icono ?? '';
            document.getElementById('catEstado').value         = c.estado;
            openModal('modalCategoria');
        })
        .catch(() => toast('Error al cargar la categoría', 'error'));
}

function guardarCategoria() {
    const id     = document.getElementById('categoriaId').value;
    const url    = id ? `/categorias/${id}` : '/categorias';
    const method = id ? 'put' : 'post';

    const data = {
        nombre: document.getElementById('catNombre').value,
        icono:  document.getElementById('catIcono').value,
        estado: document.getElementById('catEstado').value,
    };

    axios[method](url, data)
        .then(res => {
            toast(res.data.message, 'success');
            closeModal('modalCategoria');
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

function toggleEstadoCategoria(id, estadoActual) {
    const mensaje = estadoActual === 'Activo'
        ? '¿Deseas deshabilitar esta categoría?'
        : '¿Deseas habilitar esta categoría?';

    if (!confirm(mensaje)) return;

    axios.patch(`/categorias/${id}/estado`)
        .then(res => { toast(res.data.message, 'success'); cargarTabla(); })
        .catch(() => toast('Error al cambiar el estado', 'error'));
}

function limpiarModal() {
    document.getElementById('categoriaId').value       = '';
    document.getElementById('modalTitulo').textContent = 'Nueva Categoría';
    document.getElementById('formCategoria').reset();
}

function eliminarCategoria(id) {
    if (!confirm('¿Estás seguro de eliminar esta categoría?')) return;
    axios.delete(`/categorias/${id}`)
        .then(res => { toast(res.data.message, 'success'); cargarTabla(); })
        .catch(err => toast(err.response?.data?.message || 'Error al eliminar', 'error'));
}

function importarCategorias() {
    const archivo = document.getElementById('archivoCargaMasiva').files[0];
    if (!archivo) { toast('Debes seleccionar un archivo', 'error'); return; }

    const formData = new FormData();
    formData.append('archivo', archivo);

    axios.post('/plantillas/categorias/importar', formData, {
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