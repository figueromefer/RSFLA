<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingActivity extends Model
{
    use HasFactory;

    public const TYPE_BROADCAST_EMAIL = 'broadcast_email';
    public const TYPE_CAMPAIGN = 'campaign';
    public const TYPE_SOCIAL_POST = 'social_post';
    public const TYPE_LISTING_UPDATE = 'listing_update';
    public const TYPE_FLYER = 'flyer';
    public const TYPE_SIGNAGE = 'signage';
    public const TYPE_BROKER_OUTREACH = 'broker_outreach';
    public const TYPE_OTHER = 'other';

    public const TYPES = [
        self::TYPE_BROADCAST_EMAIL,
        self::TYPE_CAMPAIGN,
        self::TYPE_SOCIAL_POST,
        self::TYPE_LISTING_UPDATE,
        self::TYPE_FLYER,
        self::TYPE_SIGNAGE,
        self::TYPE_BROKER_OUTREACH,
        self::TYPE_OTHER,
    ];

    protected $fillable = [
        'property_id',
        'user_id',
        'type',
        'title',
        'description',
        'activity_date',
        'metric_label',
        'metric_value',
        'url',
        'visible_to_client',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
            'visible_to_client' => 'boolean',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeVisibleToClient($query)
    {
        return $query->where('visible_to_client', true);
    }

    public static function typeLabel(string $type): string
    {
        return str($type)->replace('_', ' ')->title()->toString();
    }
}
