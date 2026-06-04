<div class="modal-overlay" id="modalItem">
    <div class="modal" style="max-width:740px;">
        <div class="modal-header">
            <span class="modal-title" id="modalTitulo">Nuevo Ítem</span>
            <button class="modal-close" onclick="closeModal('modalItem'); limpiarModal();">
                <i class="ti ti-x"></i>
            </button>
        </div>
<div class="form-group">
    <label class="form-label">Categorías <span class="required">*</span>
        <span class="text-secondary text-sm">(selecciona al menos una, la primera marcada será la principal)</span>
    </label>
    <div style="display:flex;flex-wrap:wrap;gap:8px;padding:10px;border:0.5px solid var(--border);border-radius:8px;background:var(--input-bg);max-height:160px;overflow-y:auto;" id="categoriasAdicionales">
        @foreach(\App\Models\CategoriaInventario::where('estado','Activo')->orderBy('nombre')->get() as $cat)
        <label style="display:flex;align-items:center;gap:6px;font-size:12px;cursor:pointer;padding:4px 8px;border:0.5px solid var(--border);border-radius:6px;background:var(--card-bg);">
            <input type="checkbox" name="categorias_adicionales[]"
                value="{{ $cat->id }}"
                class="cat-adicional"
                style="width:13px;height:13px;">
            <i class="ti {{ $cat->icono ?? 'ti-box' }}" style="font-size:13px;color:var(--primary);"></i>
            {{ $cat->nombre }}
        </label>
        @endforeach
    </div>
    <span class="form-hint">La primera categoría seleccionada se usará como categoría principal del ítem</span>
</div>
        <input type="hidden" id="itemId">
        <form id="formItem" enctype="multipart/form-data">

            {{-- Datos base --}}
            <div class="grid-2">
                <div class="form-group">
                <label class="form-label">Nombre <span class="required">*</span></label>
                <input type="text" id="iNombre" class="form-control" placeholder="Nombre del ítem">
            </div>
                <div class="form-group">
                    <label class="form-label">Estado <span class="required">*</span></label>
                    <select id="iEstado" class="form-control">
                        <option value="Disponible">Disponible</option>
                        <option value="Asignado">Asignado</option>
                        <option value="En mantención">En mantención</option>
                        <option value="De baja">De baja</option>
                        <option value="Extraviado">Extraviado</option>
                    </select>
                </div>
            </div>

       

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Marca</label>
                    <input type="text" id="iMarca" class="form-control" placeholder="Marca">
                </div>
                <div class="form-group">
                    <label class="form-label">Modelo</label>
                    <input type="text" id="iModelo" class="form-control" placeholder="Modelo">
                </div>
            </div>

        <div class="grid-2">
    <div class="form-group">
        <label class="form-label">Código interno
            <span class="text-secondary text-sm">(si posee)</span>
        </label>
        <input type="text" id="iCodigo" class="form-control" placeholder="Ej: 1600005256">
    </div>
    <div class="form-group">
        <label class="form-label">N° Serie
            <span class="text-secondary text-sm">(si posee)</span>
        </label>
        <input type="text" id="iSerie" class="form-control" placeholder="Ej: CNC4125D3">
    </div>
</div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Precio</label>
                    <input type="number" id="iPrecio" class="form-control" placeholder="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Fecha adquisición</label>
                    <input type="date" id="iFecha" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Ubicación</label>
                <input type="text" id="iUbicacion" class="form-control" placeholder="Piso, oficina, etc.">
            </div>

            <div class="form-group">
                <label class="form-label">Imagen <span class="text-secondary text-sm">(opcional, máx 2MB)</span></label>
                <input type="file" id="iImagen" class="form-control" accept="image/*">
            </div>

            <div class="form-group">
                <label class="form-label">Observación</label>
                <textarea id="iObservacion" class="form-control" rows="2" placeholder="Observaciones..."></textarea>
            </div>

            {{-- Specs dinámicas según categoría --}}
            <div id="specsContainer"></div>

        </form>

        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modalItem'); limpiarModal();">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarItem()">
                <i class="ti ti-device-floppy"></i> Guardar
            </button>
        </div>
    </div>
</div>