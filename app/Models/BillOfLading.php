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
        'port_id', 'country_origin', 'shipper_ref', 'bill_of_loading_ref', 'shipper', 'consignee',
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

    public function voidRequests(): HasMany
    {
        return $this->hasMany(BlVoidRequest::class)->latest('requested_at');
    }

    public function pendingVoidRequest(): ?BlVoidRequest
    {
        return $this->voidRequests()->where('status', 'pendiente')->first();
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(BlAuditLog::class)->latest('created_at');
    }

    /**
     * Emite el BL: reserva el consecutivo del puerto de forma atómica,
     * arma el número formateado y cambia el estado a "emitido".
     * Requiere que el usuario tenga permiso 'bl.issue' (verificado en el controller/policy).
     */
    public function issue(): self
    {
        return DB::transaction(function () {
            $port = $this->port()->lockForUpdate()->first();
            $sequence = $port->reserveNextSequence();

            $this->sequence_number = $sequence;
            $this->bl_number = $port->formatBlNumber($sequence);
            $this->status = 'emitido';
            $this->issued_at = now();
            $this->save();

            return $this;
        });
    }

    /**
     * Aplica la anulación ya APROBADA (llamado solo desde el flujo de aprobación,
     * nunca directamente por un controller sin pasar por BlVoidRequest).
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
}
