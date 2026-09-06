<div>
    {{-- Indicador de pasos --}}
    <div style="display:flex; gap:8px; margin-bottom:20px;">
        @foreach (['Datos generales', 'Transporte', 'Carga', 'Flete', 'Revisión'] as $i => $label)
            @php $n = $i + 1; @endphp
            <div wire:click="goToStep({{ $n }})"
                style="flex:1; text-align:center; padding:8px 4px; border-radius:4px; font-size:12px; font-weight:bold;
                background: {{ $step === $n ? '#1e6fb8' : ($step > $n ? '#2fae4e' : '#e2e5e9') }};
                color: {{ $step >= $n ? '#fff' : '#666' }};
                cursor: {{ $n <= $maxStepReached ? 'pointer' : 'not-allowed' }};
                opacity: {{ $n <= $maxStepReached ? '1' : '.6' }};">
                {{ $n }}. {{ $label }}
            </div>
        @endforeach
    </div>

    <div class="card">

        {{-- PASO 1: Datos generales --}}
        @if ($step === 1)
            <h3>1. Datos generales</h3>
            <div class="grid-2">
                <div class="field">
                    <label>Puerto (define el prefijo del BL)</label>
                    <select wire:model="port_id">
                        <option value="">-- Selecciona --</option>
                        @foreach ($ports as $port)
                            <option value="{{ $port->id }}">{{ $port->code }} - {{ $port->name }}</option>
                        @endforeach
                    </select>
                    @error('port_id') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div class="field">
                    <label>Country Origin</label>
                    <input type="text" wire:model="country_origin">
                </div>
            </div>
            <div class="grid-2">
                <div class="field">
                    <label>Shipper's Ref</label>
                    <input type="text" wire:model="shipper_ref">
                </div>
                <div class="field">
                    <label>Bill of Loading Nº (referencia de la naviera, si aplica)</label>
                    <input type="text" wire:model="bill_of_loading_ref">
                </div>
            </div>
            <div class="field">
                <label>1. Shipper</label>
                <textarea rows="3" wire:model="shipper"></textarea>
                @error('shipper') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="field">
                <label>2. Consignee or Order</label>
                <textarea rows="3" wire:model="consignee"></textarea>
                @error('consignee') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="field">
                <label>3. Notify Party</label>
                <textarea rows="3" wire:model="notify_party"></textarea>
            </div>
        @endif

        {{-- PASO 2: Transporte --}}
        @if ($step === 2)
            <h3>2. Transporte</h3>
            <div class="grid-2">
                <div class="field">
                    <label>4. Pre-carriage By</label>
                    <input type="text" wire:model="pre_carriage_by">
                </div>
                <div class="field">
                    <label>5. Place of Receipt</label>
                    <input type="text" wire:model="place_of_receipt">
                </div>
                <div class="field">
                    <label>6. Ocean Vessel Voy. No.</label>
                    <input type="text" wire:model="ocean_vessel_voy_no">
                </div>
                <div class="field">
                    <label>8. Port of Discharge</label>
                    <input type="text" wire:model="port_of_discharge">
                    @error('port_of_discharge') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div class="field">
                    <label>9. Place of Delivery</label>
                    <input type="text" wire:model="place_of_delivery">
                </div>
                <div class="field">
                    <label>Final Destination</label>
                    <input type="text" wire:model="final_destination">
                </div>
            </div>
        @endif

        {{-- PASO 3: Carga --}}
        @if ($step === 3)
            <h3>3. Carga</h3>
            @error('cargoItems') <div class="error">{{ $message }}</div> @enderror
            @foreach ($cargoItems as $index => $item)
                <div style="border:1px solid #e2e5e9; border-radius:4px; padding:14px; margin-bottom:12px;">
                    <div class="grid-2">
                        <div class="field">
                            <label>Marks &amp; Number / Container-Seal</label>
                            <textarea rows="2" wire:model="cargoItems.{{ $index }}.marks_and_numbers"></textarea>
                        </div>
                        <div class="field">
                            <label>Container / Seal No.</label>
                            <input type="text" wire:model="cargoItems.{{ $index }}.container_seal_no">
                        </div>
                    </div>
                    <div class="field">
                        <label>Description of Goods</label>
                        <textarea rows="2" wire:model="cargoItems.{{ $index }}.description_of_goods"></textarea>
                        @error("cargoItems.{$index}.description_of_goods") <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div class="grid-2">
                        <div class="field">
                            <label>No. of Pkgs</label>
                            <input type="text" wire:model="cargoItems.{{ $index }}.no_of_pkgs">
                        </div>
                        <div class="field">
                            <label>Gross Weight</label>
                            <input type="text" wire:model="cargoItems.{{ $index }}.gross_weight">
                        </div>
                    </div>
                    <div class="field">
                        <label>Measurement (CBM)</label>
                        <input type="text" wire:model="cargoItems.{{ $index }}.measurement_cbm">
                    </div>
                    @if (count($cargoItems) > 1)
                        <button type="button" class="btn btn-danger" wire:click="removeCargoItem({{ $index }})">Quitar línea</button>
                    @endif
                </div>
            @endforeach
            <button type="button" class="btn btn-secondary" wire:click="addCargoItem">+ Agregar línea de carga</button>
        @endif

        {{-- PASO 4: Flete y condiciones --}}
        @if ($step === 4)
            <h3>4. Flete y condiciones</h3>
            <div class="grid-2">
                <div class="field">
                    <label>Freight Payable At</label>
                    <input type="text" wire:model="freight_payable_at">
                </div>
                <div class="field">
                    <label>Number of Original Bs/L</label>
                    <input type="text" wire:model="number_of_original_bsl">
                </div>
                <div class="field">
                    <label>Freight Type</label>
                    <select wire:model="freight_type">
                        <option value="prepaid">Prepaid</option>
                        <option value="collect">Collect</option>
                    </select>
                </div>
                <div class="field">
                    <label>Freight Total (USD)</label>
                    <input type="number" step="0.01" wire:model="freight_total">
                </div>
                <div class="field">
                    <label>On Board Date</label>
                    <input type="date" wire:model="on_board_date">
                </div>
                <div class="field">
                    <label>14. Delivery Agent</label>
                    <input type="text" wire:model="delivery_agent">
                </div>
            </div>
            <div class="field">
                <label>11. Freight and Charges (detalle)</label>
                <textarea rows="2" wire:model="freight_and_charges"></textarea>
            </div>
            <div class="field">
                <label>10. Temperature Control Instructions</label>
                <input type="text" wire:model="temperature_control_instructions">
            </div>
            <div class="field">
                <label>12. Excess Value Declaration</label>
                <textarea rows="2" wire:model="excess_value_declaration"></textarea>
            </div>
        @endif

        {{-- PASO 5: Revisión y emisión --}}
        @if ($step === 5)
            <h3>5. Revisión</h3>
            <p style="font-size:13px; color:#555;">
                Revisa que todo esté correcto. Al hacer clic en <strong>Emitir BL</strong> se genera
                el número consecutivo definitivo del puerto <strong>{{ optional($ports->firstWhere('id', $port_id))->code }}</strong>
                y ya no se puede volver a editar sin anular el documento.
            </p>
            <table style="width:100%; font-size:13px; border-collapse:collapse;">
                <tr><td style="padding:4px 0; color:#666;">Shipper</td><td>{{ $shipper }}</td></tr>
                <tr><td style="padding:4px 0; color:#666;">Consignee</td><td>{{ $consignee }}</td></tr>
                <tr><td style="padding:4px 0; color:#666;">Puerto de descarga</td><td>{{ $port_of_discharge }}</td></tr>
                <tr><td style="padding:4px 0; color:#666;">Líneas de carga</td><td>{{ count($cargoItems) }}</td></tr>
                <tr><td style="padding:4px 0; color:#666;">Flete</td><td>{{ ucfirst($freight_type) }} — USD {{ number_format((float) $freight_total, 2) }}</td></tr>
            </table>
            <div style="margin-top:20px;">
                <button type="button" class="btn btn-success" wire:click="issue" wire:confirm="¿Confirmas emitir este BL? Ya no se podrá editar.">
                    Emitir BL
                </button>
            </div>
        @endif

        {{-- Navegación --}}
        <div style="margin-top:20px; display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex; gap:10px;">
                <button type="button" class="btn btn-secondary" wire:click="previousStep" @if($step === 1) disabled @endif>
                    &larr; Anterior
                </button>
                <button type="button" class="btn btn-secondary" wire:click="saveDraftAndExit" wire:confirm="¿Guardar como borrador y salir? Puedes continuar editándolo después desde el listado.">
                    Guardar borrador y salir
                </button>
            </div>
            @if ($step < $totalSteps)
                <button type="button" class="btn btn-primary" wire:click="nextStep">
                    Guardar y continuar &rarr;
                </button>
            @endif
        </div>
    </div>
</div>