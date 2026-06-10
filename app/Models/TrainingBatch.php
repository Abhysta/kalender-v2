<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingBatch extends Model
{
    protected $fillable = [
        'training_template_id', 'organizational_unit_id', 'batch_number',
        'year', 'name', 'start_date', 'end_date', 'participant_count', 'status',
        'has_conflict_alert', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date'         => 'date',
            'end_date'           => 'date',
            'has_conflict_alert' => 'boolean',
        ];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(TrainingTemplate::class, 'training_template_id');
    }

    public function organizationalUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(BatchSchedule::class)->orderBy('sequence');
    }

    public function isGenerated(): bool
    {
        return $this->status !== 'draft';
    }
}
