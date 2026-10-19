<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnnualCalendarActivity extends Model
{
    protected $fillable = [
        'annual_calendar_id', 'training_template_id', 'name',
        'planned_start_date', 'planned_end_date', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'planned_start_date' => 'date',
            'planned_end_date'   => 'date',
        ];
    }

    public function calendar(): BelongsTo
    {
        return $this->belongsTo(AnnualCalendar::class, 'annual_calendar_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(TrainingTemplate::class, 'training_template_id');
    }

    public function trainingBatches(): HasMany
    {
        return $this->hasMany(TrainingBatch::class);
    }

    public function hasPlannedDate(): bool
    {
        return $this->planned_start_date !== null;
    }
}
