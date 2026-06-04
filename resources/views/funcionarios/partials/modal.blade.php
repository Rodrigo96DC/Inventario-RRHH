<div class="modal-overlay" id="modalFuncionario">
    <div class="modal" style="max-width:680px;">
        <div class="modal-header">
            <span class="modal-title" id="modalTitulo">Nuevo Funcionario</span>
            <button class="modal-close" onclick="closeModal('modalFuncionario'); limpiarModal();">
                <i class="ti ti-x"></i>
            </button>
        </div>

        <input type="hidden" id="funcionarioId">
        <form id="formFuncionario">

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">RUT <span class="required">*</span></label>
                    <input type="text" id="fRut" class="form-control" placeholder="12.345.678-9" data-rut
                        maxlength="12">
                </div>
                <div class="form-group">
                    <label class="form-label">Estado <span class="required">*</span></label>
                    <select id="fEstado" class="form-control">
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                        <option value="Licencia">Licencia</option>
                        <option value="Vacaciones">Vacaciones</option>
                    </select>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Nombre <span class="required">*</span></label>
                    <input type="text" id="fNombre" class="form-control" placeholder="Nombre">
                </div>
                <div class="form-group">
                    <label class="form-label">Apellido <span class="required">*</span></label>
                    <input type="text" id="fApellido" class="form-control" placeholder="Apellido">
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" id="fEmail" class="form-control" placeholder="correo@ejemplo.cl">
                </div>
                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input type="text" id="fTelefono" class="form-control" placeholder="+56 9 1234 5678">
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Área</label>
                    <select id="fArea" class="form-control">
                        <option value="">Seleccionar área</option>
                        @foreach (\App\Models\Area::where('estado', 'Activo')->get() as $area)
                            <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Cargo</label>
                    <select id="fCargo" class="form-control">
                        <option value="">Seleccionar cargo</option>
                        @foreach (\App\Models\Cargo::where('estado', 'Activo')->get() as $cargo)
                            <option value="{{ $cargo->id }}">{{ $cargo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Tipo contrato</label>
                    <select id="fContrato" class="form-control">
                        <option value="">Seleccionar</option>
                        <option value="Planta">Planta</option>
                        <option value="Contrata">Contrata</option>
                        <option value="Honorarios">Honorarios</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Fecha ingreso</label>
                    <input type="date" id="fIngreso" class="form-control">
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Fecha fin contrato
                        <span class="text-secondary text-sm">(opcional)</span>
                    </label>
                    <input type="date" id="fFinContrato" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Días de aviso antes del vencimiento</label>
                    <select id="fDiasAviso" class="form-control">
                        <option value="15">15 días</option>
                        <option value="30" selected>30 días</option>
                        <option value="60">60 días</option>
                        <option value="90">90 días</option>
                    </select>
                </div>
            </div>


            <div class="form-group">
                <label class="form-label">Dirección</label>
                <input type="text" id="fDireccion" class="form-control" placeholder="Dirección">
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Región</label>
                    <select id="fRegion" class="form-control">
                        <option value="">Seleccionar región</option>
                        @foreach (\App\Models\Region::orderBy('numero')->get() as $region)
                            <option value="{{ $region->id }}">{{ $region->numero }} — {{ $region->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Provincia</label>
                    <select id="fProvincia" class="form-control">
                        <option value="">Seleccionar provincia</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Comuna</label>
                <select id="fComuna" class="form-control">
                    <option value="">Seleccionar comuna</option>
                </select>
            </div>

        </form>

        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modalFuncionario'); limpiarModal();">
                Cancelar
            </button>
            <button class="btn btn-primary" onclick="guardarFuncionario()">
                <i class="ti ti-device-floppy"></i> Guardar
            </button>
        </div>
    </div>
</div>
