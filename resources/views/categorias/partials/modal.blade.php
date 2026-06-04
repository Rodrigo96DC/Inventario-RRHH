<div class="modal-overlay" id="modalCategoria">
    <div class="modal" style="max-width:440px;">
        <div class="modal-header">
            <span class="modal-title" id="modalTitulo">Nueva Categoría</span>
            <button class="modal-close" onclick="closeModal('modalCategoria'); limpiarModal();">
                <i class="ti ti-x"></i>
            </button>
        </div>

        <input type="hidden" id="categoriaId">
        <form id="formCategoria">
            <div class="form-group">
                <label class="form-label">Nombre <span class="required">*</span></label>
                <input type="text" id="catNombre" class="form-control" placeholder="Nombre de la categoría">
            </div>
            <div class="form-group">
                <label class="form-label">Ícono <span class="text-secondary text-sm">(clase Tabler Icons)</span></label>
                <input type="text" id="catIcono" class="form-control" placeholder="Ej: ti-cpu">
                <span class="form-hint">Ver íconos en <a href="https://tabler.io/icons" target="_blank" style="color:var(--primary);">tabler.io/icons</a></span>
            </div>
            <div class="form-group">
                <label class="form-label">Estado <span class="required">*</span></label>
                <select id="catEstado" class="form-control">
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                </select>
            </div>
        </form>

        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modalCategoria'); limpiarModal();">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarCategoria()">
                <i class="ti ti-device-floppy"></i> Guardar
            </button>
        </div>
    </div>
</div>