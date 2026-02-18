<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'category_id',
        'created_by',
        'title',
        'slug',
        'short_description',
        'description',
        'cover_image',
        'goal_amount',
        'current_amount',
        'start_date',
        'end_date',
        'status',
        'location',
        'meta',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'goal_amount'    => 'decimal:2',
            'current_amount' => 'decimal:2',
            'start_date'     => 'date',
            'end_date'       => 'date',
            'meta'           => 'array',
            'published_at'   => 'datetime',
        ];
    }

    // ── Relations ──

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function updates(): HasMany
    {
        return $this->hasMany(CampaignUpdate::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }
}
