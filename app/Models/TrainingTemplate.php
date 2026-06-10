<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingTemplate extends Model
{
    protected $fillable = [
        'organizational_unit_id', 'code', 'name', 'description', 'is_active',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function organizationalUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class);
    }

    public function phases(): HasMany
    {
        return $this->hasMany(TemplatePhase::class)->orderBy('sequence');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(TrainingBatch::class);
    }
}
