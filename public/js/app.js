/* =============================================
   APP.JS — Lógica principal del sistema
   =============================================
   Dependencias (cargar antes en el layout):
   - Alpine.js (cdn)
   - Axios (cdn)
   ============================================= */

document.addEventListener('DOMContentLoaded', () => {

    // ─── CSRF para todas las peticiones Axios ───
    axios.defaults.headers.common['X-CSRF-TOKEN'] =
        document.querySelector('meta[name="csrf-token"]')?.content;
    axios.defaults.headers.common['Accept'] = 'application/json';

    // ─── SIDEBAR ────────────────────────────────
    const sidebar     = document.getElementById('sidebar');
    const collapseBtn = document.getElementById('collapseBtn');

    if (collapseBtn && sidebar) {
        // Estado guardado en localStorage
        const isCollapsed = localStorage.getItem('sidebar_collapsed') === 'true';
        if (isCollapsed) sidebar.classList.add('collapsed');

        collapseBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem(
                'sidebar_collapsed',
                sidebar.classList.contains('collapsed')
            );
        });
    }

    // ─── TEMA (claro/oscuro) ─────────────────────
    const wrapper   = document.getElementById('appWrapper');
    const themeIcon = document.getElementById('themeIcon');

    // Aplicar tema guardado
    const savedTheme = localStorage.getItem('tema') || 'light';
    if (wrapper) {
        wrapper.dataset.theme = savedTheme;
        updateThemeIcon(savedTheme);
    }

    // Aplicar color guardado
    const savedColor = localStorage.getItem('color') || 'blue';
    if (wrapper) wrapper.dataset.color = savedColor;

    // ─── DROPDOWN USUARIO ───────────────────────
    const userMenu     = document.getElementById('userMenu');
    const userDropdown = document.getElementById('userDropdown');

    if (userMenu && userDropdown) {
        userMenu.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('open');
        });

        document.addEventListener('click', () => {
            userDropdown.classList.remove('open');
        });
    }

    // ─── AUTO-CERRAR ALERTAS ─────────────────────
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            alert.style.transition = 'opacity 0.4s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 400);
        });
    }, 4000);

});

// ─── TOGGLE TEMA ────────────────────────────────
function toggleTheme() {
    const wrapper = document.getElementById('appWrapper');
    if (!wrapper) return;

    const isDark  = wrapper.dataset.theme === 'dark';
    const newTheme = isDark ? 'light' : 'dark';

    wrapper.dataset.theme = newTheme;
    localStorage.setItem('tema', newTheme);
    updateThemeIcon(newTheme);

    // Guardar en BD vía Axios (si el usuario está autenticado)
    axios.post('/configuracion/tema', { tema: newTheme }).catch(() => {});
}

function updateThemeIcon(theme) {
    const icon = document.getElementById('themeIcon');
    if (!icon) return;
    icon.className = theme === 'dark' ? 'ti ti-moon' : 'ti ti-sun';
}

// ─── CAMBIAR COLOR PRIMARIO ──────────────────────
function setColor(color) {
    const wrapper = document.getElementById('appWrapper');
    if (!wrapper) return;

    wrapper.dataset.color = color;
    localStorage.setItem('color', color);

    axios.post('/configuracion/color', { color }).catch(() => {});
}

// ─── CERRAR ALERTA ──────────────────────────────
function closeAlert(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.style.transition = 'opacity 0.3s ease';
    el.style.opacity = '0';
    setTimeout(() => el.remove(), 300);
}

// ─── TOGGLE MENÚ USUARIO ────────────────────────
function toggleUserMenu() {
    document.getElementById('userDropdown')?.classList.toggle('open');
}

// ─── MODAL ──────────────────────────────────────
function openModal(id) {
    document.getElementById(id)?.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    document.getElementById(id)?.classList.remove('open');
    document.body.style.overflow = '';
}

// Cerrar modal al hacer click en el overlay
// document.addEventListener('click', (e) => {
//     if (e.target.classList.contains('modal-overlay')) {
//         e.target.classList.remove('open');
//         document.body.style.overflow = '';
//     }
// });

// Cerrar modal con Escape
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.open').forEach(m => {
            m.classList.remove('open');
            document.body.style.overflow = '';
        });
    }
});

