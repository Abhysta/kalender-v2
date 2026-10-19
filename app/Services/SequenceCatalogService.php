<?php

namespace App\Services;

use App\Models\ConflictGroup;
use App\Models\SequenceCatalog;
use Illuminate\Database\UniqueConstraintViolationException;

class SequenceCatalogService
{
    public function normalize(string $name): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/', ' ', $name)));
    }

    /**
     * Deterministically resolve an imported activity name to its canonical catalog entry.
     * Exact normalized-name match only — never fuzzy, never merges on similarity.
     * Auto-creates a new canonical identity when the name is genuinely new.
     *
     * Conflict-group resolution: an explicit code from the import row always wins and is
     * learned as the catalog entry's default, so future imports that leave the column
     * blank resolve to the same group automatically (no re-typing, no silent re-guessing).
     */
    public function resolve(string $rawName, ?string $explicitConflictGroupCode): array
    {
        $normalized = $this->normalize($rawName);

        $catalog = SequenceCatalog::where('normalized_name', $normalized)->first();
        if (! $catalog) {
            try {
                $catalog = SequenceCatalog::create([
                    'normalized_name' => $normalized,
                    'display_name'    => trim($rawName),
                ]);
            } catch (UniqueConstraintViolationException) {
                // Lost a race to another concurrent import — reuse the winner, no duplicate.
                $catalog = SequenceCatalog::where('normalized_name', $normalized)->first();
            }
        }

        $explicitCode = $explicitConflictGroupCode !== null ? trim($explicitConflictGroupCode) : '';
        $group        = null;

        if ($explicitCode !== '') {
            $group = ConflictGroup::where('code', $explicitCode)->first();
            if (! $group) {
                try {
                    $group = ConflictGroup::create(['code' => $explicitCode, 'name' => $explicitCode, 'is_active' => true]);
                } catch (UniqueConstraintViolationException) {
                    $group = ConflictGroup::where('code', $explicitCode)->first();
                }
            }

            if ($catalog->default_conflict_group_id === null) {
                $catalog->update(['default_conflict_group_id' => $group->id]);
            }
        } elseif ($catalog->default_conflict_group_id) {
            $group = $catalog->defaultConflictGroup;
        }

        return ['catalog' => $catalog, 'group' => $group];
    }
}
