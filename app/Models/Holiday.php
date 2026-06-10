<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Holiday extends Model
{
    protected $fillable = ['date', 'name', 'is_national', 'organizational_unit_id'];

    protected function casts(): array
    {
        return [
            'date'        => 'date',
            'is_national' => 'boolean',
        ];
    }

    public function organizationalUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class);
    }

    public function isGlobal(): bool
    {
        return $this->organizational_unit_id === null;
    }
}
