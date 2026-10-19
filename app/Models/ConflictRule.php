<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConflictRule extends Model
{
    protected $fillable = [
        'code', 'name', 'rule_type', 'scope', 'severity', 'config', 'is_active', 'conflict_group_id',
    ];

    protected function casts(): array
    {
        return [
            'config'    => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function conflictGroup(): BelongsTo
    {
        return $this->belongsTo(ConflictGroup::class);
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(ConflictAlert::class, 'conflict_rule_id');
    }
}