// ─── NOTIFICACIÓN TOAST ──────────────────────────
function toast(message, type = 'success') {
    const colors = {
        success: { bg: '#d1fae5', color: '#065f46', icon: 'ti-circle-check' },
        error:   { bg: '#fee2e2', color: '#991b1b', icon: 'ti-circle-x' },
        warning: { bg: '#fef3c7', color: '#92400e', icon: 'ti-alert-triangle' },
        info:    { bg: '#e0f2fe', color: '#075985', icon: 'ti-info-circle' },
    };

    const t = colors[type] || colors.success;

    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed; bottom: 20px; right: 20px; z-index: 9999;
        background: ${t.bg}; color: ${t.color};
        padding: 12px 18px; border-radius: 8px;
        display: flex; align-items: center; gap: 10px;
        font-size: 13px; font-family: system-ui, sans-serif;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transition: opacity 0.3s ease;
        max-width: 320px;
    `;
    toast.innerHTML = `<i class="ti ${t.icon}" style="font-size:16px;flex-shrink:0"></i><span>${message}</span>`;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

// ─── CONFIRMAR ELIMINACIÓN ───────────────────────
function confirmarEliminar(url, mensaje = '¿Estás seguro de eliminar este registro?') {
    if (!confirm(mensaje)) return;

    axios.delete(url)
        .then(res => {
            toast(res.data.message || 'Eliminado correctamente', 'success');
            // Recargar tabla si existe función global
            if (typeof recargarTabla === 'function') recargarTabla();
        })
        .catch(err => {
            toast(err.response?.data?.message || 'Error al eliminar', 'error');
        });
}

// ─── HELPER: FORMATEAR FECHA ─────────────────────
function formatFecha(fecha) {
    if (!fecha) return '—';
    return new Date(fecha).toLocaleDateString('es-CL', {
        day: '2-digit', month: '2-digit', year: 'numeric'
    });
}

// ─── HELPER: DEBOUNCE PARA BUSCADORES ────────────
function debounce(fn, delay = 350) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
}

// ─── FORMATO RUT ─────────────────────────────────────────────────
function formatearRut(rut) {
    let valor = rut.replace(/\./g, '').replace('-', '');
    if (valor.length <= 1) return valor;

    let cuerpo = valor.slice(0, -1);
    let dv     = valor.slice(-1).toUpperCase();

    cuerpo = cuerpo.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    return `${cuerpo}-${dv}`;
}

document.addEventListener('input', e => {
    if (e.target.dataset.rut !== undefined) {
        const pos = e.target.selectionStart;
        e.target.value = formatearRut(e.target.value);
    }
});

// ─── TABS ────────────────────────────────────────────────────────
function cambiarTab(id, btn) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById(`tab-${id}`)?.classList.add('active');
    btn.classList.add('active');
}

if (!window.location.pathname.includes('inventario')) {
    localStorage.removeItem('inventario_tab');
}

// ─── NOTIFICACIONES ──────────────────────────────────────────────
function toggleNotificaciones() {
    const dropdown = document.getElementById('notifDropdown');
    if (!dropdown) return;
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
}

function marcarTodasLeidas() {
    axios.post('/notificaciones/leer-todas')
        .then(() => {
            document.getElementById('notifDropdown').style.display = 'none';
            window.location.reload();
        })
        .catch(() => toast('Error al marcar notificaciones', 'error'));
}

// Cerrar dropdown al hacer click fuera
document.addEventListener('click', e => {
    const container = document.getElementById('notifContainer');
    if (container && !container.contains(e.target)) {
        const dropdown = document.getElementById('notifDropdown');
        if (dropdown) dropdown.style.display = 'none';
    }
});

function marcarLeida(id) {
    axios.post(`/notificaciones/${id}/leer`).catch(() => {});
}

function manejarResultadoImportacion(res, callback = null) {
    if (res.data.reporte) {
        toast(res.data.message, res.data.errores > 0 ? 'warning' : 'success');
        setTimeout(() => {
            if (confirm(`${res.data.message}\n\n¿Deseas descargar el reporte detallado?`)) {
                window.open(res.data.reporte, '_blank');
            }
            if (callback) callback();
        }, 300);
    } else {
        toast(res.data.message, 'success');
        if (callback) callback();
    }
}