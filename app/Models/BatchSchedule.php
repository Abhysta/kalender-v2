<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BatchSchedule extends Model
{
    protected $fillable = [
        'training_batch_id', 'template_phase_id', 'name', 'start_date', 'end_date',
        'sequence', 'activity_type', 'conflict_group', 'color',
        'is_locked', 'is_manual', 'is_alert', 'notes', 'generated_by',
        'is_anchor', 'recalculation_source', 'recalculated_at',
    ];

    protected function casts(): array
    {
        return [
            'start_date'      => 'date',
            'end_date'        => 'date',
            'is_locked'       => 'boolean',
            'is_manual'       => 'boolean',
            'is_alert'        => 'boolean',
            'is_anchor'       => 'boolean',
            'recalculated_at' => 'datetime',
        ];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(TrainingBatch::class, 'training_batch_id');
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(TemplatePhase::class, 'template_phase_id');
    }

    public function wiAssignments(): HasMany
    {
        return $this->hasMany(WiAssignment::class);
    }
}
