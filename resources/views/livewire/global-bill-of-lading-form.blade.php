<div>
    <div style="display:flex; gap:8px; margin-bottom:20px;">
        @foreach (['Partes', 'Referencias', 'Transporte', 'Carga', 'Cargos'] as $i => $label)
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

        @if ($step === 1)
            <h3>1. Partes involucradas</h3>
            <div class="field">
                <label>Shipper / Exporter</label>
                <textarea rows="3" wire:model="shipper"></textarea>
                @error('shipper') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="field">
                <label>Consignee</label>
                <textarea rows="3" wire:model="consignee"></textarea>
                @error('consignee') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="field">
                <label>Notify Party</label>
                <textarea rows="3" wire:model="notify_party"></textarea>
            </div>
        @endif

        @if ($step === 2)
            <h3>2. Referencias y ruteo</h3>
            <div class="field">
                <label>Domestic Routing / Export Instructions</label>
                <textarea rows="3" wire:model="domestic_routing"></textarea>
            </div>
            <div class="field">
                <label>Export References</label>
                <textarea rows="2" wire:model="export_references"></textarea>
            </div>
            <div class="field">
                <label>For Cargo Delivery Please Contact</label>
                <textarea rows="2" wire:model="cargo_delivery_contact"></textarea>
            </div>
            <div class="field">
                <label>Forwarding Agent - References</label>
                <textarea rows="2" wire:model="forwarding_agent_references"></textarea>
            </div>
        @endif

        @if ($step === 3)
            <h3>3. Transporte</h3>
            <div class="grid-2">
                <div class="field">
                    <label>Pre-carriage By</label>
                    <input type="text" wire:model="pre_carriage_by">
                </div>
                <div class="field">
                    <label>Place of Receipt</label>
                    <input type="text" wire:model="place_of_receipt">
                </div>
                <div class="field">
                    <label>Exporting Carrier</label>
                    <input type="text" wire:model="exporting_carrier">
                </div>
                <div class="field">
                    <label>Port of Loading</label>
                    <input type="text" wire:model="port_of_loading_text">
                    @error('port_of_loading_text') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div class="field">
                    <label>On-Carriage By</label>
                    <input type="text" wire:model="on_carriage_by">
                </div>
                <div class="field">
                    <label>No. of Bills of Lading</label>
                    <input type="text" wire:model="number_of_original_bsl">
                </div>
                <div class="field">
                    <label>Port of Discharge</label>
                    <input type="text" wire:model="port_of_discharge">
                    @error('port_of_discharge') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div class="field">
                    <label>Place of Delivery</label>
                    <input type="text" wire:model="place_of_delivery">
                </div>
                <div class="field">
                    <label>Final Destination</label>
                    <input type="text" wire:model="final_destination">
                </div>
            </div>
        @endif

        @if ($step === 4)
            <h3>4. Carga</h3>
            @error('cargoItems') <div class="error">{{ $message }}</div> @enderror
            @foreach ($cargoItems as $index => $item)
                <div style="border:1px solid #e2e5e9; border-radius:4px; padding:14px; margin-bottom:12px;">
                    <div class="field">
                        <label>Marks and Numbers</label>
                        <textarea rows="2" wire:model="cargoItems.{{ $index }}.marks_and_numbers"></textarea>
                    </div>
                    <div class="field">
                        <label>Description of Packages and Goods</label>
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
                        <label>Measurement</label>
                        <input type="text" wire:model="cargoItems.{{ $index }}.measurement_cbm">
                    </div>
                    @if (count($cargoItems) > 1)
                        <button type="button" class="btn btn-danger" wire:click="removeCargoItem({{ $index }})">Quitar línea</button>
                    @endif
                </div>
            @endforeach
            <button type="button" class="btn btn-secondary" wire:click="addCargoItem">+ Agregar línea de carga</button>
            <div class="field" style="margin-top:14px;">
                <label>Net Weight (total)</label>
                <input type="text" wire:model="net_weight">
            </div>
        @endif

        @if ($step === 5)
            <h3>5. Cargos</h3>
            <div class="grid-2">
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
            </div>
            <div class="field">
                <label>Freight Charges (detalle)</label>
                <textarea rows="2" wire:model="freight_and_charges"></textarea>
            </div>
            <div class="field">
                <label>Excess Value Charge</label>
                <input type="text" wire:model="excess_value_declaration">
            </div>
            <div class="field">
                <label>Total Charges (USD)</label>
                <input type="number" step="0.01" wire:model="total_charges">
            </div>
            <div style="margin-top:20px;">
                <button type="button" class="btn btn-success" wire:click="issue" wire:confirm="¿Confirmas emitir este BL? Ya no se podrá editar.">
                    Emitir BL
                </button>
            </div>
        @endif

        <div style="margin-top:20px; display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex; gap:10px;">
                <button type="button" class="btn btn-secondary" wire:click="previousStep" @if($step === 1) disabled @endif>
                    &larr; Anterior
                </button>
                <button type="button" class="btn btn-secondary" wire:click="saveDraftAndExit" wire:confirm="¿Guardar como borrador y salir?">
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
