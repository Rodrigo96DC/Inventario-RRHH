document.addEventListener('DOMContentLoaded', () => {
    const buscarDebounce = debounce(() => cargarTabla(), 350);
    document.getElementById('buscador')?.addEventListener('input', buscarDebounce);
    document.getElementById('filtroEstado')?.addEventListener('change', () => cargarTabla());

    document.addEventListener('click', e => {
        const link = e.target.closest('.pagina-link');
        if (link) { e.preventDefault(); cargarTabla(link.dataset.url); }
    });
});

function cargarTabla(url = '/proveedores') {
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
    openModal('modalProveedor');
}

function editarProveedor(id) {
    axios.get(`/proveedores/${id}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(res => {

            console.log(JSON.stringify(res.data));
            const p = res.data.data;
            document.getElementById('modalTitulo').textContent = 'Editar Proveedor';
            document.getElementById('proveedorId').value       = p.id;
            document.getElementById('pRazonSocial').value      = p.razon_social;
            document.getElementById('pRut').value              = p.rut;
            document.getElementById('pContacto').value         = p.contacto ?? '';
            document.getElementById('pTelefono').value         = p.telefono ?? '';
            document.getElementById('pEmail').value            = p.email ?? '';
            document.getElementById('pDireccion').value        = p.direccion ?? '';
            document.getElementById('pEstado').value           = p.estado;
            openModal('modalProveedor');
        })
        .catch(() => toast('Error al cargar el proveedor', 'error'));
}

function guardarProveedor() {
    const id     = document.getElementById('proveedorId').value;
    const url    = id ? `/proveedores/${id}` : '/proveedores';
    const method = id ? 'put' : 'post';

    const data = {
        razon_social: document.getElementById('pRazonSocial').value,
        rut:          document.getElementById('pRut').value,
        contacto:     document.getElementById('pContacto').value,
        telefono:     document.getElementById('pTelefono').value,
        email:        document.getElementById('pEmail').value,
        direccion:    document.getElementById('pDireccion').value,
        estado:       document.getElementById('pEstado').value,
    };

    axios[method](url, data)
        .then(res => {
            toast(res.data.message, 'success');
            closeModal('modalProveedor');
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

function toggleEstadoProveedor(id, estadoActual) {
    const mensaje = estadoActual === 'Activo'
        ? '¿Deseas deshabilitar este proveedor?'
        : '¿Deseas habilitar este proveedor?';

    if (!confirm(mensaje)) return;

    axios.patch(`/proveedores/${id}/estado`)
        .then(res => { toast(res.data.message, 'success'); cargarTabla(); })
        .catch(() => toast('Error al cambiar estado', 'error'));
}

function eliminarProveedor(id) {
    if (!confirm('¿Estás seguro de eliminar este proveedor?')) return;

    axios.delete(`/proveedores/${id}`)
        .then(res => { toast(res.data.message, 'success'); cargarTabla(); })
        .catch(err => toast(err.response?.data?.message || 'Error al eliminar', 'error'));
}

function limpiarModal() {
    document.getElementById('proveedorId').value         = '';
    document.getElementById('modalTitulo').textContent   = 'Nuevo Proveedor';
    document.getElementById('formProveedor').reset();
}



function importarProveedores() {
    const archivo = document.getElementById('archivoCargaMasiva').files[0];
    if (!archivo) { toast('Debes seleccionar un archivo', 'error'); return; }

    const formData = new FormData();
    formData.append('archivo', archivo);

    axios.post('/plantillas/proveedores/importar', formData, {
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