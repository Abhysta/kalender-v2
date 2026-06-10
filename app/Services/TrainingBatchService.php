<?php

namespace App\Services;

use App\Models\TrainingBatch;
use App\Models\User;

class TrainingBatchService
{
    /**
     * Create a new training batch. Enforces unit ownership for non-super_admin users.
     */
    public function create(array $data, User $user): TrainingBatch
    {
        if (! $user->isSuperAdmin()) {
            $data['organizational_unit_id'] = $user->organizational_unit_id;
        }

        $data['status']     = 'draft';
        $data['created_by'] = $user->id;
        $data['updated_by'] = $user->id;

        return TrainingBatch::create($data);
    }
}
