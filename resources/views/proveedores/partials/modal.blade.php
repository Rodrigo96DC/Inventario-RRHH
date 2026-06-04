<div class="modal-overlay" id="modalProveedor">
    <div class="modal" style="max-width:540px;">
        <div class="modal-header">
            <span class="modal-title" id="modalTitulo">Nuevo Proveedor</span>
            <button class="modal-close" onclick="closeModal('modalProveedor'); limpiarModal();">
                <i class="ti ti-x"></i>
            </button>
        </div>

        <input type="hidden" id="proveedorId">
        <form id="formProveedor">
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Razón Social <span class="required">*</span></label>
                    <input type="text" id="pRazonSocial" class="form-control" placeholder="Nombre empresa">
                </div>
                <div class="form-group">
                    <label class="form-label">RUT <span class="required">*</span></label>
                    <input type="text" id="pRut" class="form-control" placeholder="76.123.456-7" data-rut>
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Contacto</label>
                    <input type="text" id="pContacto" class="form-control" placeholder="Nombre contacto">
                </div>
                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input type="text" id="pTelefono" class="form-control" placeholder="+56 9 1234 5678">
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" id="pEmail" class="form-control" placeholder="correo@empresa.cl">
                </div>
                <div class="form-group">
                    <label class="form-label">Estado <span class="required">*</span></label>
                    <select id="pEstado" class="form-control">
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Dirección</label>
                <input type="text" id="pDireccion" class="form-control" placeholder="Dirección">
            </div>
        </form>

        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modalProveedor'); limpiarModal();">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarProveedor()">
                <i class="ti ti-device-floppy"></i> Guardar
            </button>
        </div>
    </div>
</div>