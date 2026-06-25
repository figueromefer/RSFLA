<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'name',
        'slug',
        'market',
        'street_address',
        'city',
        'state',
        'hero_image',
        'report_title',
        'postal_code',
        'property_type',
        'unit_count',
        'owner_name',
        'status',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'unit_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['role', 'receives_reports'])
            ->withTimestamps();
    }

    public function teamMembers(): BelongsToMany
    {
        return $this->belongsToMany(TeamMember::class)->withTimestamps();
    }

    public function prospects(): HasMany
    {
        return $this->hasMany(Prospect::class);
    }

    public function visibleProspects(): HasMany
    {
        return $this->prospects()->visibleToClient();
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ProspectActivity::class);
    }

    public function marketingActivities(): HasMany
    {
        return $this->hasMany(MarketingActivity::class)->latest('activity_date');
    }

    public function visibleMarketingActivities(): HasMany
    {
        return $this->marketingActivities()->visibleToClient();
    }

    public function links(): HasMany
    {
        return $this->hasMany(PropertyLink::class)->orderBy('sort_order');
    }

    public function visibleLinks(): HasMany
    {
        return $this->links()->visibleToClient();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function syncStatusFromActiveFlag(): void
    {
        $this->status = $this->is_active ? self::STATUS_ACTIVE : self::STATUS_INACTIVE;
    }
}
