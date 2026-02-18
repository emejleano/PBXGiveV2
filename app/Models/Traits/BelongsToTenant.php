<?php

namespace App\Models\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait BelongsToTenant
 * otomatis menyaring data agar user hanya bisa melihat data
 * milik tenant mereka sendiri, tidak bisa mengintip tenant sebelah.
 * Tambahkan trait ini ke semua model yang memiliki tenant_id.
 * Secara otomatis akan:
 * - Filter query berdasarkan tenant_id aktif
 * - Set tenant_id saat membuat record baru
 */
trait BelongsToTenant
{
    /**
     * Boot the trait.
     */
    protected static function bootBelongsToTenant(): void
    {
        // Auto-scope query ke tenant aktif
        static::addGlobalScope('tenant', function (Builder $builder) {
            if ($tenantId = request()->header('X-Tenant-ID')) {
                $builder->where($builder->getModel()->getTable() . '.tenant_id', $tenantId);
            }
        });

        // Auto-set tenant_id saat create
        static::creating(function ($model) {
            if (empty($model->tenant_id) && $tenantId = request()->header('X-Tenant-ID')) {
                $model->tenant_id = $tenantId;
            }
        });
    }

    /**
     * Relasi ke Tenant.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Scope untuk filter berdasarkan tenant tertentu.
     */
    public function scopeForTenant(Builder $query, int $tenantId): Builder
    {
        return $query->withoutGlobalScope('tenant')->where('tenant_id', $tenantId);
    }

    /**
     * Scope untuk skip tenant filter (superadmin).
     */
    public function scopeWithoutTenantScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope('tenant');
    }
}
