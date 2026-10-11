<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlSequence extends Model
{
    protected $fillable = ['company_id', 'label', 'last_number'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
