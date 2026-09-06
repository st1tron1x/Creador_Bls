<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlCargoItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_of_lading_id', 'sort_order', 'marks_and_numbers',
        'container_seal_no', 'no_of_pkgs', 'description_of_goods',
        'gross_weight', 'measurement_cbm',
    ];

    public function billOfLading(): BelongsTo
    {
        return $this->belongsTo(BillOfLading::class);
    }
}
