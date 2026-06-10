<?php

namespace App\Services;

use App\Models\Holiday;

class HolidayService
{
    /**
     * Create a holiday scoped to a unit (null = global).
     * Returns null if date already exists for that scope.
     */
    public function create(string $date, string $name, bool $isNational, ?int $unitId): ?Holiday
    {
        if (Holiday::where('date', $date)->where('organizational_unit_id', $unitId)->exists()) {
            return null;
        }

        return Holiday::create([
            'date'                   => $date,
            'name'                   => $name,
            'is_national'            => $isNational,
            'organizational_unit_id' => $unitId,
        ]);
    }

    /**
     * Import rows (each: date, name, is_national). Returns ['inserted', 'skipped'].
     */
    public function importRows(array $rows, ?int $unitId): array
    {
        $inserted = $skipped = 0;

        foreach ($rows as $row) {
            if (empty($row['date']) || empty($row['name'])) { $skipped++; continue; }

            $date = date('Y-m-d', strtotime($row['date']));
            if (! $date || $date === '1970-01-01') { $skipped++; continue; }

            $isNational = in_array(strtolower($row['is_national'] ?? '1'), ['1', 'true', 'yes', 'ya']);

            Holiday::firstOrCreate(
                ['date' => $date, 'organizational_unit_id' => $unitId],
                ['name' => trim($row['name']), 'is_national' => $isNational]
            );
            $inserted++;
        }

        return compact('inserted', 'skipped');
    }
}
