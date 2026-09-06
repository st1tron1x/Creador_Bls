<?php

namespace App\Livewire;

use App\Models\BillOfLading;
use App\Models\Port;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layout')]
class BillOfLadingForm extends Component
{
    public int $step = 1;
    public int $totalSteps = 5;
    public int $maxStepReached = 1; // hasta qué paso ha llegado validado; controla qué se puede clicar arriba

    // Paso 1: datos generales
    public ?int $port_id = null;
    public string $country_origin = '';
    public string $shipper_ref = '';
    public string $bill_of_loading_ref = '';
    public string $shipper = '';
    public string $consignee = '';
    public string $notify_party = '';

    // Paso 2: transporte
    public string $pre_carriage_by = '';
    public string $place_of_receipt = '';
    public string $ocean_vessel_voy_no = '';
    public string $port_of_discharge = '';
    public string $place_of_delivery = '';
    public string $final_destination = '';

    // Paso 3: carga (líneas dinámicas)
    public array $cargoItems = [];

    // Paso 4: flete y condiciones
    public string $freight_payable_at = '';
    public string $number_of_original_bsl = '';
    public string $temperature_control_instructions = '';
    public string $freight_type = 'prepaid'; // prepaid | collect
    public ?float $freight_total = null;
    public string $freight_and_charges = '';
    public string $excess_value_declaration = '';
    public ?string $on_board_date = null;
    public string $delivery_agent = '';

    public ?int $draftId = null; // id del BL en borrador mientras se llena el formulario

    public function mount(?BillOfLading $billOfLading = null): void
    {
        $this->cargoItems = [$this->emptyCargoItem()];

        if ($billOfLading) {
            abort_unless($billOfLading->status === 'borrador', 403, 'Solo se pueden editar BL en borrador. Este BL ya fue emitido o anulado.');

            $this->maxStepReached = $this->totalSteps; // ya tiene datos guardados, se puede navegar libremente
            $this->draftId = $billOfLading->id;
            $this->port_id = $billOfLading->port_id;
            $this->country_origin = $billOfLading->country_origin ?? '';
            $this->shipper_ref = $billOfLading->shipper_ref ?? '';
            $this->bill_of_loading_ref = $billOfLading->bill_of_loading_ref ?? '';
            $this->shipper = $billOfLading->shipper ?? '';
            $this->consignee = $billOfLading->consignee ?? '';
            $this->notify_party = $billOfLading->notify_party ?? '';
            $this->pre_carriage_by = $billOfLading->pre_carriage_by ?? '';
            $this->place_of_receipt = $billOfLading->place_of_receipt ?? '';
            $this->ocean_vessel_voy_no = $billOfLading->ocean_vessel_voy_no ?? '';
            $this->port_of_discharge = $billOfLading->port_of_discharge ?? '';
            $this->place_of_delivery = $billOfLading->place_of_delivery ?? '';
            $this->final_destination = $billOfLading->final_destination ?? '';
            $this->freight_payable_at = $billOfLading->freight_payable_at ?? '';
            $this->number_of_original_bsl = $billOfLading->number_of_original_bsl ?? '';
            $this->temperature_control_instructions = $billOfLading->temperature_control_instructions ?? '';
            $this->freight_type = $billOfLading->freight_type ?? 'prepaid';
            $this->freight_total = $billOfLading->freight_total;
            $this->freight_and_charges = $billOfLading->freight_and_charges ?? '';
            $this->excess_value_declaration = $billOfLading->excess_value_declaration ?? '';
            $this->on_board_date = optional($billOfLading->on_board_date)->format('Y-m-d');
            $this->delivery_agent = $billOfLading->delivery_agent ?? '';

            $existingItems = $billOfLading->cargoItems()->orderBy('sort_order')->get();
            if ($existingItems->isNotEmpty()) {
                $this->cargoItems = $existingItems->map(fn ($item) => [
                    'marks_and_numbers' => $item->marks_and_numbers,
                    'container_seal_no' => $item->container_seal_no,
                    'no_of_pkgs' => $item->no_of_pkgs,
                    'description_of_goods' => $item->description_of_goods,
                    'gross_weight' => $item->gross_weight,
                    'measurement_cbm' => $item->measurement_cbm,
                ])->toArray();
            }
        }
    }

