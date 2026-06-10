<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'organizational_unit_id',
        'name',
        'email',
        'password',
        'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    public function organizationalUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isUnitManager(): bool
    {
        return $this->hasRole('unit_manager');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Apply 3-tier ownership filter to a query.
     * super_admin  → no filter
     * unit_manager → filter by organizational_unit_id
     * admin        → filter by organizational_unit_id + created_by
     */
    public function applyOwnership(Builder $query, string $createdByColumn = 'created_by'): Builder
    {
        if ($this->isSuperAdmin()) {
            return $query;
        }

        $query->where('organizational_unit_id', $this->organizational_unit_id);

        if ($this->isAdmin()) {
            $query->where($createdByColumn, $this->id);
        }

        return $query;
    }
}
