<div style="border-top:0.5px solid var(--border);margin:16px 0;padding-top:16px;">
    <div style="font-size:13px;font-weight:500;color:var(--text-primary);margin-bottom:12px;">
        <i class="ti ti-box" style="color:var(--primary);"></i> Control de stock
    </div>
    <div class="grid-2">
        <div class="form-group">
            <label class="form-label">Stock inicial</label>
            <input type="number" id="sStockInicial" class="form-control" placeholder="0" min="0">
        </div>
        <div class="form-group">
            <label class="form-label">Stock actual</label>
            <input type="number" id="sStockActual" class="form-control" placeholder="0" min="0">
        </div>
    </div>
    <div class="grid-2">
        <div class="form-group">
            <label class="form-label">Unidad de medida</label>
            <select id="sUnidadMedida" class="form-control">
                <option value="">Seleccionar</option>
                <option value="Unidad">Unidad</option>
                <option value="Caja">Caja</option>
                <option value="Paquete">Paquete</option>
                <option value="Litro">Litro</option>
                <option value="Kilo">Kilo</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Stock mínimo</label>
            <input type="number" id="sStockMinimo" class="form-control" placeholder="0" min="0">
        </div>
    </div>
</div>