    protected function emptyCargoItem(): array
    {
        return [
            'marks_and_numbers' => '',
            'container_seal_no' => '',
            'no_of_pkgs' => '',
            'description_of_goods' => '',
            'gross_weight' => '',
            'measurement_cbm' => '',
        ];
    }

    protected function fieldLabel(string $field): string
    {
        return match ($field) {
            'port_id' => 'Puerto',
            'country_origin' => 'Country Origin',
            'shipper_ref' => "Shipper's Ref",
            'bill_of_loading_ref' => 'Bill of Loading Nº',
            'shipper' => 'Shipper',
            'consignee' => 'Consignee',
            'notify_party' => 'Notify Party',
            'pre_carriage_by' => 'Pre-carriage By',
            'place_of_receipt' => 'Place of Receipt',
            'ocean_vessel_voy_no' => 'Ocean Vessel Voy. No.',
            'port_of_discharge' => 'Port of Discharge',
            'place_of_delivery' => 'Place of Delivery',
            'final_destination' => 'Final Destination',
            'freight_payable_at' => 'Freight Payable At',
            'number_of_original_bsl' => 'Number of Original Bs/L',
            'temperature_control_instructions' => 'Temperature Control',
            'freight_type' => 'Freight Type',
            'freight_total' => 'Freight Total',
            'freight_and_charges' => 'Freight and Charges',
            'excess_value_declaration' => 'Excess Value Declaration',
            'on_board_date' => 'On Board Date',
            'delivery_agent' => 'Delivery Agent',
            default => $field,
        };
    }

    public function addCargoItem(): void
    {
        $this->cargoItems[] = $this->emptyCargoItem();
    }

    public function removeCargoItem(int $index): void
    {
        unset($this->cargoItems[$index]);
        $this->cargoItems = array_values($this->cargoItems);
    }

    protected function rulesForStep(int $step): array
    {
        return match ($step) {
            1 => [
                'port_id' => 'required|exists:ports,id',
                'country_origin' => 'nullable|string|max:255',
                'shipper_ref' => 'nullable|string|max:255',
                'bill_of_loading_ref' => 'nullable|string|max:255',
                'shipper' => 'required|string',
                'consignee' => 'required|string',
                'notify_party' => 'nullable|string',
            ],
            2 => [
                'pre_carriage_by' => 'nullable|string|max:255',
                'place_of_receipt' => 'nullable|string|max:255',
                'ocean_vessel_voy_no' => 'nullable|string|max:255',
                'port_of_discharge' => 'required|string|max:255',
                'place_of_delivery' => 'nullable|string|max:255',
                'final_destination' => 'nullable|string|max:255',
            ],
            3 => [
                'cargoItems' => 'required|array|min:1',
                'cargoItems.*.description_of_goods' => 'required|string',
                'cargoItems.*.no_of_pkgs' => 'nullable|string|max:100',
                'cargoItems.*.gross_weight' => 'nullable|string|max:100',
                'cargoItems.*.measurement_cbm' => 'nullable|string|max:100',
            ],
            4 => [
                'freight_payable_at' => 'nullable|string|max:255',
                'number_of_original_bsl' => 'nullable|string|max:50',
                'temperature_control_instructions' => 'nullable|string',
                'freight_type' => 'required|in:prepaid,collect',
                'freight_total' => 'nullable|numeric|min:0',
                'freight_and_charges' => 'nullable|string',
                'excess_value_declaration' => 'nullable|string',
                'on_board_date' => 'nullable|date',
                'delivery_agent' => 'nullable|string|max:255',
            ],
            default => [],
        };
    }

