document.addEventListener('DOMContentLoaded', () => {});

// ─── EDIFICIOS ───────────────────────────────────────────────────
function editarEdificio(id, nombre, direccion, estado) {
    document.getElementById('modalEdificioTitulo').textContent = 'Editar Edificio';
    document.getElementById('edificioId').value   = id;
    document.getElementById('edNombre').value     = nombre;
    document.getElementById('edDireccion').value  = direccion ?? '';
    document.getElementById('edEstado').value     = estado;
    openModal('modalEdificio');
}

function guardarEdificio() {
    const id     = document.getElementById('edificioId').value;
    const url    = id ? `/edificios/${id}` : '/edificios';
    const method = id ? 'put' : 'post';

    const data = {
        nombre:    document.getElementById('edNombre').value,
        direccion: document.getElementById('edDireccion').value,
        estado:    document.getElementById('edEstado').value,
    };

    axios[method](url, data)
        .then(res => {
            toast(res.data.message, 'success');
            closeModal('modalEdificio');
            window.location.reload();
        })
        .catch(err => {
            toast(err.response?.data?.message || 'Error al guardar', 'error');
        });
}

// ─── PISOS ───────────────────────────────────────────────────────
function abrirModalPiso(edificioId) {
    document.getElementById('pisoEdificioId').value = edificioId;
    openModal('modalPiso');
}

function guardarPiso() {
    const formData = new FormData();
    formData.append('id_edificio', document.getElementById('pisoEdificioId').value);
    formData.append('nombre',      document.getElementById('pisoNombre').value);
    formData.append('numero',      document.getElementById('pisoNumero').value);
    formData.append('estado',      document.getElementById('pisoEstado').value);

    const imagen = document.getElementById('pisoImagen').files[0];
    if (imagen) formData.append('imagen', imagen);

    axios.post('/pisos', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    })
        .then(res => {
            toast(res.data.message, 'success');
            closeModal('modalPiso');
            window.location.reload();
        })
        .catch(err => {
            const errores = err.response?.data?.errors;
            toast(errores ? Object.values(errores).flat().join(' ') : 'Error al guardar', 'error');
        });
}

function editarPiso(id, nombre, numero, estado) {
    document.getElementById('editarPisoId').value     = id;
    document.getElementById('editarPisoNombre').value = nombre;
    document.getElementById('editarPisoNumero').value = numero;
    document.getElementById('editarPisoEstado').value = estado;
    document.getElementById('editarPisoImagen').value = '';
    openModal('modalEditarPiso');
}

function guardarEditarPiso() {
    const id     = document.getElementById('editarPisoId').value;
    const numero = document.getElementById('editarPisoNumero').value;
    console.log('numero enviado:', numero, typeof numero);

    const formData = new FormData();
    formData.append('nombre',  document.getElementById('editarPisoNombre').value);
    formData.append('numero',  numero);
    formData.append('estado',  document.getElementById('editarPisoEstado').value);

    const imagen = document.getElementById('editarPisoImagen').files[0];
    if (imagen) formData.append('imagen', imagen);

    axios.post(`/pisos/${id}/update`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    })
        .then(res => {
            toast(res.data.message, 'success');
            closeModal('modalEditarPiso');
            window.location.reload();
        })
        .catch(err => {
            const errores = err.response?.data?.errors;
            toast(errores ? Object.values(errores).flat().join(' ') : 'Error al guardar', 'error');
        });
}