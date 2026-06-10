<?php

namespace App\Services;

use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class WorkingDayService
{
    private Collection $holidays;

    public function __construct()
    {
        $this->loadHolidays(null);
    }

    /**
     * Reload holiday list for a specific organizational unit.
     * Loads global holidays (unit_id IS NULL) + unit-specific holidays.
     */
    public function setUnit(?int $unitId): void
    {
        $this->loadHolidays($unitId);
    }

    private function loadHolidays(?int $unitId): void
    {
        $this->holidays = Holiday::where(function ($q) use ($unitId) {
            $q->whereNull('organizational_unit_id');
            if ($unitId !== null) {
                $q->orWhere('organizational_unit_id', $unitId);
            }
        })->pluck('date')->map(fn($d) => $d->format('Y-m-d'));
    }

    public function isHoliday(Carbon $date): bool
    {
        return $this->holidays->contains($date->format('Y-m-d'));
    }

    /**
     * $workDays: 5 = Mon-Fri, 6 = Mon-Sat, 7 = Mon-Sun. Invalid values treated as 5.
     */
    public function isWorkingDay(Carbon $date, int $workDays = 5): bool
    {
        if (! in_array($workDays, [6, 7])) {
            $workDays = 5;
        }
        if ($date->isSunday() && $workDays < 7) {
            return false;
        }
        if ($date->isSaturday() && $workDays < 6) {
            return false;
        }
        return ! $this->isHoliday($date);
    }

    public function addWorkingDays(Carbon $date, int $days, int $workDays = 5): Carbon
    {
        $current   = $date->copy();
        $remaining = $days;

        while ($remaining > 0) {
            $current->addDay();
            if ($this->isWorkingDay($current, $workDays)) {
                $remaining--;
            }
        }

        return $current;
    }

    public function addCalendarDays(Carbon $date, int $days): Carbon
    {
        $current   = $date->copy();
        $remaining = $days;

        while ($remaining > 0) {
            $current->addDay();
            if (! $this->isHoliday($current)) {
                $remaining--;
            }
        }

        return $current;
    }

    public function nextWorkingDay(Carbon $date, int $workDays = 5): Carbon
    {
        $d = $date->copy();
        while (! $this->isWorkingDay($d, $workDays)) {
            $d->addDay();
        }
        return $d;
    }

    public function countWorkingDays(Carbon $start, Carbon $end, int $workDays = 5): int
    {
        $count   = 0;
        $current = $start->copy();
        while ($current->lte($end)) {
            if ($this->isWorkingDay($current, $workDays)) {
                $count++;
            }
            $current->addDay();
        }
        return $count;
    }
}
