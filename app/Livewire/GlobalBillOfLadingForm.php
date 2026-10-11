<?php

namespace App\Livewire;

use App\Models\BillOfLading;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layout')]
class GlobalBillOfLadingForm extends Component
{
    public int $step = 1;
    public int $totalSteps = 5;
    public int $maxStepReached = 1;
    public ?int $draftId = null;

    // Paso 1: partes involucradas
    public string $shipper = '';
    public string $consignee = '';
    public string $notify_party = '';

    // Paso 2: referencias y ruteo
    public string $domestic_routing = '';
    public string $export_references = '';
    public string $cargo_delivery_contact = '';
    public string $forwarding_agent_references = '';

    // Paso 3: transporte
    public string $pre_carriage_by = '';
    public string $place_of_receipt = '';
    public string $exporting_carrier = '';
    public string $port_of_loading_text = '';
    public string $on_carriage_by = '';
    public string $number_of_original_bsl = '';
    public string $port_of_discharge = '';
    public string $place_of_delivery = '';
    public string $final_destination = '';

    // Paso 4: carga
    public array $cargoItems = [];
    public string $net_weight = '';

    // Paso 5: cargos
    public string $freight_type = 'prepaid';
    public ?float $freight_total = null;
    public string $freight_and_charges = '';
    public string $excess_value_declaration = '';
    public ?float $total_charges = null;

    public function mount(?BillOfLading $billOfLading = null): void
    {
        $this->cargoItems = [$this->emptyCargoItem()];

        if ($billOfLading) {
            abort_unless($billOfLading->status === 'borrador', 403, 'Solo se pueden editar BL en borrador.');
            abort_unless($billOfLading->company_id === Auth::user()->activeCompany()->id, 403);

            $this->maxStepReached = $this->totalSteps;
            $this->draftId = $billOfLading->id;
            $this->shipper = $billOfLading->shipper ?? '';
            $this->consignee = $billOfLading->consignee ?? '';
            $this->notify_party = $billOfLading->notify_party ?? '';
            $this->domestic_routing = $billOfLading->domestic_routing ?? '';
            $this->export_references = $billOfLading->export_references ?? '';
            $this->cargo_delivery_contact = $billOfLading->cargo_delivery_contact ?? '';
            $this->forwarding_agent_references = $billOfLading->forwarding_agent_references ?? '';
            $this->pre_carriage_by = $billOfLading->pre_carriage_by ?? '';
            $this->place_of_receipt = $billOfLading->place_of_receipt ?? '';
            $this->exporting_carrier = $billOfLading->exporting_carrier ?? '';
            $this->port_of_loading_text = $billOfLading->port_of_loading_text ?? '';
            $this->on_carriage_by = $billOfLading->on_carriage_by ?? '';
            $this->number_of_original_bsl = $billOfLading->number_of_original_bsl ?? '';
            $this->port_of_discharge = $billOfLading->port_of_discharge ?? '';
            $this->place_of_delivery = $billOfLading->place_of_delivery ?? '';
            $this->final_destination = $billOfLading->final_destination ?? '';
            $this->net_weight = $billOfLading->net_weight ?? '';
            $this->freight_type = $billOfLading->freight_type ?? 'prepaid';
            $this->freight_total = $billOfLading->freight_total;
            $this->freight_and_charges = $billOfLading->freight_and_charges ?? '';
            $this->excess_value_declaration = $billOfLading->excess_value_declaration ?? '';
            $this->total_charges = $billOfLading->total_charges;

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
            'marks_and_numbers' => '', 'container_seal_no' => '', 'no_of_pkgs' => '',
            'description_of_goods' => '', 'gross_weight' => '', 'measurement_cbm' => '',
        ];
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
                'shipper' => 'required|string',
                'consignee' => 'required|string',
                'notify_party' => 'nullable|string',
            ],
            2 => [
                'domestic_routing' => 'nullable|string',
                'export_references' => 'nullable|string',
                'cargo_delivery_contact' => 'nullable|string',
                'forwarding_agent_references' => 'nullable|string',
            ],
            3 => [
                'pre_carriage_by' => 'nullable|string|max:255',
                'place_of_receipt' => 'nullable|string|max:255',
                'exporting_carrier' => 'nullable|string|max:255',
                'port_of_loading_text' => 'required|string|max:255',
                'on_carriage_by' => 'nullable|string|max:255',
                'number_of_original_bsl' => 'nullable|string|max:50',
                'port_of_discharge' => 'required|string|max:255',
                'place_of_delivery' => 'nullable|string|max:255',
                'final_destination' => 'nullable|string|max:255',
            ],
            4 => [
                'cargoItems' => 'required|array|min:1',
                'cargoItems.*.description_of_goods' => 'required|string',
                'net_weight' => 'nullable|string|max:100',
            ],
            5 => [
                'freight_type' => 'required|in:prepaid,collect',
                'freight_total' => 'nullable|numeric|min:0',
                'freight_and_charges' => 'nullable|string',
                'excess_value_declaration' => 'nullable|string',
                'total_charges' => 'nullable|numeric|min:0',
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

    public function goToStep(int $step): void
    {
        $target = max(1, min($step, $this->totalSteps));
        if ($target > $this->maxStepReached) {
            return;
        }
        if (! $this->draftId && trim($this->shipper) === '') {
            return;
        }
        $this->saveDraft();
        $this->step = $target;
    }

    public function saveDraftAndExit()
    {
        if (! $this->draftId && trim($this->shipper) === '') {
            $this->addError('shipper', 'Escribe al menos el Shipper antes de guardar el borrador.');

            return;
        }
        $this->saveDraft();

        return redirect()->route('bl.index')->with('success', 'Borrador guardado.');
    }

    protected function saveDraft(): BillOfLading
    {
        $data = [
            'shipper' => $this->shipper,
            'consignee' => $this->consignee,
            'notify_party' => $this->notify_party,
            'domestic_routing' => $this->domestic_routing,
            'export_references' => $this->export_references,
            'cargo_delivery_contact' => $this->cargo_delivery_contact,
            'forwarding_agent_references' => $this->forwarding_agent_references,
            'pre_carriage_by' => $this->pre_carriage_by,
            'place_of_receipt' => $this->place_of_receipt,
            'exporting_carrier' => $this->exporting_carrier,
            'port_of_loading_text' => $this->port_of_loading_text,
            'on_carriage_by' => $this->on_carriage_by,
            'number_of_original_bsl' => $this->number_of_original_bsl,
            'port_of_discharge' => $this->port_of_discharge,
            'place_of_delivery' => $this->place_of_delivery,
            'final_destination' => $this->final_destination,
            'net_weight' => $this->net_weight,
            'freight_type' => $this->freight_type,
            'freight_total' => $this->freight_total,
            'freight_and_charges' => $this->freight_and_charges,
            'excess_value_declaration' => $this->excess_value_declaration,
            'total_charges' => $this->total_charges,
        ];

        if ($this->draftId) {
            $bl = BillOfLading::findOrFail($this->draftId);
            $bl->fill($data);
            $bl->updated_by = Auth::id();
            $bl->save();
        } else {
            $bl = new BillOfLading($data);
            $bl->created_by = Auth::id();
            $bl->company_id = Auth::user()->activeCompany()->id;
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

    public function issue()
    {
        $this->validate($this->rulesForStep(5));
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
        return view('livewire.global-bill-of-lading-form');
    }
}
