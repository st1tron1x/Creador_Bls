<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlVoidRequest extends Model
{
    protected $fillable = [
        'bill_of_lading_id', 'requested_by', 'requested_reason', 'requested_at',
        'status', 'resolved_by', 'resolved_at', 'resolution_note',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function billOfLading(): BelongsTo
    {
        return $this->belongsTo(BillOfLading::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
