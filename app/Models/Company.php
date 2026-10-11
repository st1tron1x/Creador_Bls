<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name', 'short_name', 'numbering_scheme', 'fixed_prefix',
        'pdf_template', 'logo_path', 'address', 'phone', 'email', 'active',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function billsOfLading(): HasMany
    {
        return $this->hasMany(BillOfLading::class);
    }

    public function sequences(): HasMany
    {
        return $this->hasMany(BlSequence::class);
    }

    /**
     * Reserva y devuelve el siguiente consecutivo de esta empresa para el $label dado
     * (código de puerto para Trade & Business, o el fixed_prefix para Global), de forma
     * atómica. Debe llamarse dentro de DB::transaction().
     */
    public function reserveNextSequenceFor(string $label): int
    {
        $sequence = BlSequence::firstOrCreate(
            ['company_id' => $this->id, 'label' => $label],
            ['last_number' => 0]
        );
        $sequence = BlSequence::where('id', $sequence->id)->lockForUpdate()->first();

        $sequence->last_number += 1;
        $sequence->save();

        return $sequence->last_number;
    }

    public function formatBlNumber(string $label, int $sequence): string
    {
        $seq = str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
        $separator = $this->numbering_scheme === 'company_based' ? ' ' : '-';

        return $label . $separator . $seq;
    }
}
