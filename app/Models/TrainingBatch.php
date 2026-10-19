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
        'annual_calendar_activity_id', 'override_start_date',
    ];

    protected function casts(): array
    {
        return [
            'start_date'           => 'date',
            'end_date'             => 'date',
            'has_conflict_alert'   => 'boolean',
            'override_start_date'  => 'date',
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

    public function annualCalendarActivity(): BelongsTo
    {
        return $this->belongsTo(AnnualCalendarActivity::class);
    }

    public function hasDateOverride(): bool
    {
        return $this->override_start_date !== null;
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
