<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Port extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'last_bl_number', 'active'];

    public function billsOfLading(): HasMany
    {
        return $this->hasMany(BillOfLading::class);
    }

    /**
     * Reserva y devuelve el siguiente consecutivo de este puerto de forma atómica.
     * Debe llamarse dentro de DB::transaction() por quien lo use (ver BillOfLading::issueFor()).
     */
    public function reserveNextSequence(): int
    {
        $port = self::where('id', $this->id)->lockForUpdate()->first();

        $port->last_bl_number += 1;
        $port->save();

        $this->last_bl_number = $port->last_bl_number;

        return $port->last_bl_number;
    }

    public function formatBlNumber(int $sequence): string
    {
        return $this->code . '-' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