    public function nextStep(): void
    {
        $this->validate($this->rulesForStep($this->step));
        $this->saveDraft();

        if ($this->step < $this->totalSteps) {
            $this->step++;
            $this->maxStepReached = max($this->maxStepReached, $this->step);
        }
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    /**
     * Permite saltar directamente a cualquier paso haciendo clic en el indicador.
     * Guarda lo que se lleve hasta ahora como borrador, sin exigir validación completa
     * (es solo navegación, no un "avanzar" formal).
     */
    public function goToStep(int $step): void
    {
        $target = max(1, min($step, $this->totalSteps));

        if ($target > $this->maxStepReached) {
            return; // no se puede saltar a un paso que aún no se ha validado
        }

        if (! $this->draftId && ! $this->port_id) {
            return; // nada que guardar todavía, no forzar el insert
        }

        $this->saveDraft();
        $this->step = $target;
    }

    /**
     * Guarda el borrador con lo que se lleve hasta ahora y sale del wizard,
     * sin exigir que el paso actual esté completo.
     */
    public function saveDraftAndExit()
    {
        if (! $this->draftId && ! $this->port_id) {
            $this->addError('port_id', 'Selecciona un puerto antes de poder guardar el borrador.');

            return;
        }

        $this->saveDraft();

        return redirect()->route('bl.index')->with('success', 'Borrador guardado. Puedes continuar editándolo cuando quieras.');
    }

    /**
     * Guarda (o actualiza) el BL como borrador después de cada paso,
     * para no perder el trabajo si el usuario cierra la pestaña.
     */
    protected function saveDraft(): BillOfLading
    {
        $data = [
            'port_id' => $this->port_id,
            'country_origin' => $this->country_origin,
            'shipper_ref' => $this->shipper_ref,
            'bill_of_loading_ref' => $this->bill_of_loading_ref,
            'shipper' => $this->shipper,
            'consignee' => $this->consignee,
            'notify_party' => $this->notify_party,
            'pre_carriage_by' => $this->pre_carriage_by,
            'place_of_receipt' => $this->place_of_receipt,
            'ocean_vessel_voy_no' => $this->ocean_vessel_voy_no,
            'port_of_discharge' => $this->port_of_discharge,
            'place_of_delivery' => $this->place_of_delivery,
            'final_destination' => $this->final_destination,
            'freight_payable_at' => $this->freight_payable_at,
            'number_of_original_bsl' => $this->number_of_original_bsl,
            'temperature_control_instructions' => $this->temperature_control_instructions,
            'freight_type' => $this->freight_type,
            'freight_total' => $this->freight_total,
            'freight_and_charges' => $this->freight_and_charges,
            'excess_value_declaration' => $this->excess_value_declaration,
            'on_board_date' => $this->on_board_date,
            'delivery_agent' => $this->delivery_agent,
        ];

        if ($this->draftId) {
            $bl = BillOfLading::findOrFail($this->draftId);
            $bl->fill($data);
            $bl->updated_by = Auth::id(); // asignación directa: bypasea $fillable a propósito
            $changedFields = array_keys($bl->getDirty());
            $bl->save();

            if (! empty($changedFields)) {
                $labels = array_map(fn ($f) => $this->fieldLabel($f), $changedFields);
                \App\Models\BlAuditLog::log($bl, 'editado', implode(', ', $labels));
            }
        } else {
            $bl = new BillOfLading($data);
            $bl->created_by = Auth::id(); // idem: campo de auditoría, no debe ser masivo-asignable
            $bl->status = 'borrador';
            $bl->save();
            $this->draftId = $bl->id;
            \App\Models\BlAuditLog::log($bl, 'creado');
        }

        $bl->cargoItems()->delete();
        foreach ($this->cargoItems as $i => $item) {
            if (trim($item['description_of_goods'] ?? '') === '') {
                continue;
            }
            $bl->cargoItems()->create($item + ['sort_order' => $i]);
        }

        return $bl;
    }

    /**
     * Paso final: emite el BL (genera el número consecutivo real del puerto).
     * Requiere permiso 'bl.issue'.
     */
    public function issue()
    {
        $this->validate($this->rulesForStep(4));
        abort_unless(Auth::user()->can('bl.issue'), 403);

        $bl = DB::transaction(function () {
            $bl = $this->saveDraft();
            $bl->issue();

            return $bl;
        });

        session()->flash('success', "BL {$bl->bl_number} emitido correctamente.");

        return redirect()->route('bl.show', $bl);
    }

    public function render()
    {
        return view('livewire.bill-of-lading-form', [
            'ports' => Port::orderBy('name')->get(),
        ]);
    }
}