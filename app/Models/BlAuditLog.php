<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class BlAuditLog extends Model
{
    public $timestamps = false; // solo usamos created_at, manejado manualmente

    protected $fillable = ['bill_of_lading_id', 'user_id', 'action', 'detail'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function billOfLading(): BelongsTo
    {
        return $this->belongsTo(BillOfLading::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(BillOfLading $bl, string $action, ?string $detail = null): void
    {
        static::create([
            'bill_of_lading_id' => $bl->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'detail' => $detail,
            'created_at' => now(),
        ]);
    }

    public function labelForAction(): string
    {
        return match ($this->action) {
            'creado' => 'Creó el BL',
            'editado' => 'Editó',
            'visto' => 'Consultó el BL',
            'descargado' => 'Descargó el PDF',
            'anulacion_solicitada' => 'Solicitó anulación',
            'anulacion_aprobada' => 'Aprobó la anulación',
            'anulacion_rechazada' => 'Rechazó la anulación',
            default => $this->action,
        };
    }
}