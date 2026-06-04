<div class="modal-overlay" id="modalCargo">
    <div class="modal" style="max-width:440px;">
        <div class="modal-header">
            <span class="modal-title" id="modalTitulo">Nuevo Cargo</span>
            <button class="modal-close" onclick="closeModal('modalCargo'); limpiarModal();">
                <i class="ti ti-x"></i>
            </button>
        </div>

        <input type="hidden" id="cargoId">
        <form id="formCargo">
            <div class="form-group">
                <label class="form-label">Nombre <span class="required">*</span></label>
                <input type="text" id="cNombre" class="form-control" placeholder="Nombre del cargo">
            </div>
            <div class="form-group">
                <label class="form-label">Grado</label>
                <input type="text" id="cGrado" class="form-control" placeholder="Ej: 10">
            </div>
            <div class="form-group">
                <label class="form-label">Estado <span class="required">*</span></label>
                <select id="cEstado" class="form-control">
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                </select>
            </div>
        </form>

        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modalCargo'); limpiarModal();">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarCargo()">
                <i class="ti ti-device-floppy"></i> Guardar
            </button>
        </div>
    </div>
</div>