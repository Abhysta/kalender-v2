<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConflictGroup extends Model
{
    protected $fillable = ['code', 'name', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function sequenceCatalogEntries(): HasMany
    {
        return $this->hasMany(SequenceCatalog::class, 'default_conflict_group_id');
    }

    public function phases(): HasMany
    {
        return $this->hasMany(TemplatePhase::class, 'conflict_group_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(BatchSchedule::class, 'conflict_group_id');
    }

    public function rules(): HasMany
    {
        return $this->hasMany(ConflictRule::class, 'conflict_group_id');
    }
}
