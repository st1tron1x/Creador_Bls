<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class BillOfLading extends Model
{
    use HasFactory;

    protected $table = 'bills_of_lading';

    protected $fillable = [
        'port_id', 'bill_of_loading_ref', 'port_of_loading_text', 'domestic_routing', 'export_references',
        'cargo_delivery_contact', 'forwarding_agent_references', 'exporting_carrier',
        'on_carriage_by', 'net_weight', 'total_charges', 'country_origin', 'shipper_ref', 'shipper', 'consignee',
        'notify_party', 'pre_carriage_by', 'place_of_receipt', 'ocean_vessel_voy_no',
        'port_of_discharge', 'place_of_delivery', 'final_destination',
        'freight_payable_at', 'number_of_original_bsl', 'temperature_control_instructions',
        'freight_type', 'freight_total', 'freight_and_charges', 'excess_value_declaration',
        'on_board_date', 'delivery_agent',
    ];

    protected $casts = [
        'on_board_date' => 'date',
        'issued_at' => 'datetime',
        'voided_at' => 'datetime',
        'freight_total' => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function port(): BelongsTo
    {
        return $this->belongsTo(Port::class);
    }

    public function cargoItems(): HasMany
    {
        return $this->hasMany(BlCargoItem::class)->orderBy('sort_order');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function voidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    /**
     * Emite el BL: reserva el consecutivo del puerto de forma atómica,
     * arma el número formateado y cambia el estado a "emitido".
     * Requiere que el usuario tenga permiso 'bl.issue' (verificado en el controller/policy).
     */
    public function issue(): self
    {
        return DB::transaction(function () {
            $company = Company::where('id', $this->company_id)->lockForUpdate()->firstOrFail();

            if ($company->numbering_scheme === 'port_based') {
                $port = $this->port;
                $label = $port->code;
            } else {
                $label = $company->fixed_prefix;
            }

            $sequence = $company->reserveNextSequenceFor($label);

            $this->sequence_number = $sequence;
            $this->bl_number = $company->formatBlNumber($label, $sequence);
            $this->status = 'emitido';
            $this->issued_at = now();
            $this->save();

            return $this;
        });
    }

    public function voidRequests(): HasMany
    {
        return $this->hasMany(BlVoidRequest::class)->latest('id');
    }

    public function pendingVoidRequest(): ?BlVoidRequest
    {
        return $this->voidRequests()->where('status', 'pendiente')->first();
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(BlAuditLog::class)->latest('id');
    }

    /**
     * Marca el BL como anulado (llamado al aprobarse una solicitud de anulación).
     */
    public function finalizeVoid(User $approver, string $reason): self
    {
        $this->status = 'anulado';
        $this->voided_by = $approver->id;
        $this->voided_at = now();
        $this->voided_reason = $reason;
        $this->save();

        return $this;
    }

    public function void(User $user, string $reason): self
    {
        return $this->finalizeVoid($user, $reason);
    }
}
