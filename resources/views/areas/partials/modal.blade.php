<div class="modal-overlay" id="modalArea">
    <div class="modal" style="max-width:440px;">
        <div class="modal-header">
            <span class="modal-title" id="modalTitulo">Nueva Área</span>
            <button class="modal-close" onclick="closeModal('modalArea'); limpiarModal();">
                <i class="ti ti-x"></i>
            </button>
        </div>

        <input type="hidden" id="areaId">
        <form id="formArea">
            <div class="form-group">
                <label class="form-label">Nombre <span class="required">*</span></label>
                <input type="text" id="aNombre" class="form-control" placeholder="Nombre del área">
            </div>
            <div class="form-group">
                <label class="form-label">Estado <span class="required">*</span></label>
                <select id="aEstado" class="form-control">
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                </select>
            </div>
        </form>

        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modalArea'); limpiarModal();">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarArea()">
                <i class="ti ti-device-floppy"></i> Guardar
            </button>
        </div>
    </div>
</div>