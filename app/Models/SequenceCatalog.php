<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SequenceCatalog extends Model
{
    protected $table = 'sequence_catalog';

    protected $fillable = ['normalized_name', 'display_name', 'default_conflict_group_id'];

    public function defaultConflictGroup(): BelongsTo
    {
        return $this->belongsTo(ConflictGroup::class, 'default_conflict_group_id');
    }

    public function phases(): HasMany
    {
        return $this->hasMany(TemplatePhase::class, 'sequence_catalog_id');
    }
}
