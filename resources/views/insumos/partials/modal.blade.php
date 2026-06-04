{{-- Modal insumo --}}
<div class="modal-overlay" id="modalInsumo">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header">
            <span class="modal-title" id="modalInsumoTitulo">Nuevo Insumo</span>
            <button class="modal-close" onclick="closeModal('modalInsumo');limpiarModalInsumo();">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <input type="hidden" id="insumoId">
        <form id="formInsumo">
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Nombre <span class="required">*</span></label>
                    <input type="text" id="insNombre" class="form-control" placeholder="Ej: Papel A4">
                </div>
                <div class="form-group">
                    <label class="form-label">Unidad de medida <span class="required">*</span></label>
                    <select id="insUnidad" class="form-control">
                        <option value="unidad">Unidad</option>
                        <option value="caja">Caja</option>
                        <option value="resma">Resma</option>
                        <option value="paquete">Paquete</option>
                        <option value="litro">Litro</option>
                        <option value="kilogramo">Kilogramo</option>
                        <option value="metro">Metro</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Descripción</label>
                <textarea id="insDescripcion" class="form-control" rows="2"></textarea>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Stock mínimo <span class="required">*</span></label>
                    <input type="number" id="insStockMinimo" class="form-control" min="0" value="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Método valuación <span class="required">*</span></label>
                    <select id="insMetodo" class="form-control">
                        <option value="FIFO">FIFO (primero en entrar, primero en salir)</option>
                        <option value="LIFO">LIFO (último en entrar, primero en salir)</option>
                    </select>
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Categoría</label>
                    <select id="insCategoria" class="form-control">
                        <option value="">Sin categoría</option>
                        @foreach(\App\Models\CategoriaInventario::where('estado','Activo')->orderBy('nombre')->get() as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Estado</label>
                    <select id="insEstado" class="form-control">
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
            </div>
        </form>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modalInsumo');limpiarModalInsumo();">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarInsumo()">
                <i class="ti ti-device-floppy"></i> Guardar
            </button>
        </div>
    </div>
</div>

{{-- Modal entrada masiva --}}
<div class="modal-overlay" id="modalEntrada">
    <div class="modal" style="max-width:600px;">
        <div class="modal-header">
            <span class="modal-title">Registrar entrada —
                <span id="entradaInsumoNombre" style="color:var(--primary);"></span>
            </span>
            <button class="modal-close" onclick="closeModal('modalEntrada');limpiarEntrada();">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <input type="hidden" id="entradaInsumoId">

        {{-- Factura opcional --}}
        <div class="form-group">
            <label class="form-label">Factura asociada
                <span class="text-secondary text-sm">(opcional)</span>
            </label>
            <input type="text" id="entradaBuscarFactura" class="form-control"
                placeholder="Buscar por N° factura..."
                oninput="buscarFacturaEntrada()">
            <div id="entradaResultadosFactura"
                style="display:none;border:0.5px solid var(--border);border-radius:8px;margin-top:4px;max-height:150px;overflow-y:auto;background:var(--card-bg);">
            </div>
            <div id="entradaFacturaSeleccionada" style="display:none;margin-top:6px;padding:6px 10px;background:var(--primary-light);border-radius:6px;font-size:12px;color:var(--primary);">
            </div>
            <input type="hidden" id="entradaFacturaId">
        </div>

        {{-- Lista de lotes a ingresar --}}
        <div style="font-size:12px;font-weight:500;color:var(--text-secondary);margin-bottom:8px;">
            LOTES A INGRESAR
        </div>

        <div id="entradaLotesContainer" style="max-height:240px;overflow-y:auto;border:0.5px solid var(--border);border-radius:8px;margin-bottom:12px;">
            <div style="text-align:center;padding:20px;color:var(--text-secondary);font-size:13px;" id="entradaVacio">
                Agrega al menos un lote
            </div>
        </div>

        <button class="btn btn-secondary btn-sm" onclick="agregarLoteEntrada()" style="margin-bottom:16px;">
            <i class="ti ti-plus"></i> Agregar lote
        </button>

        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modalEntrada');limpiarEntrada();">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarEntrada()">
                <i class="ti ti-package-import"></i> Registrar entrada
            </button>
        </div>
    </div>
</div>