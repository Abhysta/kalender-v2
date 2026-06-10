<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WiAssignment extends Model
{
    protected $fillable = [
        'batch_schedule_id', 'widyaiswara_id', 'start_date', 'end_date', 'notes', 'assigned_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date'   => 'date',
        ];
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(BatchSchedule::class, 'batch_schedule_id');
    }

    public function widyaiswara(): BelongsTo
    {
        return $this->belongsTo(Widyaiswara::class);
    }
}
