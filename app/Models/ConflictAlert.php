<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConflictAlert extends Model
{
    protected $fillable = [
        'conflict_rule_id', 'rule_type', 'batch_schedule_id', 'related_batch_schedule_id',
        'conflict_group_id', 'status', 'details', 'dedup_key', 'detected_at',
        'resolved_by', 'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'detected_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(ConflictRule::class, 'conflict_rule_id');
    }

    public function scheduleItem(): BelongsTo
    {
        return $this->belongsTo(BatchSchedule::class, 'batch_schedule_id');
    }

    public function relatedScheduleItem(): BelongsTo
    {
        return $this->belongsTo(BatchSchedule::class, 'related_batch_schedule_id');
    }

    public function conflictGroup(): BelongsTo
    {
        return $this->belongsTo(ConflictGroup::class);
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
