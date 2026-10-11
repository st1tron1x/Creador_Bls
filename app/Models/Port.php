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
}
