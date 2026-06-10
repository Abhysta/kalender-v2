<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplatePhase extends Model
{
    protected $fillable = [
        'training_template_id', 'name', 'sequence', 'duration', 'duration_unit',
        'offset_days', 'day_type', 'work_days', 'activity_type', 'conflict_group',
        'is_alert', 'alert_type', 'can_manual_edit', 'color',
    ];

    protected function casts(): array
    {
        return [
            'work_days'       => 'integer',
            'is_alert'        => 'boolean',
            'can_manual_edit' => 'boolean',
        ];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(TrainingTemplate::class, 'training_template_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(BatchSchedule::class);
    }
}
